<?php

namespace App\Http\Livewire\Components\Restructure;

use App\Models\LOANAPPLICATION;
use App\TIRAClient\Scripts\Classes\EsbClient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Restructure proposal calculator (bank-initiated, LOAN_RESTRUCTURE_REQUEST_FSP).
 *
 * The figures come from the ESS gateway endpoint restructure/calculate, which runs the same rules as the
 * restructure affordability calculation (CBS principal balance, product limits, loan calculator, processing fee)
 * without sending anything to ESS. Sending uses the existing restructure endpoint with the calculated tenure,
 * so ESS receives exactly the terms shown on screen.
 */
class Calculator extends Component
{
    public $loanId;
    public $mode = 'tenure';      // tenure | installment
    public $tenure;
    public $installment;
    public $reason = '';

    public $snapshot = [];        // current loan figures (balance, limits)
    public $result = null;        // calculated terms
    public $error = null;
    public $calculatedFor = null; // inputs the result belongs to

    public function mount($loan)
    {
        $this->loanId = $loan->ID;
        $this->loadSnapshot();
    }

    /** Current balance and limits: a call with no tenure / instalment returns them without terms. */
    public function loadSnapshot()
    {
        $data = $this->callGateway(null, 0);
        if ($data !== null) {
            $this->snapshot = $data;
            $desc = $data['statusDescription'] ?? '';
            if (stripos($desc, 'provide the new tenure') === false && ($data['statusCode'] ?? 400) != 200) {
                $this->error = $desc;
            }
        }
    }

    public function updated($field)
    {
        if (in_array($field, ['mode', 'tenure', 'installment'])) {
            $this->result = null;
            $this->error = null;
        }
    }

    public function setMode($mode)
    {
        $this->mode = $mode === 'installment' ? 'installment' : 'tenure';
        $this->result = null;
        $this->error = null;
    }

    public function quickTenure($months)
    {
        $this->mode = 'tenure';
        $this->tenure = (int) $months;
        $this->calculate();
    }

    public function calculate()
    {
        $this->error = null;
        $this->result = null;
        if ($this->mode === 'tenure') {
            $this->validate(['tenure' => 'required|integer|min:1|max:600'], [], ['tenure' => 'new tenure']);
            $data = $this->callGateway((int) $this->tenure, 0);
        } else {
            $this->validate(['installment' => 'required|numeric|min:1'], [], ['installment' => 'new monthly instalment']);
            $data = $this->callGateway(null, (float) $this->installment);
        }
        if ($data === null) {
            return;
        }
        $this->snapshot = array_merge($this->snapshot, array_filter($data, fn ($v) => $v !== null));
        if (($data['statusCode'] ?? 400) == 200) {
            $this->result = $data;
            $this->calculatedFor = $this->mode.':'.($this->mode === 'tenure' ? $this->tenure : $this->installment);
        } else {
            $this->error = $data['statusDescription'] ?? 'The terms could not be calculated';
            // Show the figures even when a rule fails, so the officer can see why
            if (isset($data['newInstalment'])) {
                $this->result = $data + ['failed' => true];
            }
        }
    }

    public function propose()
    {
        if (Auth::user()->role != 'Credit Officer') {
            $this->error = 'Only a Credit Officer can send a restructure proposal.';
            return;
        }
        if (!$this->result || !empty($this->result['failed'])) {
            $this->error = 'Calculate valid terms before sending the proposal.';
            return;
        }
        $this->validate(['reason' => 'required|string|min:5|max:255'], [], ['reason' => 'reason']);

        $loan = LOANAPPLICATION::find($this->loanId);
        $profile = 'v1/properties/staff/registration/profile/'.$this->loanId;
        if ($loan == null || $loan->TRANSACTION_STATUS !== 'Disbursed') {
            $this->error = 'Only a disbursed loan can be restructured.';
            return;
        }
        // Send the calculated tenure so ESS receives exactly the terms shown on screen
        $body = json_encode([
            'loanApplication' => $loan->LOAN_ID,
            'tenure'          => (int) $this->result['newTenure'],
            'installment'     => 0,
            'reason'          => $this->reason,
        ]);
        try {
            $response = EsbClient::SendesbRequestbody($body, 'restructure');
        } catch (\Throwable $e) {
            $this->error = 'The ESS gateway is not reachable: '.$e->getMessage();
            return;
        }
        if ($response != null && isset($response->statusCode) && $response->statusCode === 200) {
            session()->flash('success', '<b>SUCCESS</b>, '.e($response->statusDescription));
            return redirect($profile);
        }
        $this->error = ($response != null && isset($response->statusDescription)) ? $response->statusDescription : 'No response from the ESS gateway';
    }

    protected function callGateway($tenure, $installment)
    {
        $loan = LOANAPPLICATION::find($this->loanId);
        if ($loan == null) {
            $this->error = 'Loan not found';
            return null;
        }
        $body = json_encode([
            'loanApplication' => $loan->LOAN_ID,
            'tenure'          => $tenure,
            'installment'     => $installment,
            'reason'          => 'calculation',
        ]);
        try {
            $response = EsbClient::SendesbRequestbody($body, 'restructure/calculate');
        } catch (\Throwable $e) {
            $this->error = 'The ESS gateway is not reachable: '.$e->getMessage();
            return null;
        }
        if ($response === null) {
            $this->error = 'No response from the ESS gateway (is the gateway updated with restructure/calculate?)';
            return null;
        }
        return json_decode(json_encode($response), true);
    }

    public function render()
    {
        $loan = LOANAPPLICATION::find($this->loanId);
        return view('livewire.components.restructure.calculator', [
            'loan'       => $loan,
            'instalment' => \App\Http\Livewire\Components\Vehicle\Details::instalment($loan),
            'canSend'    => Auth::user()->role == 'Credit Officer',
        ]);
    }
}

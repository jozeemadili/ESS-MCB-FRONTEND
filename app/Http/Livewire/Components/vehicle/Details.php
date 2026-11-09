<?php

namespace App\Http\Livewire\Components\Vehicle;

use App\Models\LOANAPPLICATION;
use App\TIRAClient\Scripts\Classes\EsbClient;
use Livewire\Component;

class Details extends Component
{
    public $vehicle;
    public $approval_datils;
    public $status;

    public function render()
    {
        // Restructuring: a restructure row points to the loan it replaces (ORIGINAL_LOAN_ID = original LOAN_ID)
        $original_loan = null;
        $restructures = collect();
        if ($this->vehicle->LOAN_TYPE === 'RESTRUCTURING' && !empty($this->vehicle->ORIGINAL_LOAN_ID)) {
            $original_loan = LOANAPPLICATION::where('LOAN_ID', $this->vehicle->ORIGINAL_LOAN_ID)->first();
        }
        if (!empty($this->vehicle->LOAN_ID)) {
            $restructures = LOANAPPLICATION::where('ORIGINAL_LOAN_ID', $this->vehicle->LOAN_ID)
                ->where('LOAN_TYPE', 'RESTRUCTURING')->orderBy('ID', 'desc')->get();
        }
        return view('livewire.components.vehicle.details', [
            'original_loan' => $original_loan,
            'restructures'  => $restructures,
        ]);
    }

    /**
     * Monthly instalment of a loan. MONTHLY_DEDUCTION holds the instalment for loans booked since Nov 2026 but the
     * employee's total deductions for older loans, so it is used only when it is not above TOTAL_AMOUNT_TO_PAY / TENURE.
     */
    public static function instalment($loan)
    {
        if ($loan == null) {
            return 0;
        }
        $monthly = (float) $loan->MONTHLY_DEDUCTION;
        if (empty($loan->TOTAL_AMOUNT_TO_PAY) || empty($loan->TENURE)) {
            return $monthly;
        }
        $totalOverTenure = (float) $loan->TOTAL_AMOUNT_TO_PAY / (int) $loan->TENURE;
        return ($monthly > 0 && $monthly <= $totalOverTenure + 1) ? $monthly : $totalOverTenure;
    }
    
    public function loanInitialApproval()
    {
        dd('helo');
        
    }
    public function liqudateLoanAccount($loanid)
    {
            $endPoint="liquidate/$loanid";
            $response = EsbClient::SendesbRequesturl($endPoint); 
            $status = $response->status;
            $statusDescription = $response->statusDescription;
            if ($response->statusCode === 200) 
                {
                    return to_route('staff-profile',['id' => $loanid])->with('success', ' <b>SUCCESS</b>, '.$statusDescription.' ');
                }
            else
                {
                  return to_route('staff-profile',['id' => $loanid])->with('success', ' <b>FAILED</b>, '.$statusDescription.' ');
                }
    }
}

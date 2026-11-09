<div class="rc">
@php
    $money = fn ($x) => $x === null || $x === '' ? '—' : number_format((float) $x, 2, '.', ',');
    $s = $snapshot;
    $minT = (int) ($s['minimumTenure'] ?? 1);
    $maxT = (int) ($s['maximumTenure'] ?? 96);
    if (!empty($s['retirementMonths']) && (int) $s['retirementMonths'] > 0) { $maxT = min($maxT, (int) $s['retirementMonths']); }
    $quick = [];
    for ($m = 12; $m <= $maxT; $m += 12) { if ($m >= $minT) $quick[] = $m; }
    if ($maxT >= $minT && !in_array($maxT, $quick)) { $quick[] = $maxT; }
    $r = $result;
    $currentInst = $s['currentInstalment'] ?? $instalment;
    $diff = ($r && isset($r['newInstalment'])) ? (float) $r['newInstalment'] - (float) $currentInst : null;
    $name = strtoupper(trim(preg_replace('/\s+/', ' ', $loan->FIRST_NAME.' '.$loan->MIDDLE_NAME.' '.$loan->LAST_NAME)));
@endphp
<style>
  .rc{--b:#0d47a1;--o:#f28c28;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .rc-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 10px rgba(15,35,80,.04);margin-bottom:20px;}
  .rc-h{padding:14px 20px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;}
  .rc-h h6{margin:0;font-weight:700;font-size:14.5px;}
  .rc-h small{color:var(--mut);}
  .rc-b{padding:18px 20px;}
  .rc-hero{background:linear-gradient(120deg,#0b3c8c,#0d47a1 50%,#1565c0);border-radius:16px;color:#fff;padding:20px 24px;margin-bottom:20px;}
  .rc-hero h4{color:#fff;margin:0;font-weight:700;font-size:19px;}
  .rc-hero .sub{color:#cfe0ff;font-size:12.5px;margin-top:3px;}
  .rc-hero .sub b{color:#fff;}
  .rc-figs{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-top:16px;}
  .rc-fig{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.16);border-radius:12px;padding:10px 14px;}
  .rc-fig span{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#cfe0ff;font-weight:700;}
  .rc-fig b{font-size:17px;font-weight:800;white-space:nowrap;}
  .rc-fig b small{font-size:11px;color:#cfe0ff;margin-right:3px;}
  .rc-tabs{display:inline-flex;background:var(--soft);border:1px solid var(--line);border-radius:10px;padding:3px;margin-bottom:16px;}
  .rc-tabs button{border:0;background:transparent;padding:7px 14px;border-radius:8px;font-weight:600;font-size:13px;color:var(--mut);}
  .rc-tabs button.on{background:#fff;color:var(--b);box-shadow:0 1px 4px rgba(0,0,0,.08);}
  .rc label{font-weight:600;font-size:12.5px;color:#374151;}
  .rc .form-control{border-radius:10px;padding:10px 12px;}
  .rc-hint{font-size:11.5px;color:var(--mut);margin-top:4px;}
  .rc-chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;}
  .rc-chips button{border:1px solid var(--line);background:#fff;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600;color:#374151;}
  .rc-chips button:hover,.rc-chips button.on{border-color:var(--b);color:var(--b);background:#eef4ff;}
  .rc-btn{border-radius:10px;font-weight:700;padding:10px 18px;}
  .rc-cmp{width:100%;border-collapse:collapse;font-size:13px;}
  .rc-cmp th{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:#64748b;background:var(--soft);padding:10px 12px;text-align:right;}
  .rc-cmp th:first-child,.rc-cmp td:first-child{text-align:left;}
  .rc-cmp td{padding:10px 12px;border-bottom:1px solid var(--line);text-align:right;font-variant-numeric:tabular-nums;}
  .rc-cmp td.new{font-weight:800;color:var(--b);}
  .rc-cmp tr.hl td{background:#f8fbff;}
  .rc-delta{display:inline-block;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px;}
  .rc-delta.down{background:#e6f6ee;color:#137a43;} .rc-delta.up{background:#fdecec;color:#c62828;}
  .rc-empty{text-align:center;color:var(--mut);padding:40px 10px;}
  .rc-empty i{font-size:34px;color:#c7d2e3;display:block;margin-bottom:8px;}
  .rc-note{font-size:12px;color:var(--mut);}
  .rc-loading{color:var(--b);font-size:12.5px;font-weight:600;}
  @media (max-width:767px){.rc-figs{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width:430px){.rc-figs{grid-template-columns:1fr;}}
</style>

<div class="rc-hero">
  <h4>{{ $name }}</h4>
  <div class="sub">Loan <b>{{ $loan->LOAN_ID }}</b> · Application {{ $loan->APPLICATION_NUMBER }} · Check No. {{ $loan->CHECK_NUMBER }} · {{ $loan->VOTE_NAME }}</div>
  <div class="rc-figs">
    <div class="rc-fig"><span>Principal balance (CBS)</span><b><small>TZS</small>{{ $money($s['principalBalance'] ?? null) }}</b></div>
    <div class="rc-fig"><span>Outstanding balance</span><b><small>TZS</small>{{ $money($s['outstandingBalance'] ?? null) }}</b></div>
    <div class="rc-fig"><span>Current instalment</span><b><small>TZS</small>{{ $money($currentInst) }}</b></div>
    <div class="rc-fig"><span>Current tenure</span><b>{{ $loan->TENURE ?? '—' }} <small>months</small></b></div>
  </div>
</div>

<div class="row">
  {{-- Inputs --}}
  <div class="col-xl-5">
    <div class="rc-card">
      <div class="rc-h"><h6><i class="fa fa-calculator" style="color:#0d47a1"></i> New terms</h6><small>Choose one: tenure or instalment</small></div>
      <div class="rc-b">
        <div class="rc-tabs">
          <button type="button" class="{{ $mode === 'tenure' ? 'on' : '' }}" wire:click="setMode('tenure')">By new tenure</button>
          <button type="button" class="{{ $mode === 'installment' ? 'on' : '' }}" wire:click="setMode('installment')">By new instalment</button>
        </div>

        <form wire:submit.prevent="calculate">
          @if($mode === 'tenure')
            <label for="rc-tenure">New tenure (months)</label>
            <input id="rc-tenure" type="number" min="1" class="form-control" wire:model.defer="tenure" placeholder="e.g. 48">
            <div class="rc-hint">Allowed {{ $minT }} – {{ $maxT }} months (product limit{{ !empty($s['retirementMonths']) ? ' and months to retirement' : '' }}). The new instalment is calculated.</div>
            @error('tenure')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            @if(count($quick))
              <div class="rc-chips">
                @foreach($quick as $q)
                  <button type="button" wire:click="quickTenure({{ $q }})" class="{{ (int) $tenure === $q ? 'on' : '' }}">{{ $q }} m</button>
                @endforeach
              </div>
            @endif
          @else
            <label for="rc-inst">New monthly instalment (TZS)</label>
            <input id="rc-inst" type="number" min="1" step="0.01" class="form-control" wire:model.defer="installment" placeholder="e.g. 50000">
            <div class="rc-hint">The shortest tenure whose instalment does not exceed this amount is used.</div>
            @error('installment')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          @endif

          <div class="d-flex align-items-center gap-3 mt-3">
            <button type="submit" class="btn btn-primary rc-btn" wire:loading.attr="disabled" wire:target="calculate,quickTenure"><i class="fa fa-calculator"></i> Calculate</button>
            <span class="rc-loading" wire:loading wire:target="calculate,quickTenure"><i class="fa fa-spinner fa-spin"></i> Calculating with core banking balance…</span>
          </div>
        </form>

        <hr>
        <div class="rc-note">
          Calculated by the ESS gateway with the same rules as <b>LOAN_RESTRUCTURE_AFFORDABILITY_REQUEST</b>:
          the principal balance from core banking is re-spread at the product rate
          ({{ $s['interestRate'] ?? '—' }}% · product {{ $s['productCode'] ?? '' }}).
          <b>No processing fee</b> is charged: it does not apply when MCB initiates the restructure.
          Nothing is sent to ESS until you press <b>Send proposal</b>.
        </div>
      </div>
    </div>

    {{-- Balance, exactly as answered to ESS in LOAN_RESTRUCTURE_BALANCE_RESPONSE --}}
    @php $bal = $s['balance'] ?? null; @endphp
    <div class="rc-card">
      <div class="rc-h"><h6><i class="fa fa-file-text-o" style="color:#0d47a1"></i> Loan balance</h6><small>As in LOAN_RESTRUCTURE_BALANCE_RESPONSE</small></div>
      <div class="rc-b">
        @if($bal)
          <table class="rc-cmp">
            <tbody>
              <tr><td>Loan Number</td><td style="font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px">{{ $bal['LoanNumber'] }}</td></tr>
              <tr><td>Instalment Amount</td><td>{{ $money($bal['InstallmentAmount']) }}</td></tr>
              <tr class="hl"><td><b>Principal Balance</b></td><td class="new">{{ $money($bal['PrincipalBalance']) }}</td></tr>
              <tr><td>Outstanding Balance</td><td>{{ $money($bal['OutstandingBalance']) }}</td></tr>
              <tr><td>Last Repayment Date</td><td>{{ \Illuminate\Support\Str::of($bal['LastRepaymentDate'])->before('T') }}</td></tr>
              <tr><td>Maturity Date</td><td>{{ \Illuminate\Support\Str::of($bal['MaturityDate'])->before('T') }}</td></tr>
              <tr><td>Validity Date</td><td>{{ str_replace('T', ' ', $bal['ValidityDate']) }}</td></tr>
            </tbody>
          </table>
          <div class="rc-hint mt-2">The new terms re-spread the <b>Principal Balance</b>. Outstanding Balance includes the 1% payoff margin.</div>
        @else
          <div class="rc-note">The balance is not available{{ $error ? ': '.$error : '' }}.</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Results --}}
  <div class="col-xl-7">
    <div class="rc-card">
      <div class="rc-h"><h6><i class="fa fa-balance-scale" style="color:#f28c28"></i> Current vs proposed</h6>
        @if($r && empty($r['failed']))<span class="badge" style="background:#e6f6ee;color:#137a43;border-radius:20px;padding:5px 10px">Eligible</span>@endif
      </div>
      <div class="rc-b">
        @if($error)
          <div class="alert alert-danger outline" role="alert"><i class="fa fa-exclamation-triangle"></i> {{ $error }}</div>
        @endif

        @if($r)
          <div class="table-responsive">
          <table class="rc-cmp">
            <thead><tr><th>Item</th><th>Current loan</th><th>Proposed</th></tr></thead>
            <tbody>
              <tr class="hl"><td>Monthly instalment</td><td>{{ $money($currentInst) }}</td>
                <td class="new">{{ $money($r['newInstalment'] ?? null) }}
                  @if($diff !== null && abs($diff) >= 0.01)
                    <br><span class="rc-delta {{ $diff < 0 ? 'down' : 'up' }}">{{ $diff < 0 ? '▼' : '▲' }} {{ $money(abs($diff)) }} / month</span>
                  @endif
                </td></tr>
              <tr class="hl"><td>Tenure (months)</td><td>{{ $loan->TENURE ?? '—' }}</td><td class="new">{{ $r['newTenure'] ?? '—' }}</td></tr>
              <tr><td>Principal to restructure</td><td>{{ $money($s['principalBalance'] ?? null) }}</td><td class="new">{{ $money($r['newPrincipal'] ?? null) }}</td></tr>
              <tr><td>Interest</td><td>—</td><td class="new">{{ $money($r['interest'] ?? null) }}</td></tr>
              <tr><td>Processing fee</td><td>—</td><td class="new">{{ $money($r['processingFee'] ?? 0) }}<br><span class="rc-note" style="font-weight:500">Not applicable (MCB-initiated)</span></td></tr>
              <tr class="hl"><td><b>Total amount to pay</b></td><td>{{ $money($loan->TOTAL_AMOUNT_TO_PAY) }}</td><td class="new">{{ $money($r['totalAmountToPay'] ?? null) }}</td></tr>
              <tr><td>Maturity / last repayment</td><td>{{ $s['maturityDate'] ?? '—' }}</td>
                <td class="new">{{ isset($r['newTenure']) ? now()->addMonths((int) $r['newTenure'])->format('Y-m') . ' (approx.)' : '—' }}</td></tr>
            </tbody>
          </table>
          </div>

          @if(empty($r['failed']))
            <div class="mt-4">
              @if($canSend)
                <label for="rc-reason">Reason for the restructure (sent to ESS)</label>
                <textarea id="rc-reason" class="form-control" rows="2" maxlength="255" wire:model.defer="reason" placeholder="e.g. Reduce monthly deduction to improve affordability"></textarea>
                @error('reason')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="d-flex align-items-center gap-3 mt-3 flex-wrap">
                  <button type="button" class="btn btn-warning rc-btn" wire:click="propose" wire:loading.attr="disabled" wire:target="propose"
                          onclick="return confirm('Send this restructure proposal to ESS? The employee will accept or reject it in the ESS portal.')">
                    <i class="fa fa-paper-plane"></i> Send proposal to ESS
                  </button>
                  <span class="rc-loading" wire:loading wire:target="propose"><i class="fa fa-spinner fa-spin"></i> Sending to ESS…</span>
                  <span class="rc-note">The employee accepts or rejects it in the ESS portal.</span>
                </div>
              @else
                <div class="alert alert-light-primary mb-0">Only a <b>Credit Officer</b> can send the proposal. You can still use this calculator.</div>
              @endif
            </div>
          @endif
        @elseif(!$error)
          <div class="rc-empty"><i class="fa fa-calculator"></i>Enter a new tenure or a new instalment and press <b>Calculate</b>,<br>or pick one of the quick tenures.</div>
        @endif
      </div>
    </div>
  </div>
</div>
</div>

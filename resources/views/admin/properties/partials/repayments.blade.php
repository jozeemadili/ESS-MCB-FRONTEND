@php
    use Carbon\Carbon;
    $money = fn ($x) => $x === null ? '—' : number_format((float) $x, 2, '.', ',');
    $day = function ($d) { if (empty($d)) return '—'; try { return Carbon::parse($d)->format('d M Y'); } catch (\Throwable $e) { return $d; } };
    $ready = \App\Models\PayrollDeduction::tableReady();
    $rows = $ready ? \App\Models\PayrollDeduction::forEmployee(Auth::user(), $vehicle->CHECK_NUMBER)->with('loan')->orderByDesc('CHECK_DATE')->orderByDesc('ID')->get() : collect();
    $latest = $rows->first();
    $thisLoan = $rows->where('LOAN_APPLICATION_ID', $vehicle->ID);
@endphp
<style>
  .rp{--b:#0d47a1;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .rp-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:14px 0;}
  .rp-kpi{background:#fff;border:1px solid var(--line);border-radius:12px;padding:12px 14px;position:relative;overflow:hidden;}
  .rp-kpi:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--a,var(--b));}
  .rp-kpi span{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:700;}
  .rp-kpi b{display:block;font-size:19px;font-weight:800;margin-top:3px;font-variant-numeric:tabular-nums;}
  .rp-kpi b small{font-size:11px;color:var(--mut);font-weight:600;margin-right:3px;}
  .rp-bar{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;}
  .rp-bar b{font-size:15px;} .rp-bar small{color:var(--mut);display:block;}
  .rp-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12.5px;border:1px solid var(--line);border-radius:12px;overflow:hidden;}
  .rp-table th{background:var(--soft);color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;border-bottom:2px solid var(--line);white-space:nowrap;}
  .rp-table td{padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top;}
  .rp-table tr.cur td{background:#f5f9ff;}
  .rp-table .r{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums;font-weight:700;}
  .rp-sub{display:block;color:var(--mut);font-size:11.5px;margin-top:2px;}
  .rp-mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;}
  .rp-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;}
  .rp-pill.stop{background:#fdecec;color:#c62828;} .rp-pill.cur{background:#e3f2fd;color:#0d47a1;}
  .rp-empty{text-align:center;color:var(--mut);padding:40px 10px;border:1px dashed var(--line);border-radius:12px;}
  @media (max-width:991px){.rp-kpis{grid-template-columns:repeat(2,minmax(0,1fr));}}
</style>

<div class="rp">
  <div class="rp-bar">
    <div>
      <b>Payroll deductions (ESS)</b>
      <small>Every monthly deduction ESS sent for check no. {{ $vehicle->CHECK_NUMBER }}, on all of this employee's MCB loans. Rows for this loan are highlighted.</small>
    </div>
    @if($rows->isNotEmpty())
      <a class="btn btn-success btn-sm" style="border-radius:8px;font-weight:600" href="{{ route('deductions-payroll-export', ['check' => $vehicle->CHECK_NUMBER]) }}"><i class="fa fa-file-excel-o"></i> Export Excel</a>
    @endif
  </div>

  @if(!$ready || $rows->isEmpty())
    <div class="rp-empty"><i class="fa fa-calendar-o fa-2x d-block mb-2"></i>No payroll deductions received from ESS for this employee yet.</div>
  @else
    <div class="rp-kpis">
      <div class="rp-kpi"><span>Payrolls deducted</span><b>{{ $rows->pluck('CHECK_DATE')->unique()->count() }}</b></div>
      <div class="rp-kpi" style="--a:#16a34a"><span>Total deducted</span><b><small>TZS</small>{{ $money($rows->sum('DEDUCTION_AMOUNT')) }}</b></div>
      <div class="rp-kpi" style="--a:#f28c28">@php $bal = $thisLoan->first() ?? $latest; @endphp<span>{{ $thisLoan->isNotEmpty() ? 'This loan' : 'Latest' }} balance ({{ $day($bal->CHECK_DATE) }})</span><b><small>TZS</small>{{ $money($bal->BALANCE_AMOUNT) }}</b></div>
      <div class="rp-kpi" style="--a:#dc2626"><span>Stop pay</span><b>{{ $rows->where('HAS_STOP_PAY', true)->count() }}</b></div>
    </div>

    <div style="overflow:auto">
      <table class="rp-table">
        <thead><tr><th>Payroll date</th><th>Loan</th><th>Vote / department</th><th class="r">Deducted</th><th class="r">Balance left</th><th>Stop pay</th><th>Received</th></tr></thead>
        <tbody>
        @foreach($rows as $p)
          @php $cur = (int) $p->LOAN_APPLICATION_ID === (int) $vehicle->ID; @endphp
          <tr class="{{ $cur ? 'cur' : '' }}">
            <td style="white-space:nowrap"><b>{{ $day($p->CHECK_DATE) }}</b></td>
            <td>
              <span class="rp-mono">{{ $p->LOAN_ID }}</span>
              @if($cur)<span class="rp-pill cur" style="margin-left:4px">This loan</span>
              @elseif($p->loan)<a class="rp-sub" href="{{ route('staff-profile', ['id' => $p->loan->ID]) }}">{{ ucwords(strtolower(str_replace('_', ' ', $p->loan->LOAN_TYPE ?: 'NEW_LOAN'))) }} · open loan</a>@endif
            </td>
            <td style="min-width:180px">{{ $p->VOTE_NAME }}<span class="rp-sub">{{ $p->DEPARTMENT_NAME }}</span></td>
            <td class="r">{{ $money($p->DEDUCTION_AMOUNT) }}</td>
            <td class="r">{{ $money($p->BALANCE_AMOUNT) }}</td>
            <td>@if($p->HAS_STOP_PAY)<span class="rp-pill stop">Stop pay</span><span class="rp-sub">{{ $p->HAS_STOP_PAY_REASON ?: 'No reason given' }}</span>@else<span style="color:#6b7280">No</span>@endif</td>
            <td style="white-space:nowrap;color:#6b7280">{{ $p->REQUEST_DATE ? Carbon::parse($p->REQUEST_DATE)->format('d M Y H:i') : '—' }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

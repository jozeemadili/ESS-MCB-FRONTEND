@extends('layouts.admin.master')
@section('title', 'Payroll Deductions')
@php
    use Carbon\Carbon;
    $money = fn ($x) => $x === null ? '—' : number_format((float) $x, 2, '.', ',');
    $day = function ($d) { if (empty($d)) return '—'; try { return Carbon::parse($d)->format('d M Y'); } catch (\Throwable $e) { return $d; } };
    $payrollLabel = $payroll === 'all' ? 'All payrolls' : 'Payroll '.$day($payroll);
@endphp
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Payroll Deductions · {{ $payrollLabel }}</h3>
    @endslot
    <li class="breadcrumb-item">Deductions</li>
    <li class="breadcrumb-item active">Payroll Deductions</li>
  @endcomponent

<style>
  .sd{--b:#0d47a1;--o:#f28c28;--r:#dc2626;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .sd-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px;}
  .sd-kpi{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;position:relative;overflow:hidden;}
  .sd-kpi:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--a,var(--b));}
  .sd-kpi span{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:700;}
  .sd-kpi b{display:block;font-size:22px;font-weight:800;margin-top:4px;font-variant-numeric:tabular-nums;}
  .sd-kpi b small{font-size:12px;color:var(--mut);font-weight:600;margin-right:3px;}
  .sd-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 10px rgba(15,35,80,.04);margin-bottom:18px;}
  .sd-bar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:14px 18px;border-bottom:1px solid var(--line);}
  .sd-title b{font-size:15px;} .sd-count{margin-left:8px;color:var(--mut);font-size:12.5px;}
  .sd-per,.sd-sel{border:1px solid var(--line);border-radius:10px;padding:6px 8px;font-size:12.5px;background:#fff;}
  .sd-pager{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:12px 18px;border-top:1px solid var(--line);font-size:12.5px;color:var(--mut);}
  .sd-pager .pagination{margin:0;}
  .sd-search{display:flex;gap:6px;flex-wrap:wrap;align-items:center;}
  .sd-search input[type=text]{border:1px solid var(--line);border-radius:10px;padding:7px 12px;font-size:13px;min-width:220px;}
  .sd-search label{font-size:12.5px;color:#475569;display:flex;gap:5px;align-items:center;margin:0 4px;}
  .sd-scroll{overflow:auto;}
  .sd-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12.5px;}
  .sd-table th{background:var(--soft);color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;border-bottom:2px solid var(--line);white-space:nowrap;}
  .sd-table td{padding:10px 12px;border-bottom:1px solid var(--line);vertical-align:top;}
  .sd-table tr:hover td{background:#fafcff;}
  .sd-table .r{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums;font-weight:700;}
  .sd-sub{display:block;color:var(--mut);font-size:11.5px;margin-top:2px;}
  .sd-mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;}
  .sd-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;}
  .sd-pill.ok{background:#e6f6ee;color:#137a43;} .sd-pill.stop{background:#fdecec;color:#c62828;} .sd-pill.grey{background:#f1f3f6;color:#55606e;}
  .sd-empty{text-align:center;color:var(--mut);padding:44px 10px;}
  .sd-btn{border-radius:8px;font-weight:600;font-size:12px;padding:5px 10px;}
  @media (max-width:991px){.sd-kpis{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width:575px){.sd-search input[type=text]{min-width:0;width:100%;} .sd-search{width:100%;}}
</style>

<div class="container-fluid sd">
  <div class="sd-kpis">
    <div class="sd-kpi"><span>Loans deducted</span><b>{{ number_format($summary['records']) }}</b></div>
    <div class="sd-kpi" style="--a:#16a34a"><span>Total deducted</span><b><small>TZS</small>{{ $money($summary['deducted']) }}</b></div>
    <div class="sd-kpi" style="--a:#f28c28"><span>Balance left (per ESS)</span><b><small>TZS</small>{{ $money($summary['balance']) }}</b></div>
    <div class="sd-kpi" style="--a:#dc2626"><span>Stop pay flagged</span><b>{{ number_format($summary['stop']) }}</b></div>
  </div>

  <div class="sd-card">
    <div class="sd-bar">
      <div class="sd-title">
        <b>{{ $payrollLabel }}</b>
        <span class="sd-count">{{ number_format($deductions->total()) }} record{{ $deductions->total() == 1 ? '' : 's' }}{{ $stopOnly ? ' with stop pay' : '' }}{{ $search !== '' ? ' matching "'.$search.'"' : '' }}</span>
      </div>
      <form class="sd-search" method="get" action="{{ route('deductions-payroll') }}">
        <select name="payroll" class="sd-sel" onchange="this.form.submit()" title="Payroll date">
          @foreach($dates as $d)<option value="{{ $d }}" {{ $payroll === (string) $d ? 'selected' : '' }}>Payroll {{ $day($d) }}</option>@endforeach
          <option value="all" {{ $payroll === 'all' ? 'selected' : '' }}>All payrolls</option>
        </select>
        <label><input type="checkbox" name="stop" value="1" {{ $stopOnly ? 'checked' : '' }} onchange="this.form.submit()"> Stop pay only</label>
        <select name="per_page" class="sd-per" onchange="this.form.submit()" title="Rows per page">
          @foreach([15, 25, 50, 100] as $n)<option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} / page</option>@endforeach
        </select>
        <input type="text" name="q" value="{{ $search }}" placeholder="Check no., loan no., name or vote">
        <button class="btn btn-primary sd-btn" type="submit"><i class="fa fa-search"></i></button>
        <a class="btn btn-success sd-btn" href="{{ route('deductions-payroll-export', array_filter(['payroll' => $payroll, 'stop' => $stopOnly ? 1 : null, 'q' => $search !== '' ? $search : null])) }}"><i class="fa fa-file-excel-o"></i> Export Excel</a>
      </form>
    </div>

    @if($deductions->isEmpty())
      <div class="sd-empty"><i class="fa fa-calendar-o fa-2x d-block mb-2"></i>No payroll deductions {{ $dates->isEmpty() ? 'received from ESS yet' : 'match these filters' }}.</div>
    @else
      <div class="sd-scroll">
        <table class="sd-table">
          <thead>
            <tr><th>Payroll date</th><th>Employee</th><th>Loan</th><th>Vote / department</th><th class="r">Deducted</th><th class="r">Balance left</th><th>Stop pay</th><th>Received</th></tr>
          </thead>
          <tbody>
          @foreach($deductions as $d)
            @php $loan = $d->loan; @endphp
            <tr>
              <td style="white-space:nowrap"><b>{{ $day($d->CHECK_DATE) }}</b></td>
              <td style="min-width:180px">
                @if($loan)
                  <a href="{{ route('staff-profile', ['id' => $loan->ID]) }}"><b>{{ strtoupper(trim($loan->FIRST_NAME.' '.$loan->LAST_NAME)) }}</b></a>
                @else
                  <b>{{ strtoupper(trim($d->FIRST_DATE.' '.$d->LAST_NAME)) }}</b>
                @endif
                <span class="sd-sub">Check No. {{ $d->CHECK_NUMBER }}</span>
              </td>
              <td><span class="sd-mono">{{ $d->LOAN_ID }}</span>@if($loan)<span class="sd-sub">{{ ucwords(strtolower(str_replace('_', ' ', $loan->LOAN_TYPE ?: 'NEW_LOAN'))) }} · {{ $loan->TRANSACTION_STATUS }}</span>@endif</td>
              <td style="min-width:180px">{{ $d->VOTE_NAME }}<span class="sd-sub">{{ $d->DEPARTMENT_NAME }}</span></td>
              <td class="r">{{ $money($d->DEDUCTION_AMOUNT) }}</td>
              <td class="r">{{ $money($d->BALANCE_AMOUNT) }}</td>
              <td>@if($d->HAS_STOP_PAY)<span class="sd-pill stop">Stop pay</span><span class="sd-sub">{{ $d->HAS_STOP_PAY_REASON ?: 'No reason given' }}</span>@else<span class="sd-pill ok">No</span>@endif</td>
              <td style="white-space:nowrap">{{ $d->REQUEST_DATE ? Carbon::parse($d->REQUEST_DATE)->format('d M Y H:i') : '—' }}<span class="sd-sub">Batch #{{ $d->BATCH_APPLICATION_PAYMENT_ID }}</span></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="sd-pager">
        <span>Showing {{ $deductions->firstItem() }}–{{ $deductions->lastItem() }} of {{ number_format($deductions->total()) }}</span>
        {{ $deductions->links() }}
      </div>
    @endif
  </div>

  <div class="sd-card">
    <div class="sd-bar">
      <div class="sd-title"><b>Batches received from ESS</b><span class="sd-count">{{ $payroll === 'all' ? 'latest 20' : 'for '.$payrollLabel }}</span></div>
    </div>
    @if($batches->isEmpty())
      <div class="sd-empty">No batches.</div>
    @else
      <div class="sd-scroll">
        <table class="sd-table">
          <thead><tr><th>#</th><th>Payroll date</th><th>Batch</th><th class="r">Records sent</th><th class="r">Saved for MCB loans</th><th>Status</th><th>Received</th></tr></thead>
          <tbody>
          @foreach($batches as $b)
            <tr>
              <td class="sd-mono">{{ $b->ID }}</td>
              <td>{{ $day($b->BATCH_PAYMENT_DATE) }}</td>
              <td>{{ $b->BATCH_NUMBER ?? '—' }} of {{ $b->TOTAL_BATCH ?? '—' }}</td>
              <td class="r">{{ number_format((int) $b->BATCH_RECORD_SIZE) }}</td>
              <td class="r">{{ number_format($b->deductions_count) }}</td>
              <td><span class="sd-pill {{ $b->STATUS === 'Processed' ? 'ok' : ($b->STATUS === 'Pending' ? 'grey' : 'stop') }}">{{ $b->STATUS ?: '—' }}</span></td>
              <td style="white-space:nowrap">{{ $b->REQUEST_DATE ? Carbon::parse($b->REQUEST_DATE)->format('d M Y H:i') : '—' }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="sd-pager"><span>"Records sent" is ESS's BatchRecordSize. A batch saved before the gateway fix shows Pending.</span></div>
    @endif
  </div>
</div>
@endsection

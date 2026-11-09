@extends('layouts.admin.master')
@section('title', 'Stopped Deductions')
@php
    use Carbon\Carbon;
    $money = fn ($x) => $x === null ? '—' : number_format((float) $x, 2, '.', ',');
    $tabs = \App\Models\LoanDeduction::FILTERS;
    $stopDate = function ($d) { if (empty($d)) return '—'; try { return Carbon::parse($d)->format('d M Y'); } catch (\Throwable $e) { return $d; } };
@endphp
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Stopped Deductions · {{ $tabs[$filter] }}</h3>
    @endslot
    <li class="breadcrumb-item">Deductions</li>
    <li class="breadcrumb-item">Stopped</li>
    <li class="breadcrumb-item active">{{ $tabs[$filter] }}</li>
  @endcomponent

<style>
  .sd{--b:#0d47a1;--o:#f28c28;--r:#dc2626;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .sd-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px;}
  .sd-kpi{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;position:relative;overflow:hidden;}
  .sd-kpi:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--a,var(--b));}
  .sd-kpi span{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:700;}
  .sd-kpi b{display:block;font-size:22px;font-weight:800;margin-top:4px;font-variant-numeric:tabular-nums;}
  .sd-kpi b small{font-size:12px;color:var(--mut);font-weight:600;margin-right:3px;}
  .sd-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 10px rgba(15,35,80,.04);}
  .sd-bar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:14px 18px;border-bottom:1px solid var(--line);}
  .sd-title b{font-size:15px;} .sd-count{margin-left:8px;color:var(--mut);font-size:12.5px;}
  .sd-per{border:1px solid var(--line);border-radius:10px;padding:6px 8px;font-size:12.5px;background:#fff;}
  .sd-pager{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:12px 18px;border-top:1px solid var(--line);font-size:12.5px;color:var(--mut);}
  .sd-pager .pagination{margin:0;}
  .sd-tabs{display:flex;gap:6px;flex-wrap:wrap;}
  .sd-tabs a{padding:6px 12px;border-radius:20px;border:1px solid var(--line);font-size:12.5px;font-weight:600;color:#475569;}
  .sd-tabs a.on{background:var(--b);border-color:var(--b);color:#fff;}
  .sd-tabs a .n{display:inline-block;margin-left:6px;padding:0 7px;border-radius:10px;background:rgba(0,0,0,.08);font-size:11px;}
  .sd-tabs a.on .n{background:rgba(255,255,255,.25);}
  .sd-search{display:flex;gap:6px;}
  .sd-search input{border:1px solid var(--line);border-radius:10px;padding:7px 12px;font-size:13px;min-width:240px;}
  .sd-search button{border-radius:10px;}
  .sd-scroll{overflow:auto;}
  .sd-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12.5px;}
  .sd-table th{background:var(--soft);color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;border-bottom:2px solid var(--line);white-space:nowrap;}
  .sd-table td{padding:10px 12px;border-bottom:1px solid var(--line);vertical-align:top;}
  .sd-table tr:hover td{background:#fafcff;}
  .sd-table td:nth-child(2){min-width:190px;} .sd-table td:nth-child(3){min-width:200px;}
  .sd-table .sd-btn{white-space:nowrap;}
  .sd-table .r{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums;font-weight:700;}
  .sd-sub{display:block;color:var(--mut);font-size:11.5px;margin-top:2px;}
  .sd-mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;}
  .sd-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;}
  .sd-pill.pend{background:#fdecec;color:#c62828;} .sd-pill.done{background:#e6f6ee;color:#137a43;} .sd-pill.nf{background:#fff4e5;color:#b45309;}
  .sd-empty{text-align:center;color:var(--mut);padding:44px 10px;}
  .sd-btn{border-radius:8px;font-weight:600;font-size:12px;padding:5px 10px;}
  @media (max-width:991px){.sd-kpis{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width:575px){.sd-search input{min-width:0;width:100%;} .sd-search{width:100%;}}
</style>

<div class="container-fluid sd">
  @foreach ($errors->all() as $error)
    <div class="alert alert-danger outline alert-dismissible fade show" role="alert">{{ $error }}<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endforeach
  @if($message = Session::get('success'))
    <div class="alert alert-success outline alert-dismissible fade show" role="alert">{!! $message !!}<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endif

  <div class="sd-kpis">
    <div class="sd-kpi" style="--a:#dc2626"><span>Pending review</span><b>{{ number_format($counts['pending']) }}</b></div>
    <div class="sd-kpi" style="--a:#f28c28"><span>Balance at risk (pending)</span><b><small>TZS</small>{{ $money($atRisk) }}</b></div>
    <div class="sd-kpi" style="--a:#b45309"><span>Loan not found</span><b>{{ number_format($counts['notfound']) }}</b></div>
    <div class="sd-kpi" style="--a:#16a34a"><span>Reviewed</span><b>{{ number_format($counts['reviewed']) }}</b></div>
  </div>

  <div class="sd-card">
    <div class="sd-bar">
      <div class="sd-title">
        <b>{{ $tabs[$filter] }}</b>
        <span class="sd-count">{{ number_format($deductions->total()) }} record{{ $deductions->total() == 1 ? '' : 's' }}{{ $search !== '' ? ' matching "'.$search.'"' : '' }}</span>
      </div>
      <form class="sd-search" method="get" action="{{ route('deductions-stopped') }}">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <select name="per_page" class="sd-per" onchange="this.form.submit()" title="Rows per page">
          @foreach([15, 25, 50, 100] as $n)<option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} / page</option>@endforeach
        </select>
        <input type="text" name="q" value="{{ $search }}" placeholder="Check no., loan no., application no. or name">
        <button class="btn btn-primary sd-btn" type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>

    @if($deductions->isEmpty())
      <div class="sd-empty"><i class="fa fa-check-circle fa-2x d-block mb-2" style="color:#86efac"></i>No deduction stops {{ $filter === 'all' ? '' : 'in "'.$tabs[$filter].'"' }}{{ $search !== '' ? ' matching "'.$search.'"' : '' }}.</div>
    @else
      <div class="sd-scroll">
        <table class="sd-table">
          <thead>
            <tr>
              <th>Received at MCB</th><th>Employee</th><th>Loan / Application</th><th class="r">Balance</th><th class="r">Deduction</th>
              <th>Stop reason</th><th>Stop date</th><th>Status</th><th>Review</th>
            </tr>
          </thead>
          <tbody>
          @foreach($deductions as $d)
            @php $loan = $d->loan; @endphp
            <tr>
              <td>{{ $d->RECEIVED_DATE ? $d->RECEIVED_DATE->format('d M Y H:i') : '—' }}<span class="sd-sub">{{ $d->RECEIVED_DATE ? Carbon::parse($d->RECEIVED_DATE->format('Y-m-d H:i:s'), 'Africa/Dar_es_Salaam')->diffForHumans() : '' }}</span></td>
              <td>
                @if($loan)
                  <a href="{{ route('staff-profile', ['id' => $loan->ID]) }}"><b>{{ strtoupper(trim($loan->FIRST_NAME.' '.$loan->LAST_NAME)) }}</b></a>
                  <span class="sd-sub">Check No. {{ $d->CHECK_NUMBER }} · {{ $loan->VOTE_NAME }}</span>
                @else
                  <b>Check No. {{ $d->CHECK_NUMBER }}</b><span class="sd-sub">Not an MCB loan record</span>
                @endif
              </td>
              <td><span class="sd-mono">{{ $d->LOAN_NUMBER }}</span><span class="sd-sub sd-mono">{{ $d->APPLICATION_NUMBER }}</span></td>
              <td class="r">{{ $money($d->BALANCE_AMOUNT) }}</td>
              <td class="r">{{ $money($d->DEDUCTION_AMOUNT) }}</td>
              <td style="min-width:160px">{{ $d->STOP_REASON ?: '—' }}<span class="sd-sub">{{ $d->DEDUCTION_DESCRIPTION }} ({{ $d->DEDUCTION_CODE }})</span></td>
              <td style="white-space:nowrap">{{ $stopDate($d->STOP_DATE) }}</td>
              <td>
                @if($d->STATUS === 'LOAN_NOT_FOUND')<span class="sd-pill nf">Loan not found</span>
                @elseif($d->REVIEWED_DATE)<span class="sd-pill done">Reviewed</span>
                @else<span class="sd-pill pend">Pending review</span>@endif
                @if($loan)<span class="sd-sub">Loan: {{ $loan->TRANSACTION_STATUS }}{{ $d->PREVIOUS_LOAN_STATUS ? ' · was '.$d->PREVIOUS_LOAN_STATUS : '' }}</span>@endif
              </td>
              <td style="min-width:170px">
                @if($d->REVIEWED_DATE)
                  <b>{{ $d->REVIEWED_BY }}</b><span class="sd-sub">{{ $d->REVIEWED_DATE->format('d M Y H:i') }}</span>
                  <span class="sd-sub" style="color:#374151">{{ $d->REVIEW_REMARKS }}</span>
                @elseif(Auth::user()->role != 'Reports')
                  <button class="btn btn-outline-primary sd-btn" type="button" data-bs-toggle="modal" data-bs-target="#reviewModal"
                          data-id="{{ $d->ID }}" data-loan="{{ $d->LOAN_NUMBER }}" data-name="{{ $loan ? strtoupper(trim($loan->FIRST_NAME.' '.$loan->LAST_NAME)) : 'Check No. '.$d->CHECK_NUMBER }}"
                          onclick="sdReview(this)"><i class="fa fa-check"></i> Mark reviewed</button>
                @else
                  <span class="sd-sub">Not reviewed</span>
                @endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="sd-pager">
        <span>Showing <b>{{ number_format($deductions->firstItem()) }}</b>–<b>{{ number_format($deductions->lastItem()) }}</b> of <b>{{ number_format($deductions->total()) }}</b></span>
        <div>{{ $deductions->onEachSide(1)->links() }}</div>
      </div>
    @endif
  </div>
  <p class="text-muted mt-3" style="font-size:12px">
    ESS sends a deduction stop when an employer stops an employee's deduction or the employee becomes inactive (API 2.7.3).
    The loan keeps its transaction status; its loan status is set to <b>Deduction Stopped</b>. Review each stop and record the recovery action taken.
  </p>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Mark deduction stop as reviewed</h5>
        <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="{{ route('deductions-stopped-review') }}">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" id="sdReviewId">
          <p class="mb-2">Loan <b id="sdReviewLoan"></b> · <span id="sdReviewName"></span></p>
          <label class="col-form-label">Remarks / recovery action</label>
          <textarea class="form-control" name="remarks" required minlength="5" maxlength="500" rows="3" placeholder="e.g. Called employer HR; customer to repay directly from 1 Dec"></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary" type="submit">Save review</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function sdReview(btn) {
    document.getElementById('sdReviewId').value = btn.dataset.id;
    document.getElementById('sdReviewLoan').textContent = btn.dataset.loan;
    document.getElementById('sdReviewName').textContent = btn.dataset.name;
  }
</script>
@endpush
@endsection

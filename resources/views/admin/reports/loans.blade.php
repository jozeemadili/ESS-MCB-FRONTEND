@extends('layouts.admin.master')
@php
  use App\Support\LoanReport;
  use Carbon\Carbon;
  $title = LoanReport::title($type);
  $columns = LoanReport::columns($type);
  $extraColumns = array_slice($columns, 19, null, true); // the report's own columns, after the 19 All Loans columns
  $qs = LoanReport::queryString($filters);
  $excelUrl = $type === 'all' ? route('policies-reports-download-Excel', $qs) : route('loans-report-download', ['type' => $type] + $qs);
  $searching = LoanReport::hasFilters($filters);
  $fmt = fn ($v) => number_format((float) $v, 2, '.', ',');
  $dt = function ($v) { if (empty($v)) return null; try { return $v instanceof \DateTimeInterface ? Carbon::instance($v) : Carbon::parse($v); } catch (\Throwable $e) { return null; } };
  $tabs = ['all' => ['All Loans', route('loans-applications')], 'new' => ['New Loans', route('loans-report', 'new')], 'restructuring' => ['Restructuring', route('loans-report', 'restructuring')],
           'takeover' => ['Takeover', route('loans-report', 'takeover')], 'topup' => ['Top-up', route('loans-report', 'topup')]];
  $pill = function ($s) { $s = strtolower((string) $s);
      return str_contains($s, 'active') || str_contains($s, 'disburs') || str_contains($s, 'complet') ? 'ok'
          : (str_contains($s, 'reject') || str_contains($s, 'cancel') || str_contains($s, 'fail') ? 'bad'
          : (str_contains($s, 'approv') || str_contains($s, 'accept') ? 'blue' : 'wait')); };
@endphp
@section('title', $title)
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ $title }}</h3>
    @endslot
    @slot('breadcrumb_action_buttons')
      <li><a class="btn" style="background:#137a43;border-color:#137a43;color:#fff;font-weight:600;border-radius:10px" href="{{ $excelUrl }}"><i class="fa fa-file-excel-o"></i> Download Excel{{ $searching ? ' (filtered)' : '' }}</a></li>
    @endslot
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">{{ $title }}</li>
  @endcomponent

<style>
  .rp{--b:#0d47a1;--o:#f28c28;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .rp-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;}
  .rp-tabs a{padding:8px 16px;border-radius:10px;border:1px solid var(--line);background:#fff;color:#475569;font-weight:600;font-size:13px;}
  .rp-tabs a.on{background:var(--b);border-color:var(--b);color:#fff;box-shadow:0 4px 12px rgba(13,71,161,.2);}
  .rp-tabs a:hover:not(.on){border-color:var(--b);color:var(--b);}
  .rp-desc{color:var(--mut);font-size:12.5px;margin:-4px 0 14px;}
  .rp-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:14px;}
  .rp-kpi{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;position:relative;overflow:hidden;}
  .rp-kpi:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--a,var(--b));}
  .rp-kpi span{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:700;}
  .rp-kpi b{display:block;font-size:20px;font-weight:800;margin-top:4px;font-variant-numeric:tabular-nums;}
  .rp-kpi small{color:var(--mut);font-size:11px;font-weight:600;}
  .rp-status{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;}
  .rp-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 10px rgba(15,35,80,.04);margin-bottom:14px;}
  .rp-filter{padding:14px 18px;display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:10px;align-items:end;}
  .rp-filter label{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--mut);font-weight:700;margin-bottom:4px;display:block;}
  .rp-filter .form-control,.rp-filter .form-select{border:1px solid var(--line);border-radius:10px;font-size:13px;height:38px;}
  .rp-filter .rp-actions{display:flex;gap:6px;}
  .rp-filter .btn{border-radius:10px;height:38px;font-weight:600;font-size:13px;white-space:nowrap;}
  .rp-active{padding:0 18px 12px;display:flex;flex-wrap:wrap;gap:6px;align-items:center;font-size:12px;color:var(--mut);}
  .rp-chip{background:#e8effb;color:var(--b);border-radius:20px;padding:3px 10px;font-weight:600;font-size:11.5px;}
  .rp-bar{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:12px 18px;border-bottom:1px solid var(--line);}
  .rp-bar b{font-size:14px;} .rp-count{margin-left:8px;color:var(--mut);font-size:12.5px;}
  .rp-bar select{border:1px solid var(--line);border-radius:8px;padding:4px 8px;font-size:12.5px;}
  .rp-scroll{overflow:auto;max-height:70vh;}
  .rp-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12.5px;}
  .rp-table thead th{position:sticky;top:0;z-index:2;background:var(--soft);color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;border-bottom:2px solid var(--line);white-space:nowrap;}
  .rp-table thead th.x{background:#fff4e5;color:#b45309;}
  .rp-table td,.rp-table tbody th{padding:9px 12px;border-bottom:1px solid var(--line);white-space:nowrap;vertical-align:middle;}
  .rp-table tbody tr:nth-child(even) td,.rp-table tbody tr:nth-child(even) th{background:#fafbfd;}
  .rp-table tbody tr:hover td,.rp-table tbody tr:hover th{background:#eef4ff;}
  .rp-table .r{text-align:right;font-variant-numeric:tabular-nums;font-weight:700;}
  .rp-table .n{color:var(--mut);font-weight:600;}
  .rp-table .stick{position:sticky;left:0;z-index:1;background:#fff;box-shadow:1px 0 0 var(--line);}
  .rp-table thead th.stick{z-index:3;background:var(--soft);}
  .rp-person a{color:var(--b);font-weight:700;} .rp-person a:hover{color:var(--o);}
  .rp-sub{display:block;color:var(--mut);font-size:11px;margin-top:1px;font-weight:400;}
  .rp-mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;}
  .rp-muted{color:#a0a8b4;}
  .rp-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
  .rp-pill.wait{background:#fff4e5;color:#b45309;} .rp-pill.ok{background:#e6f6ee;color:#137a43;} .rp-pill.bad{background:#fdecec;color:#c62828;} .rp-pill.blue{background:#e3f2fd;color:#0d47a1;} .rp-pill.grey{background:#f1f3f6;color:#55606e;}
  .rp-empty{text-align:left;padding-left:120px !important;color:var(--mut);padding:44px 10px !important;white-space:normal !important;}
  .rp-pager{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:12px 18px;border-top:1px solid var(--line);font-size:12.5px;color:var(--mut);}
  .rp-pager .pagination{margin:0;}
  @media (max-width:575px){.rp-filter{grid-template-columns:1fr;}}
</style>

<div class="container-fluid rp">
  <div class="rp-tabs">
    @foreach($tabs as $key => [$label, $url])
      <a href="{{ $url }}" class="{{ $type === $key ? 'on' : '' }}">{{ $label }}</a>
    @endforeach
  </div>
  <div class="rp-desc">{{ LoanReport::TYPES[$type][2] }} The Excel file has the same records{{ $searching ? ' (with your search applied)' : '' }} and columns as this table.</div>

  {{-- Summary of the records matching the search --}}
  <div class="rp-kpis">
    <div class="rp-kpi"><span>Records</span><b>{{ number_format($summary['count']) }}</b><small>{{ $searching ? 'matching your search' : 'in this report' }}</small></div>
    <div class="rp-kpi" style="--a:#137a43"><span>Total loan amount</span><b>{{ $fmt($summary['amount']) }}</b><small>TZS</small></div>
    <div class="rp-kpi" style="--a:#f28c28"><span>Total monthly deduction</span><b>{{ $fmt($summary['instalment']) }}</b><small>TZS</small></div>
    <div class="rp-kpi" style="--a:#6d28d9"><span>By loan status</span>
      <div class="rp-status">
        @forelse($summary['byStatus']->take(5) as $s => $n)
          <span class="rp-pill {{ $s === 'Not set' ? 'grey' : $pill($s) }}">{{ $s }} · {{ number_format($n) }}</span>
        @empty
          <span class="rp-muted">—</span>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Search: same options as before (Application No., Loan ID, Phone No, Name, Date Wise, Status) --}}
  <div class="rp-card">
    <form method="get" action="{{ url()->current() }}" class="rp-filter" id="rpSearch">
      <div>
        <label for="rpBy">Search by</label>
        <select class="form-select" id="rpBy" onchange="rpSearchBy(this.value)">
          @foreach(LoanReport::SEARCH_BY as $key => $label)
            <option value="{{ $key }}" @selected($searchBy === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div data-for="id_number loan_id phone_number name">
        <label for="rpRef" id="rpRefLabel">Value</label>
        <input class="form-control" id="rpRef" autocomplete="off"
               value="{{ $filters['id_number'] ?: ($filters['loan_id'] ?: ($filters['phone'] ?: $filters['cname'])) }}">
      </div>
      <div data-for="status">
        <label for="rpStatus">Status</label>
        <select class="form-select" id="rpStatus" name="status">
          <option value="">-- Choose status --</option>
          @foreach($statuses as $s)
            <option value="{{ $s }}" @selected($filters['status'] === $s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div data-for="date_wise status">
        <label for="rpFrom">Applied from</label>
        <input class="form-control" type="date" id="rpFrom" name="start_date" value="{{ $filters['start_date'] }}">
      </div>
      <div data-for="date_wise status">
        <label for="rpTo">Applied to</label>
        <input class="form-control" type="date" id="rpTo" name="end_date" value="{{ $filters['end_date'] }}">
      </div>
      <input type="hidden" name="per_page" value="{{ $perPage }}">
      <div class="rp-actions">
        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
        @if($searching)<a class="btn btn-light" href="{{ url()->current() }}">Reset</a>@endif
      </div>
    </form>
    @if($searching)
    <div class="rp-active">
      Showing results for:
      @if($filters['id_number'])<span class="rp-chip">Application No. {{ $filters['id_number'] }}</span>@endif
      @if($filters['loan_id'])<span class="rp-chip">Loan ID {{ $filters['loan_id'] }}</span>@endif
      @if($filters['phone'])<span class="rp-chip">Phone {{ $filters['phone'] }}</span>@endif
      @if($filters['cname'])<span class="rp-chip">Name “{{ $filters['cname'] }}”</span>@endif
      @if($filters['status'])<span class="rp-chip">Status {{ $filters['status'] }}</span>@endif
      @if($filters['start_date'] && $filters['end_date'])<span class="rp-chip">Applied {{ Carbon::parse($filters['start_date'])->format('d M Y') }} – {{ Carbon::parse($filters['end_date'])->format('d M Y') }}</span>@endif
    </div>
    @endif
  </div>

  <div class="rp-card">
    <div class="rp-bar">
      <div><b>{{ $title }}</b><span class="rp-count">{{ number_format($loans->total()) }} record{{ $loans->total() == 1 ? '' : 's' }}</span></div>
      <div>
        <label class="rp-count" for="rpPer" style="margin:0 6px 0 0">Rows per page</label>
        <select id="rpPer" onchange="var u=new URL(location.href);u.searchParams.set('per_page',this.value);u.searchParams.delete('page');location.href=u.toString();">
          @foreach([15, 25, 50, 100] as $n)<option value="{{ $n }}" @selected($perPage == $n)>{{ $n }}</option>@endforeach
        </select>
      </div>
    </div>
    <div class="rp-scroll">
      <table class="rp-table">
        <thead>
          <tr>
            <th>#</th>
            <th class="stick">Customer name</th>
            <th class="r">Loan amount</th>
            <th>Loan Account</th>
            <th>Period</th>
            <th>Bank Branch</th>
            <th>Region</th>
            <th>District</th>
            <th>Loan Purpose</th>
            <th>Application Date</th>
            <th>Disbursement date</th>
            <th>Application Number</th>
            <th>Deposit Account number</th>
            <th>Phone number</th>
            <th>Vote number</th>
            <th>Vote name</th>
            <th class="r">Deductible Amount</th>
            <th class="r">Loan processing fee</th>
            <th class="r">Insurance</th>
            <th>Loan Status</th>
            @foreach($extraColumns as $header => $v)<th class="x">{{ $header }}</th>@endforeach
          </tr>
        </thead>
        <tbody>
        @forelse($loans as $loan)
          @php
            $payment = $payments[$loan->ID] ?? null;
            $applied = $dt($loan->APPLICATION_DATE);
            $disbursed = $dt($loan->LOAN_DISBURSED_DATE);
          @endphp
          <tr>
            <th class="n">{{ $loans->firstItem() + $loop->index }}</th>
            <td class="stick rp-person">
              <a href="{{ route('staff-profile', ['id' => $loan->ID]) }}">{{ trim(preg_replace('/\s+/', ' ', strtoupper($loan->FIRST_NAME.' '.$loan->MIDDLE_NAME.' '.$loan->LAST_NAME))) }}</a>
              <span class="rp-sub">{{ $loan->LOAN_TYPE ?: 'Loan' }}{{ $loan->LOAN_ID ? ' · '.$loan->LOAN_ID : '' }}</span>
            </td>
            <td class="r">{{ $fmt($loan->REQUESTED_AMOUNT) }}</td>
            <td class="rp-mono">{!! $loan->DISBURSEMENT_RECEIPT ? e($loan->DISBURSEMENT_RECEIPT) : '<span class="rp-muted">—</span>' !!}</td>
            <td>{{ $loan->TENURE }} <span class="rp-muted">mo</span></td>
            <td>{{ $loan->INIT_BRANCH }}</td>
            <td></td>
            <td></td>
            <td>{{ $loan->LOAN_PURPOSE }}</td>
            <td>@if($applied){{ $applied->format('d M Y') }}<span class="rp-sub">{{ $applied->format('H:i:s') }}</span>@else<span class="rp-muted">—</span>@endif</td>
            <td>@if($disbursed){{ $disbursed->format('d M Y') }}<span class="rp-sub">{{ $disbursed->format('H:i:s') }}</span>@else<span class="rp-muted">Not disbursed</span>@endif</td>
            <td class="rp-mono">{{ $loan->APPLICATION_NUMBER }}</td>
            <td class="rp-mono">{{ $loan->BANK_ACCOUNT_NUMBER }}</td>
            <td>{{ $loan->MSISDN }}</td>
            <td>{{ $loan->VOTE_CODE }}</td>
            <td><span title="{{ $loan->VOTE_NAME }}">{{ \Illuminate\Support\Str::limit($loan->VOTE_NAME, 34) }}</span></td>
            <td class="r">{{ $fmt($loan->MONTHLY_DEDUCTION) }}</td>
            <td class="r">{{ $fmt($loan->PROCESSING_FEE) }}</td>
            <td class="r">{{ $fmt($loan->INSURANCE) }}</td>
            <td>@if($loan->LOAN_STATUS)<span class="rp-pill {{ $pill($loan->LOAN_STATUS) }}">{{ $loan->LOAN_STATUS }}</span>@else<span class="rp-muted">—</span>@endif</td>
            @foreach($extraColumns as $header => $value)
              @php $cell = $value($loan, $payment); @endphp
              <td class="{{ is_string($cell) && preg_match('/^-?[\d,]+\.\d{2}$/', $cell) ? 'r' : '' }}">
                @if($header === 'Transfer Status' || $header === 'Transaction Status')
                  <span class="rp-pill {{ $cell === 'Transferred' ? 'ok' : ($cell === 'Not transferred' || $cell === 'No payment record' ? 'wait' : $pill($cell)) }}">{{ $cell ?: '—' }}</span>
                @else
                  {!! ($cell === '' || $cell === null) ? '<span class="rp-muted">—</span>' : e($cell) !!}
                @endif
              </td>
            @endforeach
          </tr>
        @empty
          <tr><td colspan="{{ 20 + count($extraColumns) }}" class="rp-empty"><i class="fa fa-inbox fa-2x d-block mb-2"></i>{{ $searching ? 'No loans match your search.' : 'No loans in this report yet.' }}</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="rp-pager">
      <span>{{ $loans->total() ? 'Showing '.number_format($loans->firstItem()).'–'.number_format($loans->lastItem()).' of '.number_format($loans->total()) : 'No records' }}</span>
      {{ $loans->onEachSide(1)->links() }}
    </div>
  </div>
</div>

@push('scripts')
<script>
  var RP_REF = {
    id_number:    {name: 'id_number',    label: 'Application No.', ph: 'Enter Loan Application Number...', type: 'text'},
    loan_id:      {name: 'loan_id',      label: 'Loan ID',         ph: 'Enter Loan ID...',                type: 'text'},
    phone_number: {name: 'phone_number', label: 'Phone No',        ph: "Enter user's Phone...",           type: 'number'},
    name:         {name: 'cname',        label: 'Name',            ph: "Enter user's Name...",            type: 'text'}
  };
  function rpSearchBy(by) {
    document.querySelectorAll('#rpSearch [data-for]').forEach(function (el) {
      var on = el.dataset.for.split(' ').indexOf(by) >= 0;
      el.style.display = on ? '' : 'none';
      el.querySelectorAll('input,select').forEach(function (i) { i.disabled = !on; });
    });
    var ref = document.getElementById('rpRef'), cfg = RP_REF[by];
    if (cfg) {
      ref.name = cfg.name; ref.type = cfg.type; ref.placeholder = cfg.ph; ref.required = true;
      document.getElementById('rpRefLabel').textContent = cfg.label;
    } else { ref.removeAttribute('name'); ref.required = false; }
    var dates = by === 'date_wise' || by === 'status';
    document.getElementById('rpFrom').required = dates;
    document.getElementById('rpTo').required = dates;
    document.getElementById('rpStatus').required = by === 'status';
  }
  rpSearchBy(document.getElementById('rpBy').value);
</script>
@endpush
@endsection

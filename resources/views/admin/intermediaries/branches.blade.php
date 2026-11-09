@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
@endpush
<?php
use Carbon\Carbon;
?>
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
     
    @endslot
    
    @slot('breadcrumb_action_buttons')
    @if(Route::currentRouteName()=='loans-disbursed')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('disbursed-reports-download-Excel')}}'>Download Excel Disbursed</a></li>
    @elseif(Route::currentRouteName()=='loans-approved')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('approved-reports-download-Excel')}}'>Download Excel Approved</a></li>
    @elseif(Route::currentRouteName()=='loans-approved-cm')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('cm-reports-download-Excel')}}'>Download Excel CM list</a></li>
    @elseif(Route::currentRouteName()=='loans-approved-ca')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('ca-reports-download-Excel')}}'>Download Excel CA list</a></li>
    @elseif(Route::currentRouteName()=='loans-pending')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('qa-reports-download-Excel')}}'>Download Excel QA list</a></li>
    @elseif(Route::currentRouteName()=='loans-accepted')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('accepted-reports-download-Excel')}}'>Download Accepted By Bank</a></li>
    @elseif(Route::currentRouteName()=='loans-rejected')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('rejected-reports-download-Excel')}}'>Download Rejected Loans</a></li>
    @elseif(Route::currentRouteName()=='loans-cancelled')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('cancelled-reports-download-Excel')}}'>Download Cancelled Loans</a></li>
    @elseif(Route::currentRouteName()=='posted-cbs')
    <li>   <a  class="btn btn-outline-secondary" href='{{route('posted-cbs-download-Excel')}}'>Download Posted To CBS</a></li>
   
    @else
    <li>   <a  class="btn btn-outline-secondary" href='{{route('policies-reports-download-Excel')}}'>Download Excel All Loans</a></li>
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li>

    @endif

    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li> -->
    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">

                  <div class="card-body">
                  @foreach ($errors->all() as $error)
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        {{ $error }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  @endforeach
                  
                  @if($message = Session::get('success'))
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        {!! $message !!}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					@endif

                      <style>
                        .lt-wrap{--lt-blue:#0d47a1;--lt-orange:#f28c28;--lt-ink:#1f2937;--lt-muted:#6b7280;--lt-line:#e5e9f2;--lt-head:#f4f7fc;}
                        .lt-toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px;}
                        .lt-toolbar h5{margin:0;font-weight:700;color:var(--lt-ink);}
                        .lt-toolbar .lt-count{background:#e8effb;color:var(--lt-blue);border-radius:20px;padding:3px 12px;font-size:12px;font-weight:600;}
                        .lt-legend{font-size:11.5px;color:var(--lt-muted);}
                        .lt-legend b{color:var(--lt-ink);}
                        .lt-scroll{border:1px solid var(--lt-line);border-radius:10px;overflow:auto;max-height:72vh;}
                        .lt-table{width:100%;margin:0;border-collapse:separate;border-spacing:0;font-size:12.5px;color:var(--lt-ink);}
                        .lt-table thead th{position:sticky;top:0;z-index:2;background:var(--lt-head);color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:11px 12px;border-bottom:2px solid var(--lt-line);white-space:nowrap;}
                        .lt-table tbody td,.lt-table tbody th{padding:10px 12px;border-bottom:1px solid var(--lt-line);vertical-align:middle;white-space:nowrap;}
                        .lt-table tbody tr:nth-child(even){background:#fafbfd;}
                        .lt-table tbody tr:hover{background:#eef4ff;}
                        .lt-table .lt-num{color:var(--lt-muted);font-weight:600;}
                        .lt-table .lt-right{text-align:right;}
                        .lt-person{display:flex;align-items:center;gap:10px;}
                        .lt-avatar{flex:0 0 34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--lt-blue),#1976d2);color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;}
                        .lt-person a{color:var(--lt-blue);font-weight:600;}
                        .lt-person a:hover{color:var(--lt-orange);}
                        .lt-sub{display:block;color:var(--lt-muted);font-size:11.5px;margin-top:2px;}
                        .lt-mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;}
                        .lt-date{font-weight:600;}
                        .lt-time{color:var(--lt-muted);font-weight:400;}
                        .lt-wait{display:inline-block;margin-top:3px;font-size:10.5px;padding:1px 7px;border-radius:10px;background:#fff4e8;color:#b45f06;}
                        .lt-amount{font-weight:700;font-variant-numeric:tabular-nums;}
                        .lt-amount small{color:var(--lt-muted);font-weight:500;margin-right:3px;}
                        .lt-pill{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
                        .lt-pill:before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
                        .lt-pill.approved{background:#e3f2fd;color:#0d47a1;}
                        .lt-pill.disbursed{background:#e6f6ee;color:#137a43;}
                        .lt-pill.pending{background:#f1f3f6;color:#55606e;}
                        .lt-pill.other{background:#fdecec;color:#c62828;}
                        .lt-chip{display:inline-block;padding:2px 8px;border-radius:6px;background:#f1f5f9;color:#334155;font-size:11px;font-weight:600;}
                        .lt-chip.type{background:#fff4e8;color:#b45f06;}
                        .lt-muted{color:#9aa3af;font-style:italic;}
                        .lt-empty{text-align:center;padding:40px 10px !important;color:var(--lt-muted);}
                        .lt-pager{margin-top:14px;}
                      </style>

                      @php
                        $tz = 'Africa/Dar_es_Salaam';
                        $isPaged = $loans instanceof \Illuminate\Pagination\AbstractPaginator;
                        $startNo = $isPaged ? (int) $loans->firstItem() : 1;
                        $total = $loans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $loans->total() : count($loans);
                      @endphp

                      <div class="lt-wrap">
                        <div class="lt-toolbar">
                          <div class="d-flex align-items-center gap-2">
                            <h5>Loan Applications</h5>
                            <span class="lt-count">{{ number_format($total) }} record{{ $total == 1 ? '' : 's' }}</span>
                          </div>
                          <div class="lt-legend">
                            <b>Applied (ESS)</b> = when the employee applied on ESS Utumishi &nbsp;·&nbsp;
                            <b>Received at MCB</b> = when the application reached MCB
                          </div>
                        </div>

                        <div class="lt-scroll">
                          <table class="lt-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Applicant</th>
                                <th>Application No. / Loan ID</th>
                                <th>Applied (ESS)</th>
                                <th>Received at MCB</th>
                                <th class="lt-right">Amount</th>
                                <th class="lt-right">Processing Fee</th>
                                <th>Loan Type</th>
                                <th>Loan Status</th>
                                <th>Transaction Status</th>
                                <th>Approved Date</th>
                              </tr>
                            </thead>
                            <tbody>
                            @forelse ($loans as $loan)
                              @php
                                $fullName = trim(preg_replace('/\s+/', ' ', strtoupper(($loan['FIRST_NAME'] ?? '').' '.($loan['MIDDLE_NAME'] ?? '').' '.($loan['LAST_NAME'] ?? ''))));
                                $initials = strtoupper(substr($loan['FIRST_NAME'] ?? '', 0, 1).substr($loan['LAST_NAME'] ?? '', 0, 1));

                                // ESS does not send a separate "applied" date; ESS application numbers are "ESS" + the epoch time (ms) of the application
                                $appliedAt = null;
                                if (preg_match('/^ESS(\d{13})$/', (string) $loan['APPLICATION_NUMBER'], $m)) {
                                    $appliedAt = Carbon::createFromTimestampMs((int) $m[1])->setTimezone($tz);
                                }

                                // APPLICATION_DATE is stored by the gateway in local (Dar es Salaam) time when the request reaches MCB
                                $receivedAt = !empty($loan['APPLICATION_DATE'])
                                    ? Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($loan['APPLICATION_DATE'])->format('Y-m-d H:i:s'), $tz)
                                    : null;

                                $wait = null;
                                if ($appliedAt && $receivedAt && $receivedAt->gte($appliedAt)) {
                                    $mins = $appliedAt->diffInMinutes($receivedAt);
                                    $wait = $mins < 1 ? 'same minute' : ($mins < 60 ? $mins.' min later' : ($mins < 1440 ? floor($mins / 60).' hr later' : floor($mins / 1440).' day(s) later'));
                                }

                                $txStatus = $loan['TRANSACTION_STATUS'];
                                $pillClass = match ($txStatus) { 'Approved' => 'approved', 'Disbursed' => 'disbursed', 'Pending' => 'pending', default => 'other' };
                              @endphp
                              <tr>
                                <th scope="row" class="lt-num">{{ $startNo + $loop->index }}</th>
                                <td>
                                  <div class="lt-person">
                                    <span class="lt-avatar">{{ $initials ?: '?' }}</span>
                                    <div>
                                      <a href="{{ Route('staff-profile', ['id' => $loan->ID]) }}">{{ $fullName }}</a>
                                      <span class="lt-sub"><i class="fa fa-phone"></i> {{ $loan['MSISDN'] ?: '—' }}</span>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <span class="lt-mono">{{ $loan['APPLICATION_NUMBER'] }}</span>
                                  <span class="lt-sub lt-mono">{{ $loan['LOAN_ID'] ?: 'No loan ID yet' }}</span>
                                </td>
                                <td>
                                  @if($appliedAt)
                                    <span class="lt-date">{{ $appliedAt->format('d M Y') }}</span> <span class="lt-time">{{ $appliedAt->format('H:i') }}</span>
                                    <span class="lt-sub">{{ $appliedAt->diffForHumans() }}</span>
                                  @else
                                    <span class="lt-muted">Not provided</span>
                                  @endif
                                </td>
                                <td>
                                  @if($receivedAt)
                                    <span class="lt-date">{{ $receivedAt->format('d M Y') }}</span> <span class="lt-time">{{ $receivedAt->format('H:i:s') }}</span>
                                    <span class="lt-sub">{{ $receivedAt->diffForHumans() }}</span>
                                    @if($wait)<span class="lt-wait" title="Time between applying on ESS and reaching MCB">{{ $wait }}</span>@endif
                                  @else
                                    <span class="lt-muted">Not set</span>
                                  @endif
                                </td>
                                <td class="lt-right"><span class="lt-amount"><small>TZS</small>{{ number_format((float) $loan['REQUESTED_AMOUNT'], 2, '.', ',') }}</span></td>
                                <td class="lt-right"><span class="lt-amount"><small>TZS</small>{{ number_format((float) $loan['PROCESSING_FEE'], 2, '.', ',') }}</span></td>
                                <td>@if($loan['LOAN_TYPE'])<span class="lt-chip type">{{ $loan['LOAN_TYPE'] }}</span>@else<span class="lt-muted">—</span>@endif</td>
                                <td>@if($loan['LOAN_STATUS'])<span class="lt-chip">{{ $loan['LOAN_STATUS'] }}</span>@else<span class="lt-muted">—</span>@endif</td>
                                <td><span class="lt-pill {{ $pillClass }}">{{ $txStatus ?: 'Unknown' }}</span></td>
                                <td>
                                  @if(!empty($loan['APPROVED_DATE']))
                                    <span class="lt-date">{{ Carbon::parse($loan['APPROVED_DATE'])->format('d M Y') }}</span> <span class="lt-time">{{ Carbon::parse($loan['APPROVED_DATE'])->format('H:i:s') }}</span>
                                  @else
                                    <span class="lt-muted">Not set</span>
                                  @endif
                                </td>
                              </tr>
                            @empty
                              <tr><td colspan="11" class="lt-empty"><i class="fa fa-inbox fa-2x d-block mb-2"></i>No loan applications found.</td></tr>
                            @endforelse
                            </tbody>
                          </table>
                        </div>

                        @if($isPaged)
                        <div class="lt-pager">{{ $loans->links() }}</div>
                        @endif
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Branch Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="v1/intermediary/branches/add">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label" >Branch Name</label>
                                <input class="form-control" type="text" value="{{ old('name') }}" required  name="name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Branch Type</label><br>
                                <input type="radio" checked class="radio_animated" value="Branch" name="type"> Branch
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="Head Office" name="type"> Head Office
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <label class="col-form-label" >Branch Street</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="street"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                           
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW MODAL END -->

  <!-- SEARCH MODAL START -->
  <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="display: none;">
 
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Search Applicants</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <!-- <form method="post" action="aplications/search"> -->
                <form method="post" action="{{ url()->current() }}">
                    @csrf

                    <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="col-form-label">Search By</label><br>
                            <input type="radio" checked class="radio_animated" value="id_number" name="search_by" id="search_by" onChange="searchBy(this)"> Application No.
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="loan_id" name="search_by" id="search_by" onChange="searchBy(this)"> Loan ID.
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="phone_number" name="search_by" id="search_by" onChange="searchBy(this)"> Phone No.
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="name" name="search_by" id="search_by" onChange="searchBy(this)"> Name
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value="date_wise" name="search_by" id="search_by" onChange="searchBy(this)"> Date wise
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                        <div class="col-lg-12" id="start_date_container" style="display: none;">
                        <label class="col-form-label">Start Date</label><br>
                        </div>
                            <input class="form-control" type="text" value="{{ old('reference_number') }}" maxlength="20" required id="reference_number">
                        </div>
                    </div>
                    <div class="col-lg-12" id="end_date_container" style="display: none;">
                        <div class="form-group">
                        <label class="col-form-label">End Date</label><br>
                            <input class="form-control" type="date" id="end_date" placeholder="Enter End Date" name="end_date">
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Search</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

 @push('scripts')
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
  <script>
    document.getElementById("reference_number").placeholder = "Enter user's ID Number ...";
    document.getElementById('reference_number').name = 'id_number';

    function searchBy(search_by) {
        // Hide the end date container by default
        document.getElementById("end_date_container").style.display = "none";
        document.getElementById("start_date_container").style.display = "none";
       
        
        document.getElementById("end_date").required = false;  // Make the end date optional unless needed

        if (search_by.value == "id_number") {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter Loan Application Number ...";
            document.getElementById('reference_number').name = 'id_number';
        } else if (search_by.value == "loan_id") {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter Loan ID (e.g MCB ...";
            document.getElementById('reference_number').name = 'loan_id';
        } else if (search_by.value == "phone_number") {
            document.getElementById("reference_number").maxlength = "10";
            document.getElementById("reference_number").type = "number";
            document.getElementById("reference_number").placeholder = "Enter user's Phone (e.g. 0745821080) ...";
            document.getElementById('reference_number').name = 'phone_number';
        } else if (search_by.value == "date_wise") {
            document.getElementById("reference_number").type = "date";
            document.getElementById("reference_number").placeholder = "Enter Start Date ...";
            document.getElementById('reference_number').name = 'start_date';

            // Show the end date input field
            document.getElementById("end_date_container").style.display = "block";
            document.getElementById("start_date_container").style.display = "block";
            
            document.getElementById("end_date").required = true;  // Make the end date required
        } else {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter user's First Name or Middle Name or Last Name ...";
            document.getElementById('reference_number').name = 'cname';
        }
}

 </script>
  @endpush
@endsection

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

                      <p>
                      <div class="table-responsive">
                       
                      
						<table class="table table-xs">
							<thead>
								<tr>
                               
                                <th scope="col">#</th>
                                    <th scope="col" > Customer name</th>
                                    <th scope="col" > Loan amount</th>
                                    <th scope="col" > Loan Account</th>
                                    <th scope="col" > Period</th>
                                    <th scope="col" > Bank Branch</th>
                                    <th scope="col" > Region</th>
                                    <th scope="col" > District</th>
                                    <th scope="col" > Loan Purpose</th>
                                    <th scope="col" > Application Date</th>
                                    <th scope="col" > Disbursement date</th>
                                    <th scope="col" > Application Number</th>
                                    <th scope="col" > Deposit Account number</th>
                                    <th scope="col" > Phone number</th>
                                    <th scope="col" > Vote number</th>
                                    <th scope="col" > Vote name</th>
                                    <th scope="col" > Deductible Amount</th>
                                    <th scope="col" > Loan processing fee</th>
                                    <th scope="col" > Insurance</th>
                                    <th scope="col" > Loan Status</th>
								</tr>
							</thead>
							<tbody>
                          
                            @foreach ($loans as $loan)
								<tr>
                                <th scope="row">{{$loop->index + 1}}.</th>
            <td><a href="{{ Route('staff-profile', ['id' => $loan->ID])}}"><small>{{strtoupper($loan['FIRST_NAME'])}}  {{strtoupper($loan['MIDDLE_NAME'])}} {{strtoupper($loan['LAST_NAME'])}}</small></a></td>

            <td>{{number_format($loan['REQUESTED_AMOUNT'], 2,'.',',')}}</td>
            <td>{{$loan['DISBURSEMENT_RECEIPT']}}</td>
            <td>{{$loan['TENURE']}}</td>
            <td>{{$loan['INIT_BRANCH']}}</td>
            <td></td>
            <td></td>
            <td>{{$loan['LOAN_PURPOSE']}}</td>
            @if(isset($loan['APPLICATION_DATE']))
            <td>{{$loan['APPLICATION_DATE']->format('d M Y H:i:s')}}</td>   
            @else
            <td></td>
            @endif
            @if(isset($loan['LOAN_DISBURSED_DATE']))
            <td>{{$loan['LOAN_DISBURSED_DATE']->format('d M Y H:i:s')}}</td>   
            @else
            <td></td>
            @endif
            <td>{{$loan['APPLICATION_NUMBER']}}</td>
            <td>{{$loan['BANK_ACCOUNT_NUMBER']}}</td>
            <td>{{$loan['MSISDN']}}</td>
            <td>{{$loan['VOTE_CODE']}}</td>
            <td>{{$loan['VOTE_NAME']}}</td>
            
            <td>{{number_format($loan['MONTHLY_DEDUCTION'], 2,'.',',')}}</td>
            <td>{{number_format($loan['PROCESSING_FEE'], 2,'.',',')}}</td>
            <td>{{number_format($loan['INSURANCE'], 2,'.',',')}}</td>
            <td>{{$loan['LOAN_STATUS']}}</td>     
								</tr>
                                @endforeach
							</tbody>
						</table>
<br/>

                        {{ $loans->links() }}
                        </div>
                        
					</div>
                      </p>
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
  <!-- SEARCH MODAL START -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Search Applicants</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form method="post" action="{{ url()->current() }}">
                    @csrf

                    <div class="row">
                        
                        <!-- Search by -->
                        <div class="col-lg-12 mb-3">
                            <label class="col-form-label">Search By</label><br>

                            <input type="radio" checked value="id_number" name="search_by" onchange="searchBy(this)"> Application No.
                            &nbsp;&nbsp;
                            <input type="radio" value="loan_id" name="search_by" onchange="searchBy(this)"> Loan ID
                            &nbsp;&nbsp;
                            <input type="radio" value="phone_number" name="search_by" onchange="searchBy(this)"> Phone No
                            &nbsp;&nbsp;
                            <input type="radio" value="name" name="search_by" onchange="searchBy(this)"> Name
                            &nbsp;&nbsp;
                            <input type="radio" value="date_wise" name="search_by" onchange="searchBy(this)"> Date Wise
                            &nbsp;&nbsp;
                            <input type="radio" value="status" name="search_by" onchange="searchBy(this)"> Status
                        </div>

                        <!-- Reference Input -->
                        <div class="col-lg-12 mb-3">
                           
                            <input class="form-control" type="text" id="reference_number" name="id_number" required>
                        </div>

                        <!-- Start Date -->
                        <div class="col-lg-12 mb-3" id="start_date_container" style="display:none;">
                            <label class="col-form-label">Start Date</label>
                            <input class="form-control" type="date" id="start_date" name="start_date">
                        </div>

                        <!-- End Date -->
                        <div class="col-lg-12 mb-3" id="end_date_container" style="display:none;">
                            <label class="col-form-label">End Date</label>
                            <input class="form-control" type="date" id="end_date" name="end_date">
                        </div>

                        <!-- Status Dropdown -->
                        <div class="col-lg-12 mb-3" id="status_container" style="display:none;">
                            <label class="col-form-label">Select Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">--Choose status--</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit">Search</button>
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

    function searchBy(selection) {
    const ref = document.getElementById("reference_number");
    const startContainer = document.getElementById("start_date_container");
    const endContainer = document.getElementById("end_date_container");
    const statusContainer = document.getElementById("status_container");
    const startInput = document.getElementById("start_date");
    const endInput = document.getElementById("end_date");

    // 1️⃣ Hide everything by default
    ref.style.display = "none";
    ref.required = false;
    ref.name = "";

    startContainer.style.display = "none";
    endContainer.style.display = "none";
    statusContainer.style.display = "none";
    startInput.required = false;
    endInput.required = false;

    // 2️⃣ Handle each search type
    switch (selection.value) {
        case "id_number":
            ref.style.display = "block";
            ref.type = "text";
            ref.placeholder = "Enter Loan Application Number...";
            ref.name = "id_number";
            ref.required = true;
            break;

        case "loan_id":
            ref.style.display = "block";
            ref.type = "text";
            ref.placeholder = "Enter Loan ID...";
            ref.name = "loan_id";
            ref.required = true;
            break;

        case "phone_number":
            ref.style.display = "block";
            ref.type = "number";
            ref.placeholder = "Enter user's Phone...";
            ref.maxLength = 10;
            ref.name = "phone_number";
            ref.required = true;
            break;

        case "name":
            ref.style.display = "block";
            ref.type = "text";
            ref.placeholder = "Enter user's Name...";
            ref.name = "cname";
            ref.required = true;
            break;

        case "date_wise":
            startContainer.style.display = "block";
            endContainer.style.display = "block";
            startInput.required = true;
            endInput.required = true;
            break;

        case "status":
            statusContainer.style.display = "block";
            startContainer.style.display = "block";
            endContainer.style.display = "block";
            startInput.required = true;
            endInput.required = true;
            break;
    }
}


   
 </script>
  @endpush
@endsection

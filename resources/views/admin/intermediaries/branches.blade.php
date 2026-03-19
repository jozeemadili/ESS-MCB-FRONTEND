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
        
									<th scope="col">Names</th>
                                    <th scope="col">Application Date</th>
                                    <th scope="col">APPLICATION NUMBER</th>
                                    <th scope="col">mobile</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Loan ID</th>
                                    <th scope="col">transaction Status</th>
                                    
                                    <th scope="col">loan Status</th> 
                                    <th scope="col">loan Type</th> 
                                    <th scope="col">proccesing Fee</th> 
                                    <th scope="col">Approved Date</th> 
								</tr>
							</thead>
							<tbody>
                          
                            @foreach ($loans as $loan)
								<tr>
									<th scope="row">{{$loop->index + 1}}.</th>
									<td><a href="{{ Route('staff-profile', ['id' => $loan->ID])}}"><small>{{strtoupper($loan['FIRST_NAME'])}}  {{strtoupper($loan['MIDDLE_NAME'])}} {{strtoupper($loan['LAST_NAME'])}}</small></a></td>
                                    @if(isset($loan['APPLICATION_DATE']))
                                    <td>{{$loan['APPLICATION_DATE']->format('d M Y H:i:s')}} | <i>{{Carbon::parse($loan['APPLICATION_DATE'])->diffForHumans()}}</i></td>   
                                    @else
                                    <td></td>
                                    @endif
                                    <td>{{$loan['APPLICATION_NUMBER']}}</td>
                                    <td>{{$loan['MSISDN']}}</td>
                                    <td>{{number_format($loan['REQUESTED_AMOUNT'], 2,'.',',')}}</td>
                                    <td>{{$loan['LOAN_ID']}}</td>
                                    <td>
                                    @if($loan['TRANSACTION_STATUS'] === 'Approved')
                                    <span class="badge badge-primary rounded-pill">{{ $loan['TRANSACTION_STATUS'] }}</span> | 
                                    @if(isset($loan['APPROVED_DATE']))
                                    {{$loan['APPROVED_DATE']->format('d M Y H:i:s')}}
                                    @else
                                    Not Set
                                    @endif
                                    @elseif($loan['TRANSACTION_STATUS'] === 'Disbursed')
                                    <span class="badge badge-info rounded-pill">{{ $loan['TRANSACTION_STATUS'] }}</span>
                                    @elseif($loan['TRANSACTION_STATUS'] === 'Pending')
                                    <span class="badge badge-secondary rounded-pill">{{ $loan['TRANSACTION_STATUS'] }}</span>
                                    @else
                                    <span class="badge badge-danger rounded-pill">{{ $loan['TRANSACTION_STATUS'] }}</span>
                                    @endif
                                    </td>
                                    <td>{{$loan['LOAN_STATUS']}}</td>
                                    <td>{{$loan['LOAN_TYPE']}}</td>
                                    <td>{{number_format($loan['PROCESSING_FEE'], 2,'.',',')}}</td>
                                 
                                    
                                    <td> @if(isset($loan['APPROVED_DATE']))
                                    {{$loan['APPROVED_DATE']->format('d M Y H:i:s')}}
                                    @else
                                    Not Set
                                    @endif</td>
                                   
                                          
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

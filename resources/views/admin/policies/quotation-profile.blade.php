@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot

    @slot('breadcrumb_action_buttons')

    @if(($quotation['user']['company']['id'] == Auth::user()->company->id || $quotation['company_id'] == Auth::user()->company->id))
      @if($quotation->status == 'Accepted' && count($quotation->policies)<=0)
        <li><button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#newPolicy">Policy <i class="icofont icofont-file-document"></i></button></li>
      @endif
      @if($quotation->status == 'Accepted' && count($quotation->policies)>0 && count($quotation->quotations)<=0)
        @if($quotation->policies[0]->status == 'Accepted' && $quotation->end_date >= date('Y-m-d H:i:s'))
          <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#endorse">Endorse <i class="icofont icofont-edit"></i></button></li>
        @endif
      @endif
      @if($quotation->status == 'Accepted'  && $quotation->end_date >= date('Y-m-d H:i:s')  && count($quotation->quotations)<=0)
        @if(count($quotation->policies) > 0)
          @if($quotation->policies[0]->status == 'Accepted')
            <li><button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#newClaim">Claim <i class="icofont icofont-whisle"></i></button></li>
          @endif
        @endif
      @endif
    @endif

    

    <li> <a href='{{ Route('quotation-download', ['id' => $quotation->id]) }}' class="btn btn-outline-primary">Print <i class="icofont icofont-printer"></i></a> </li>
   
    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">

    @foreach ($errors->all() as $error)
    <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
      <i class="icon-info-alt txt-danger"></i>
          {{ $error }}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
      </div><br />
    @endforeach

    @if($message = Session::get('error'))
    <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
      <i class="icon-info-alt txt-danger"></i>
      {!! $message !!}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
      </div><br />
    @endif
    
    @if($message = Session::get('success'))
      <div class="alert alert-success outline alert-dismissible fade show" role="alert">
      <i class="icofont icofont-check-circled"></i>
          {!! $message !!}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
      </div>
      <br />
     @endif

    <div class="row">
      @livewire('components.results.quotation', ['quotation' => $quotation])  
    </div>
  </div>

 <!-- NEW ENDORSEMENT MODAL START -->
 <div class="modal fade" id="endorse" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-fullscreen" role="document">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Covernote Endorsement</h5>
              <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
          </div>
          <div class="modal-body">
              <div style="padding-right: 2em;padding-left: 2em;">
                  @livewire('components.quotations.endorsement', ['quotation' => $quotation])   
              </div>
          </div>
      </div>
  </div>
  </div>
<!-- NEW ENDORSEMENT MODAL END -->

 <!-- NEW ENDORSEMENT MODAL START -->
 <div class="modal fade" id="newPolicy" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Policy Submission</h5>
              <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
          </div>
          <div class="modal-body">
              <div style="padding-right: 2em;padding-left: 2em;">
                <form action="{{ Route('quotation-profile-add-policy') }}" method="post">
                  @csrf
                <div class="row">
                  <input type="hidden" name="quotation_id" value="{{ $quotation->id }}" />
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Policy Operative Clause</label>
                      <textarea class="form-control" name="policy_operative_clause" value={{ old('policy_operative_clause') }} placeholder="Enter Policy Operative Clause ..." required></textarea>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Policy Special Conditions</label>
                      <textarea class="form-control" name="special_conditions" value={{ old('special_conditions') }} placeholder="Enter Policy Special Conditions .." required></textarea>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Policy Exclusions</label>
                      <textarea class="form-control" name="exclusions" value={{ old('exclusions') }} placeholder="Enter Policy Exclusions ..." required></textarea>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
            <button class="btn btn-primary" type="submit" >Confirm & Submit</button>
            </form>
        </div>
      </div>
  </div>
  </div>
<!-- NEW ENDORSEMENT MODAL END -->

 <!-- NEW CLAIM MODAL START -->
 <div class="modal fade" id="newClaim" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Raise a Claim</h5>
            <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
        </div>
        <div class="modal-body">
            <form method="post" action="{{ Route('claim-motification-registration') }}">
                @csrf
                <div class="row">
                  @if(count($quotation->policies)>0)
                  <input type="hidden" name="policy_id" value="{{ $quotation->policies[0]->id }}" />
                  @endif
                    <div class="col-lg-6">
                        <div class="form-group">
                          <label class="col-form-label" >Loss Date</label>
                          <input class="form-control" type="datetime-local" data-language="en" value="{{ old('loss_date') }}"  name="loss_date" >
                      </div>
                        <div class="form-group">
                            <label class="col-form-label" >Report Date</label>
                            <input class="form-control" type="datetime-local" data-language="en" value="{{ old('report_date') }}" required  name="report_date">
                        </div>
                        <div class="form-group">
                          <label class="col-form-label" >Loss Nature</label>
                          <select class="form-select" required value="{{ old('loss_nature_id') }}" name="loss_nature_id">
                            <option value="">--- Choose Loss Nature ---</option>   
                            @foreach ($lossNatures as $lossNature)
                            <option value="{{ $lossNature->id }}">{{ $lossNature->title }} - {{ $lossNature->description }}</option>
                            @endforeach 
                          </select>
                        </div>

                        <div class="form-group">
                          <label class="col-form-label" >Loss Type</label>
                          <select class="form-select" required value="{{ old('loss_type_id') }}" name="loss_type_id">
                          <option value="">--- Choose Loss Type ---</option>   
                            @foreach ($lossTypes as $lossType)
                            <option value="{{ $lossType->id }}">{{ $lossType->title }} - {{ $lossType->description }}</option>
                            @endforeach 
                          </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                      @livewire('components.regions-districts-wards')
                      <div class="form-group">
                        <label class="col-form-label">Claim Dully Filled ?</label><br>
                        <input type="radio" checked class="radio_animated" value="Y" name="claim_form_dully_filled"> Yes
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" class="radio_animated" value="N" name="claim_form_dully_filled"> No
                    </div>
                    </div>
                </div>
            
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
            <button class="btn btn-primary" type="submit" >Confirm & Submit</button>
            </form>
        </div>
    </div>
</div>
  </div>
<!-- NEW CLAIM MODAL END -->


 <!-- NEW PAYMENT MODAL START -->
 <div class="modal fade" id="newPaymentModal" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">New Payment</h5>
              <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
          </div>
          <div class="modal-body">
              <div style="padding-right: 2em;padding-left: 2em;">
                  @livewire('components.payments.newpayment', ['quotation' => $quotation])
              </div>
          </div>
      </div>
  </div>
  </div>
<!-- NEW PAYMENT MODAL END -->


 <!-- MODIFY QUOTATION START -->
 <div class="modal fade" id="modifyQuotation" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Modify Quotation Details</h5>
              <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
          </div>
          <div class="modal-body">
            <form method="post" action="{{ Route('policies-quotations-modify') }}">
              @csrf
              <div style="padding-right: 2em;padding-left: 2em;">
                  <input type="hidden" name="previous_quotation_id" value="{{ $quotation['id'] }}">
                  <div class="form-group">
                    <label>Sum Assured</label> 
                    <input type="number" min="100000" step="0.01" class="form-control" placeholder="Property Value ..." name="sum_insured" value="{{ $quotation['sum_insured'] }}" required> 
                  </div>  
                  <div class="form-group">
                    <label>Start Date</label> 
                    <input type="date"  class="form-control" placeholder="Start Date ..." name="start_date" value="{{ date_format(date_create($quotation['start_date']), 'Y-m-d') }}" required> 
                  </div>  
                  <div class="form-group">
                    <label>End Date</label> 
                    <input type="date"  class="form-control" placeholder="End Date ..." name="end_date" value="{{ date_format(date_create($quotation['end_date']), 'Y-m-d') }}" required> 
                  </div>  
              </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
            <button class="btn btn-primary" type="submit" >Confirm & Submit</button>
            </form>
        </div>
      </div>
  </div>
  </div>
<!-- MODIFY QUOTATION END -->

@endsection

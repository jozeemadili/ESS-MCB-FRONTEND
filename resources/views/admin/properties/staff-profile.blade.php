@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
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
      <div class="row">
        @if(isset($vehicle))
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-controls="vehicles" aria-selected="true"><i class="icofont icofont-user-alt-3"></i>Applicant Details</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" id="profile-top-tab" data-bs-toggle="tab" href="#quotations" role="tab" aria-controls="quotations" aria-selected="false"><i class="icofont icofont-paper"></i>Loans</a></li> -->
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#policies" role="tab" aria-controls="policies" aria-selected="false"><i class="icofont icofont-shield"></i>Repayments</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" id="claims-top-tab" data-bs-toggle="tab" href="#claimstab" role="tab" aria-controls="claims" aria-selected="false"><i class="icofont icofont-whisle"></i>Claims</a></li>
                        <li class="nav-item"><a class="nav-link" id="photos-top-tab" data-bs-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="false"><i class="icofont icofont-image"></i>Photos</a></li> -->
                    </ul>
      
                      <div class="tab-content" id="top-tabContent">

                        <div class="tab-pane fade active show" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab">
                          
                          <p>@livewire('components.vehicle.details', ['vehicle' => $vehicle, 'approval_datils' => $approval_datils])</p>
                        </div>

                       

                      </div>

                  </div>
              </div>
          </div>
          @else
          <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                       User Does not exit
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
          @endif
      </div>
  </div>




  @push('scripts')
  @endpush
@endsection
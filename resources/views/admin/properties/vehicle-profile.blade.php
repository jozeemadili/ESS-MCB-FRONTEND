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

    @slot('breadcrumb_action_buttons')
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">Request Quotation <i class="icofont icofont-plus-circle"></i></button></li>
    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-controls="vehicles" aria-selected="true"><i class="icofont icofont-bus-alt-3"></i>Vehicle Details</a></li>
                        <li class="nav-item"><a class="nav-link" id="profile-top-tab" data-bs-toggle="tab" href="#quotations" role="tab" aria-controls="quotations" aria-selected="false"><i class="icofont icofont-paper"></i>Quotations</a></li>
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#policies" role="tab" aria-controls="policies" aria-selected="false"><i class="icofont icofont-shield"></i>Policies</a></li>
                        <li class="nav-item"><a class="nav-link" id="claims-top-tab" data-bs-toggle="tab" href="#claimstab" role="tab" aria-controls="claims" aria-selected="false"><i class="icofont icofont-whisle"></i>Claims</a></li>
                        <li class="nav-item"><a class="nav-link" id="photos-top-tab" data-bs-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="false"><i class="icofont icofont-image"></i>Photos</a></li>
                    </ul>
      
                      <div class="tab-content" id="top-tabContent">

                        <div class="tab-pane fade active show" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab">
                          <p>@livewire('components.vehicle.details', ['vehicle' => $vehicle])</p>
                        </div>

                        <div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="profile-top-tab">
                          <p>@livewire('components.vehicle.quotations', ['vehicle' => $vehicle])</p>
                        </div>

                        <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="contact-top-tab">
                          <p>@livewire('components.vehicle.policies', ['vehicle' => $vehicle])</p>
                        </div>

                        <div class="tab-pane fade" id="claimstab" role="tabpanel" aria-labelledby="claims-top-tab">
                            <p>@livewire('components.vehicle.claims', ['vehicle' => $vehicle])</p>
                        </div>


                        <div class="tab-pane fade" id="photos" role="tabpanel" aria-labelledby="photos-top-tab">
                          <p>@livewire('components.vehicle.photos', ['vehicle' => $vehicle])</p>
                      </div>

                      </div>

                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW QUOTATION MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">New Quotation Form</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <div style="padding-right: 2em;padding-left: 2em;">
                    {{-- @livewire('components.quotations.motor', ['vehicle' => $vehicle, 'customer' => null, 'company' => (Auth::user()->role == 'Insurer Admin' || Auth::user()->role == 'Insurer Staff') ? Auth::user()->company : null])    --}}
                    @livewire('components.quotations.motor', ['vehicle' => $vehicle, 'customer' => null, 'company' => Auth::user()->company])   
                </div>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW QUOTATION MODAL END -->


  @push('scripts')
  @endpush
@endsection
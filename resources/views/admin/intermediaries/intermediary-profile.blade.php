@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/sweetalert2.css')}}">
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot

    @slot('breadcrumb_action_buttons')
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFloat">Float <i class="icofont icofont-plus-circle"></i></button></li>
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
                        <li class="nav-item"><a class="nav-link active" id="intermediary-tab" data-bs-toggle="tab" href="#intermediary" role="tab" aria-controls="intermediary" aria-selected="true"><i class="icofont icofont-info-circle"></i>Details</a></li>
                        <li class="nav-item"><a class="nav-link" id="risks-tab" data-bs-toggle="tab" href="#risks" role="tab" aria-controls="risks" aria-selected="true"><i class="icofont icofont-shield"></i>Risks</a></li>
                        <li class="nav-item"><a class="nav-link" id="profile-top-tab" data-bs-toggle="tab" href="#quotations" role="tab" aria-controls="quotations" aria-selected="false"><i class="icofont icofont-paper"></i>Quotations</a></li>
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#floats" role="tab" aria-controls="floats" aria-selected="false"><i class="icofont icofont-money"></i>Floats</a></li>
                    </ul>
      
                      <div class="tab-content" id="top-tabContent">

                        <div class="tab-pane fade active show" id="intermediary" role="tabpanel" aria-labelledby="intermediary-tab">
                          <p>  
                            <table class="table table-sm">
                              <tr><td>Code</td><th>{{ $intermediary->company->code }}</th></tr>
                              <tr><td>Name</td><th>{{ strtoupper($intermediary->company->name) }}</th></tr>
                              <tr><td>TIN</td><th>{{ $intermediary->company->tin }}</th></tr>
                              <tr><td>Registration Number</td><th>{{ $intermediary->company->registration_number }}</th></tr>
                              <tr><td>Licence Number</td><th>{{ $intermediary->company->license_number }}</th></tr>
                              <tr><td>Category</td><th><span class="badge badge-primary rounded-pill">{{ $intermediary->company->category }}</span></th></tr>
                              <tr><td>Email Address</td><th>{{ $intermediary->company->email_address }}</th></tr>
                              <tr><td>Phone Number</td><th>{{ $intermediary->company->phone_number }}</th></tr>
                              <tr><td>Postal Address</td><th>{{ $intermediary->company->postal_address }}</th></tr>
                              <tr><td>Contact Person</td><th>{{ $intermediary->company->contact_person }}</th></tr>
                              <tr><td>Registration Date</td><th>{{ $intermediary->company->create_at->format('d M Y, H:i:s') }}</th></tr>
                              <tr><td>Status</td><th><span class="badge badge-primary rounded-pill">{{ $intermediary->company->status }}</span></th></tr>
                            </table>
                          </p>
                        </div>

                        <div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="profile-top-tab">
                          <p> 
                              <div class="table-responsive">
                                @if(count($quotations)>0)
                                <table class="table table-xs">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Reference #</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Customer</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Start</th>
                                            <th scope="col">End</th>
                                            <th scope="col">Sticker</th>
                                            <th scope="col">Submitted At</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotations as $quotation)
                                        <tr>
                                            <th scope="row">{{$loop->index + 1}}.</th>
                                            <td><a href='#'>{{ $quotation->reference_number }}</a></td>
                                            <td>{!! $quotation->cover_note_type == 1 ? "<span class='badge badge-primary'>New</span>" : ($quotation->cover_note_type == 2 ? "<span class='badge badge-success'>Renew</span>" : "<span class='badge badge-dark'>Endorsement</span>") !!}</td>
                                            <td>{{strtoupper($quotation->customer->last_name)}}</td>
                                            <td><small>{{strtoupper($quotation->risk->product->name)}}</small></td>
                                            <td>{{$quotation->start_date->format('d/m/y')}}</td>
                                            <td>{{$quotation->end_date->format('d/m/y')}}</td>
                                            <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{$quotation->sticker_number}}</a></td>
                                            <td>{{$quotation->created_at->format('d M y, H:i')}}</td>
                                            <td>{{$quotation->status}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <br />
                            
                                @else 
                                <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                    <i class="icon-info-alt txt-danger"></i>
                                        No Records Found yet
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                                  </div>
                                @endif
                            </div>
                              <br />
                              {{ $quotations->links() }}
                          </p>
                        </div>

                        <div class="tab-pane fade" id="floats" role="tabpanel" aria-labelledby="contact-top-tab">
                          <p>
                            <div class="table-responsive">
                              @if(count($floats)>0)
                              <table class="table table-xs">
                                <thead>
                                <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">Prev. Balance</th>
                                      <th scope="col">New Balance</th>
                                      <th scope="col">Prev. Amount</th>
                                      <th scope="col">New Amount</th>
                                      <th scope="col">Added At</th>
                                      <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                      @foreach($floats as $float)
                                        <tr>
                                            <th scope="row">{{$loop->index + 1}}.</th>
                                            <td>{{number_format($float->previous_balance, 2, '.', ',')}}</td>
                                            <td>{{number_format($float->current_balance, 2, '.', ',')}}</td>
                                            <td>{{number_format($float->previous_amount, 2, '.', ',')}}</td>
                                            <td>{{number_format($float->current_amount, 2, '.', ',')}}</td>
                                            <td>{{$float->created_at->format('d M Y, H:i')}}</td>
                                            <td>{{$float->status}}</td>
                                        </tr>
                                      @endforeach
                                </tbody>
                              </table>
                                  @else 
                                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                      <i class="icon-info-alt txt-danger"></i>
                                        No Records Found yet
                                      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                                  </div>
                                  @endif
                            </div>
                          </p>
                        </div>


                        <div class="tab-pane fade" id="risks" role="tabpanel" aria-labelledby="risks-top-tab">
                          <p>
                            <div class="table-responsive">
                              @if(count($intermediary->insurer_intermediaries_risks)>0)
                              <table class="table table-xs">
                                <thead>
                                <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">PRODUCT</th>
                                      <th scope="col">Code</th>
                                      <th scope="col">Name</th>
                                      <th scope="col">Rate</th>
                                </tr>
                                </thead>
                                <tbody>
                                      @foreach($intermediary->insurer_intermediaries_risks as $risk)
                                        <tr>
                                            <th scope="row">{{$loop->index + 1}}.</th>
                                            <td>{{strtoupper($risk->risk->product->name)}}</td>
                                            <td>{{$risk->risk->code}}</td>
                                            <td>{{$risk->risk->name}}</td>
                                            <td>{{($risk->risk->premium_rate * 100)}}%</td>
                                        </tr>
                                      @endforeach
                                </tbody>
                              </table>
                                  @else 
                                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                      <i class="icon-info-alt txt-danger"></i>
                                        No Records Found yet
                                      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                                  </div>
                                  @endif
                            </div>
                          </p>
                      </div> 

                      </div>

                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW QUOTATION MODAL START -->
 <div class="modal fade" id="addFloat" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Intermediary Float Management</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <div style="margin:50px;">
                   @livewire('components.intermediaries.floats', ['intermediary' => $intermediary, 'floats' => $floats])
                </div>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW QUOTATION MODAL END -->


  @push('scripts')
  <script src="{{asset('assets/js/sweet-alert/sweetalert.min.js')}}"></script>
  <script>
    window.addEventListener('swal:modal', event => { 
        swal({
          title: event.detail.message,
          text: event.detail.text,
          icon: event.detail.type,
          buttons:false,
          customClass:'swal-wide'
        });
    });
      
    window.addEventListener('swal:confirm', event => { 
        swal({
          title: event.detail.message,
          text: event.detail.text,
          icon: event.detail.type,
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            window.livewire.emit('remove');
          }
        });
    });
     </script>
  @endpush
@endsection
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
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#{{ Route::currentRouteName() == 'policies-quotations-motor' ? 'newModal' : 'newModalNonMotor' }}">New <i class="icofont icofont-plus-circle"></i></button></li>
    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
          <div class="card">
              <div class="card-body">
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
                                  <th scope="col">Date</th>
                                  <th scope="col">Status</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($quotations as $quotation)
                              <tr>
                                  <th scope="row">{{$loop->index + 1}}.</th>
                                  <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{ $quotation->reference_number }}</a></td>
                                  <td>{!! $quotation->cover_note_type == 1 ? "<span class='badge badge-primary'>New</span>" : ($quotation->cover_note_type == 2 ? "<span class='badge badge-success'>Renew</span>" : "<span class='badge badge-dark'>Endorsement</span>") !!}</td>
                                  <td>{{strtoupper($quotation->customer->last_name)}}</td>
                                  <td><small>{{strtoupper($quotation->risk->product->name)}}</small></td>
                                  <td>{{$quotation->start_date->format('d/m/y')}}</td>
                                  <td>{{$quotation->end_date->format('d/m/y')}}</td>
                                  <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{$quotation->sticker_number}}</a></td>
                                  <td>{{$quotation->created_at->format('d/m/y')}}</td>
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
                </div>
                  </p>
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
                  {{-- @livewire('components.quotations.motor', ['customer' => null, 'vehicle' => null, 'company' => (Auth::user()->role == 'Insurer Admin' || Auth::user()->role == 'Insurer Staff') ? Auth::user()->company : null])    --}}
                  @livewire('components.quotations.motor', ['customer' => null, 'vehicle' => null, 'company' =>  Auth::user()->company])   
              </div>
          </div>
      </div>
  </div>
  </div>
<!-- NEW QUOTATION MODAL END -->



 <!-- NEW QUOTATION MODAL START -->
 <div class="modal fade" id="newModalNonMotor" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">New Quotation Form</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <div style="padding-right: 2em;padding-left: 2em;">
                    {{-- @livewire('components.quotations.non-motor', ['customer' => null, 'company' => (Auth::user()->role == 'Insurer Admin' || Auth::user()->role == 'Insurer Staff') ? Auth::user()->company : null])    --}}
                    @livewire('components.quotations.non-motor', ['customer' => null, 'company' => Auth::user()->company]) 
                </div>
            </div>
        </div>
    </div>
    </div>
  <!-- NEW QUOTATION MODAL END -->

@endsection

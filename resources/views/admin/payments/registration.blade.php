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
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New Payment <i class="icofont icofont-plus-circle"></i></button></li>
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
                      @if(count($payments)>0)
                      <table class="table table-xs">
                          <thead>
                              <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Reference #</th>
                                  <th scope="col">Expected</th>
                                  <th scope="col">Paid</th>
                                  <th scope="col">Outstanding</th>
                                  <th scope="col">Method</th>
                                  <th scope="col">Currency</th>
                                  <th scope="col">Date</th>
                                  <th scope="col">Status</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($payments as $payment)
                              <tr>
                                  <th scope="row">{{$loop->index + 1}}.</th>
                                  <td><a href='{{route('quotation-profile', ['id' => $payment->quotation_id])}}'>{{ $payment->reference_number }}</a></td>
                                  <td>{{ number_format($payment->expected_amount, 2,'.', ',') }}</td>
                                  <td>{{ number_format($payment->paid_amount, 2,'.', ',') }}</td>
                                  <td>{{ number_format($payment->outstanding_amount, 2,'.', ',') }}</td>
                                  <td>{{ $payment->method == 1 ? 'CASH' : ($payment->method == 2 ? 'CHEQUE' : ($payment->method == 3 ? 'EFT' : 'IPF')) }}</td>
                                  <td>{{$payment->currency_code}}</td>
                                  <td>{{$payment->created_at->format('d/m/y H:i')}}</td>
                                  <td>{{$payment->status}}</td>
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
                    {{ $payments->links() }}
                </div>
                  </p>
              </div>
          </div>
      </div>
  </div>


 <!-- NEW PAYMENT MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">New Payment</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <div style="padding-right: 2em;padding-left: 2em;">
                    @livewire('components.payments.newpayment', ['quotation' => null])
                </div>
            </div>
        </div>
    </div>
    </div>
  <!-- NEW PAYMENT MODAL END -->

@endsection

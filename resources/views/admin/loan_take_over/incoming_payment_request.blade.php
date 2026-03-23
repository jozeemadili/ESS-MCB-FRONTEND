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
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
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
                      {{ $payment_datails }}
                      @if(count($payment_datails)>0)
                      <table class="table table-xs">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Loan Number</th>
                                <th scope="col">Payment Date</th>
                                <th scope="col">Payment Reference Number</th>
                                <th scope="col">Total Payoff Amount</th>
                                <th scope="col">Payement Advice Attachemnt</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        @foreach ( $payment_datails as $p )
                        <tr>
                        <th scope="row">{{$loop->index + 1}}.</th>
                        <td>{{ App\Http\Controllers\API\Auth\CustomersController::resolveIdCustomerName($p->LOAN_NUMBER) }}</td>
                            <td>{{ $p->REASON ?? '-' }}</td>
                            <td>{{ $p->LOAN_NUMBER ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->PAYMENT_DATE)->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>{{ $p->PAYMENT_REFERENCE_NUMBER ?? '-' }}</td>
                            <td>{{ number_format($p->TOTAL_PAYOFF_AMOUNT, 2) }}</td>
                            <td>
                                @if(!empty($p->PAYMENT_ADVICE))
                                    <button 
                                        class="btn btn-outline-primary btn-xs"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pdfPreviewModal"
                                        onclick="showPDFInModal(`{{ $p->PAYMENT_ADVICE }}`)">
                                        Preview
                                    </button>
                                @else
                                    <span class="text-muted">No Attachment</span>
                                @endif
                            </td>
                            <td>
                            <td>
                              <a href="{{ Route('incoming-payament-profile', ['loan_id' => $p->LOAN_NUMBER])}}"  class="btn btn-outline-primary btn-xs" >Details</a>
                            </td>
                            
                        </tr>
                     @endforeach
							</tbody>
						</table>

                        @else 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Record Found yet
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



  @push('scripts')
  <!-- <script>
    function showPDFInModal(base64) {
      const pdfViewer = document.getElementById('pdfViewer');
      pdfViewer.src = base64;

    // Optional: Log the src to ensure it's correct
    console.log("PDF Source:", base64);
    }
</script> -->

<!-- <script>
    function showPDFInModal(adviceText) {
        const viewer = document.getElementById('adviceViewer');
        viewer.textContent = adviceText;
    }
</script> -->
<script>
    function showPDFInModal(adviceText) {
        const viewer = document.getElementById('adviceViewer');

        // Extract the block {4: ... }
        const matchBlock4 = adviceText.match(/{4:(.*)-}/s);
        let formatted = "";

        if (matchBlock4 && matchBlock4[1]) {
            let body = matchBlock4[1];

            // Format the SWIFT fields (e.g., :20:..., :23B:..., etc.)
            formatted = body.replace(/:(\d{2}[A-Z]?):/g, '\n:$1: ')
                            .replace(/\n:/g, '<br>:'); // Add HTML line breaks
        } else {
            formatted = "Could not parse PAYMENT_ADVICE content.";
        }

        viewer.innerHTML = formatted;
    }
</script>

  @endpush
@endsection

<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfPreviewModalLabel">Payment Advice Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: auto;">
  <div id="adviceViewer" style="white-space: pre-wrap; word-wrap: break-word; font-family: monospace;"></div>
</div>

    </div>
  </div>
</div>

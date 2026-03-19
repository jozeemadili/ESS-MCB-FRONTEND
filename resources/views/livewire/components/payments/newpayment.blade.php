<div>
    <div class="form-group" style="display : {{ $is_successfull == false ? '' : 'none' }}">
        <label>Search Quotation</label>
        <input type="text" class="form-control" placeholder="Search by Reference Number, Premium Amount, Risk Description or Sum Insured ..." wire:model="quotationquery">
        @if(count($quotations)>0)
        <div class="list-group">
            @foreach ($quotations as $quotation)
            <a wire:click="selectQuotation({{ $quotation }})" class="list-group-item list-group-item-action" href="javascript:void(0)" data-bs-original-title="">
                <div class="d-flex w-100 justify-content-between">
                    <small><b>{{ $quotation->reference_number }} - {{ ($quotation->risk->product->name) }} - {{ $quotation->risk->name }}</b></small>
                    <span class="badge badge-success rounded-pill counter">{{ $loop->index+1 }}</span>
            </div>
            <small class="text-muted">Sum Insured : {{ number_format($quotation->sum_insured, 2, '.', ',') }} | Total Premium Including Tax : {{ number_format($quotation->total_premium_including_tax, 2, '.', ',') }} <br />
            <small class="text-muted">{{ strtoupper($quotation->customer->first_name) }} {{ strtoupper($quotation->customer->last_name) }} <br /> <span class="badge badge-success rounded-pill">+255{{ $quotation->customer->mobile }}</span></small>
            </a>
            @endforeach
        </div>
        @endif
    </div>

  <div style="display : {{ $is_successfull == false ? '' : 'none' }}">
    <div class="ribbon-wrapper card">
        <div class="card-body">
            <div class="ribbon ribbon-clip ribbon-primary">Payment Method</div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="media p-15">
                            <div class="radio radio-primary me-3">
                                <input id="payment_method_1" type="radio" name="payment_method" wire:model="payment_method" value="1">
                                <label for="payment_method_1"></label>
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 mega-title-badge">CASH</h6>
                                <p><small>Physical cash</small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="media p-15">
                            <div class="radio radio-primary me-3">
                                <input id="payment_method_2" type="radio"  name="payment_method" wire:model="payment_method" value="2">
                                <label for="payment_method_2"></label>
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 mega-title-badge">CHEQUE</h6>
                                <p><small>Via Cheque</small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="media p-15">
                            <div class="radio radio-primary me-3">
                                <input id="payment_method_3" type="radio"  name="payment_method" wire:model="payment_method" value="3">
                                <label for="payment_method_3"></label>
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 mega-title-badge">EFT</h6>
                                <p>E - Money</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="media p-15">
                            <div class="radio radio-primary me-3">
                                <input id="payment_method_4" type="radio"  name="payment_method" wire:model="payment_method" value="4">
                                <label for="payment_method_4"></label>
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 mega-title-badge">IPF</h6>
                                <p>Via Loan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="col-lg-6" style="display: {{ $payment_method == 3 ? 'block' : 'none' }};">
            <div class="form-group">
                <label>Mobile Number (Push may be sent)</label>
                <div class="input-group">
                    <span class="input-group-text">255</span>
                    <input class="form-control" type="number" min="600000000" minlength="9" maxLength="9" placeholder="Mobile Phone Number ..." wire:model="payer_phone_number">
                </div>
            </div>
        </div>
        <div class="col-lg-6" style="display: {{ $payment_method == 3 ? 'block' : 'none' }};">
            <div class="form-group">
                <label>Financial Service Provider</label><br />
                <input  type="radio" class="radio_animated" checked name="fsp_type"  wire:model="fsp_type" value="Mpesa">M-Pesa &nbsp;
                <input  type="radio" class="radio_animated"  name="fsp_type" wire:model="fsp_type" value="TigoPesa">Tigo Pesa &nbsp;
                <input  type="radio" class="radio_animated"  name="fsp_type" wire:model="fsp_type" value="AirtelMoney">Airtel Money
            </div>
        </div>
    </div>

    <div class="row">
        <div class="{{ $payment_method == 1 ? 'col-lg-12' : 'col-lg-6' }}">
            <div class="form-group">
                <label>Cash Amount Received</label>
                <div class="input-group">
                    <span class="input-group-text">TZS</span>
                    <input class="form-control"  type="number" step="0.01" min="0" placeholder="Amount Received ..." wire:model="amount_received">
                </div>
                <small><blockquote class="blockquote" style="font-size: 12px;"><b>{{ number_format(floatVal($amount_received), 2, '.', ',') }} TZS</b></blockquote></small>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group" style="display: {{ $payment_method != 1 ? 'block' : 'none' }};">
                <label>Outstanding to be paid via <code>{{ $payment_method == 2 ? 'CHEQUE' : ($payment_method == 3 ? 'EFT' : 'IPF') }}</code></label>
                <div class="input-group">
                    <span class="input-group-text">TZS</span>
                    <input class="form-control" type="number"  step="0.01" min="0" placeholder="Amount to be Paid ..." wire:model="amount_tobe_paid">
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="row">
        <center>
            <div wire:loading.delay>
                <div class="loader-box">
                    <div class="loader-7" style="width: 80px; height:80px;"></div>
                </div>
                <h6 class="f-w-100">Processing Your Request ...</h6>
            </div>
                <div style="display : {{ $is_successfull == true ? '' : 'none' }}">
                    <i class="icofont icofont-check-circled" style="font-size:120px; color:green;"></i>
                    <br /><br />
                    <h5 class="f-w-100">Payment Successfully Processed.</h5>
                </div>

                @if($processing_response != null)
                <div style="display : {{ $is_successfull == false ? '' : 'none' }}">
                    <i class="icofont icofont-close-circled" style="font-size:120px; color:red;"></i>
                    <br /><br />
                    <h5 class="f-w-100">{{ $processing_response['message'] }}</h5>
                </div>
                @endif

        </center>
    </div>
    

    <hr /><br />
    <div style="display : {{ $is_successfull == false ? '' : 'none' }}">
    <span wire:loading.remove class="checkboxx checkbox-dark">
        <input id="inline-1" type="checkbox" wire:model="hasconfirmed">
        <label for="inline-1">I Affirm to Read and Confirm Payment Details Above &nbsp;&nbsp;</label>
      </span>
    <button wire:click="updatePayment" class="btn btn-outline-primary pull-right" {{ ($quotation != null  && $hasconfirmed != false && ($amount_received>0 || $amount_tobe_paid >0) ) ? '' : 'disabled' }}>Confirm and Pay</button>
    </div>

</div>

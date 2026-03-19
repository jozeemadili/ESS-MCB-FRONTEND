<form class="f1" method="post">
    
    <div class="f1-steps" style="display: {{ $is_successfull == true ? 'none' :'' }}">
      <div class="f1-progress">
        <div class="f1-progress-line" data-number-of-steps="3" style="width: {{ $progress }}%"></div>
      </div>
      <div class="f1-step {{ $information_step == "block" ? "active" : "" }}">
        <div class="f1-step-icon"><i class="icofont icofont-info"></i></div>
        <p>Endorsement Type</p>
      </div>
      <div class="f1-step {{ $pricing_step == "block" ? "active" : "" }}">
        <div class="f1-step-icon"><i class="icofont icofont-deal"></i></div>
        <p>Pricing</p>
      </div>
      <div class="f1-step  {{ $confirmation_step == "block" ? "active" : "" }}">
        <div class="f1-step-icon"><i class="icofont icofont-check"></i></div>
        <p>Confirmation</p>
      </div>
    </div>

    <fieldset style="display: {{ $information_step }};">

        <div class="row">
            <div class="card_radio">
                <input type="radio" class="radio_input" name="endorsement_type"  wire:model="endorsement_type" value="premium_change" id="premium_change">
                <label for="premium_change" class="radio_label">
                    <h5 class="radio_h5">Premium Changes</h5>
                        <p>If there are changes in Premium details like Risk (Upgrading or Downgrading of the Cover)</p>
                        <i class="icofont icofont-exchange" style="font-size:140px; color:#3989c6;"></i>
                </label>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="card_radio">
                <input type="radio" class="radio_input" name="endorsement_type"  wire:model="endorsement_type" value="cover_details_change" id="cover_details_change">
                <label for="cover_details_change" class="radio_label">
                    <h5 class="radio_h5">Cover Details Changes</h5>
                        <p>If there are changes in Covernote like Policy Holders details changed, vehicle ownership changeg etc</p>
                        <i class="icofont icofont-business-man" style="font-size:140px; color:cadetblue;"></i>
                </label>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="card_radio">
                <input type="radio" class="radio_input" name="endorsement_type"  wire:model="endorsement_type" value="cancellation" id="cancellation">
                <label for="cancellation" class="radio_label">
                    <h5 class="radio_h5">Cover Cancellation</h5>
                        <p>If you or a customer decided to discontinue or Cancel cover due to various reasons</p>
                        <i class="icofont icofont-pause" style="font-size:140px; color:red;"></i>
                </label>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>

    </fieldset>


    <fieldset style="display: {{ $pricing_step }};">
        @if(count($risks)>0)
        <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Sum Insured (Property Value)</label>
                <input class="form-control" type="number" min="0" name="sum_insured" wire:model="sum_insured">
            </div>
        </div>
        @foreach ($risks as $risk)
        <div class="col-lg-6">
        <div class="card">
            <div class="media p-20">
                <div class="radio radio-primary me-3">
                    <input id="{{ $risk->id }}" type="radio" name="risk_id" value="{{ $risk->id }}" wire:model="risk_id">
                    <label for="{{ $risk->id }}"></label>
                </div>
                <div class="media-body">
                    @if($quotation->vehicle_id != null)
                        <h6 class="mt-0 mega-title-badge">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $quotation->vehicle->sitting_capacity)['total_formated'] }} TZS<span class="badge badge-primary pull-right digits">{{ $risk->premium_rate * 100 }}%</span></h6>
                    @else 
                        <h6 class="mt-0 mega-title-badge">{{  App\Http\Controllers\API\Pricings\NonMotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted)['total_formated'] }} TZS ({{ $risk->premium_rate * 100 }}%)</h6>
                    @endif
                    <p>{{ $risk->name }}</p>
                </div>
            </div>
        </div>
        </div>
        @endforeach 
        </div>
        @endif

    </fieldset>


    <fieldset style="display: {{ $confirmation_step }};">
    <div class="ribbon-wrapper card">
        <div class="card-body">
         <div class="ribbon ribbon-clip ribbon-{{ $endorsement_type == 'premium_change' ? 'primary' : ($endorsement_type == 'cancellation' ? 'danger' : 'success') }}">{{ $endorsement_type == 'premium_change' ? 'About Changing Premium Details' : ($endorsement_type == 'cancellation' ? 'About Cancelling Covernote' : 'About Changing Covernote Details') }}</div>
         <div class="row">
            <div class="col-lg-6">
                @livewire('components.results.quotation-simple', ['quotation' => $quotation])
            </div>
            <div class="col-lg-6">
                
                <center>
                <div wire:loading.delay>
                    <div class="loader-box">
                        <div class="loader-7" style="width: 80px; height:80px;"></div>
                    </div>
                    <center><h6 class="f-w-100">Processing Your Request ...</h6></center>
                </div>
                </center>

                @if($is_successfull == false && $endorsement_type != 'premium_change')
                <div wire:loading.remove>
                <div class="loader-box">
                    <div class="loader-22" style="width: 80px; height:80px;"></div>
                </div>
                <br />
                    <center><h6 class="f-w-100">Waiting your Confirmation ...</h6></center>
                </div>
                @else 
                <!-- FOR PREMIUM CHANGE -->
                <div style="display: {{ $is_successfull == false ? '' : 'none' }}">
                    <div class="ribbon-wrapper card">
                        <div class="card-body">
                            <div class="ribbon ribbon-clip ribbon-dark">Payment</div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="media p-15">
                                            <div class="radio radio-primary me-3">
                                                <input id="payment_method_1" type="radio" {{ ($is_premium_changed == false || $is_premium_upgraded == false) ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="1">
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
                                                <input id="payment_method_2" type="radio" {{ ($is_premium_changed == false || $is_premium_upgraded == false) ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="2">
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
                                                <input id="payment_method_3" type="radio" {{ ($is_premium_changed == false || $is_premium_upgraded == false) ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="3">
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
                                                <input id="payment_method_4" type="radio" {{ ($is_premium_changed == false || $is_premium_upgraded == false) ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="4">
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
    
                    <div class="row">
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
                                    <input class="form-control" {{ ($is_premium_changed == false || $is_premium_upgraded == false) ? 'disabled':'' }} type="number" step="0.01" min="0" placeholder="Amount Received ..." wire:model="amount_received">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group" style="display: {{ $payment_method != 1 ? 'block' : 'none' }};">
                                <label>Outstanding to be paid via <code>{{ $payment_method == 2 ? 'CHEQUE' : ($payment_method == 3 ? 'EFT' : 'IPF') }}</code></label>
                                <div class="input-group">
                                    <span class="input-group-text">TZS</span>
                                    <input class="form-control" type="number" {{ ($is_premium_changed == false || $is_premium_upgraded == false) ? 'disabled':'' }} step="0.01" min="0" placeholder="Amount to be Paid ..." wire:model="amount_tobe_paid">
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                <!-- END --->
                @endif
                
                @if($is_successfull == true && $processing_response != null)
                <div style="color:blue;">
                    @livewire('components.results.quotation-simple', ['quotation' => $processing_response['quotation']])
                </div>
                @endif

            </div>
         </div>  
         
        </div>
    </div>
    </fieldset>

    <div class="f1-buttons">
        <hr />
        <button wire:click="goToPrevious" class="btn btn-outline-primary btn-previous pull-left" style="display: {{ $information_step == "block" || $is_successfull == true ? 'none' : 'block' }}" type="button" wire:loading.remove><i class="icofont icofont-arrow-left"></i> {{ $previous_text }}</button>
            
        <span wire:loading.remove class="checkbox checkbox-dark" style="display: {{ $confirmation_step == 'block' && $is_successfull == false ? '' : 'none' }};">
              <input id="inline-1" type="checkbox" wire:model="hasconfirmed">
              <label for="inline-1">I Affirm to Read and Confirm &nbsp;&nbsp;</label>
            </span>

            @if($processing_response != null)
            @if($processing_response['responseCode'] == 'SUCCESS')
                <a href="{{ Route('quotation-download', ['id' => $processing_response['quotation']['id']]) }}" class="btn btn-outline-primary btn-next" style="margin-top: auto; display:{{ $is_successfull == true ? '' : 'none' }}" type="button">Print <i class="icofont icofont-printer"></i></a>
            @endif
            @endif

            <button wire:loading.remove wire:click="goToNext" {{ $this->goToNextRules() }} class="btn btn-outline-primary btn-next" style="margin-top: auto; display:{{ $is_successfull == true ? 'none' : '' }}" type="button">{{ $continue_text }} {!! $is_successfull == true ? '<i class="icofont icofont-printer"></i>' : '<i class="icofont icofont-arrow-right"></i>' !!}</button>
               
        <div wire:loading.delay>
            <div class="loader-box">
                <div class="loader-7" style="width: 50px; height:50px;"></div>
                <h5 class="f-w-100">Please wait ...</h5>
            </div>
        </div>

    </div>

  </form>


  <style>
    .card_radio{
        height: 35vh;
        width: 32%;
        position: relative;
    }
    .radio_input{
        -webkit-appearance: none;
        appearance: none;
        background-color: white;
        height: 100%;
        width: 100%;
        border-radius: 10px;
        position: absolute;
        box-shadow: 7px 7px 15px rgba(2,28,53,0.08);
        cursor: pointer;
        outline: none;
    }
    .radio_input:before{
        content: "";
        position: absolute;
        height: 22px;
        width: 22px;
        background-color: #f9fafd;
        border: 1px solid #e2e6f3;
        border-radius: 50%;
        top: 35px;
        right: 20px;
    }
    .radio_input:after{
        content: "";
        position: absolute;
        height: 13px;
        width: 13px;
        background-color: transparent;
        border-radius: 50%;
        top: 39.5px;
        right: 24.5px;
    }
    .radio_label{
        position: absolute;
        margin: 20px;
        cursor: pointer;
    }
    .radio_h5{
        font-weight: 600;
        font-size: 22px;
        letter-spacing: 0.5px;
        margin: 15px 0 20px 0;
    }
    .radio_input:hover{
        transform: scale(1.05);
    }
    .radio_input:checked{
        border: 3px solid #3989c6;
    }
    .radio_input:checked:after{
        background-color: #3989c6;
    }
</style>

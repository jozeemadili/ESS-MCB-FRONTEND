
<form class="f1" method="post">
    
    <div class="f1-steps" style="display: {{ $is_successfull == true ? 'none' :'' }}">
      <div class="f1-progress">
        <div class="f1-progress-line" data-number-of-steps="3" style="width: {{ $progress }}%"></div>
      </div>
      <div class="f1-step {{ $information_step == "block" ? "active" : "" }}">
        <div class="f1-step-icon"><i class="icofont icofont-info"></i></div>
        <p>Inforamtion</p>
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

    <div class="row" style="font-size: 12px;">
        <div class="col-lg-4">
            <div class="card">
            <div class="card-body">
                <div class="form-group">
                <label>Choose Customer</label>
                <input class="form-control" wire:model="customerquery" type="text" placeholder="Search by ID, Name or Phone Number" aria-label="Search by ID, Name or Phone Number">
                <div class="list-group">
                    @foreach ($customers as $customer)
                    <a wire:click="selectCustomer({{ $customer }})" class="list-group-item list-group-item-action" href="javascript:void(0)" data-bs-original-title="">
                        <div class="d-flex w-100 justify-content-between">
                            <small><b>{{ strtoupper($customer->first_name) }} {{ strtoupper($customer->last_name) }} ({{ strtoupper(substr($customer->gender,0,1)) }})</b></small>
                        <small class="text-muted">+255{{ $customer->mobile }} <span class="badge badge-success rounded-pill counter">{{ $loop->index+1 }}</span></small>
                    </div>
                    <small class="text-muted">{{ ($customer->id_number) }} ({{ App\Http\Controllers\API\Auth\CustomersController::resolveIdType($customer->id_type) }})</small>
                    </a>
                    @endforeach
                </div>
                </div>
        
                <div class="form-group">
                <label for="f1-last-name">Choose Insurer</label>
                <input class="form-control" {{ (Auth::user()->company_id == 1 || Auth::user()->company->category == 'Broker' || Auth::user()->company->category == 'Agent' || Auth::user()->company->category == 'Bank Assurance') ? '' : 'disabled' }} wire:model="companyquery" type="text" placeholder="Search by Code, Name or Registration No" aria-label="Search by Code, Name or Registration No">
                {{-- <input class="form-control" disabled wire:model="companyquery" type="text" placeholder="Search by Code, Name or Registration No" aria-label="Search by Code, Name or Registration No"> --}}
                <div class="list-group">
                    @foreach ($companies as $company)
                    <a wire:click="selectCompany({{ $company }})" class="list-group-item list-group-item-action" href="javascript:void(0)" data-bs-original-title="">
                        <div class="d-flex w-100 justify-content-between">
                        <small><b>{{ strtoupper($company->name) }}</b></small>
                        <small class="text-muted">{{ $company->code }} <span class="badge badge-success rounded-pill counter">{{ $loop->index+1 }}</span></small>
                    </div>
                    <small class="text-muted">{{ ($company->short_form) }} | {{ ($company->registration_number) }} | {{ ($company->sale_point_code) }}</small>
                    </a>
                    @endforeach
                </div>
                </div>
        
                <div class="form-group">
                <label for="f1-last-name">Choose Vehicle</label>
                <input class="form-control" wire:model="vehiclequery" type="text" placeholder="Search by Registration, Chasis, Engine No, or Owner Name" aria-label="Registration, Chasis, Engine No, or Owner Name">
                <div class="list-group">
                    @foreach ($vehicles as $vehicle)
                    <a wire:click="selectVehicle({{ $vehicle }})" class="list-group-item list-group-item-action" href="javascript:void(0)" data-bs-original-title="">
                        <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ strtoupper($vehicle->registration_number) }}</h6>
                        <small class="text-muted">{{ $vehicle->make }} {{ $vehicle->model }} <span class="badge badge-success rounded-pill counter">{{ $loop->index+1 }}</span></small>
                    </div>
                    <small class="text-muted">{{ ($vehicle->chassis_number) }} | {{ ($vehicle->owner_name) }}</small>
                    </a>
                    @endforeach
                </div>
                </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="form-group">
                        <label>Vehicle Value</label>
                        <input class="form-control" type="number"  wire:model="sum_insured" placeholder="Amount to be Insured...">
                        @if ($sum_insured>100)
                            <small><blockquote class="blockquote" style="font-size: 12px;"><b>{{ number_format($sum_insured, 2, '.', ',') }} TZS</b></blockquote></small>
                        @endif
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="col-form-label" >Is Tax Exempted ?</label><br />
                        <input type="radio" class="radio_animated" value="Y"  wire:model="is_tax_exempted" /> Yes
                        &nbsp;&nbsp;
                        <input type="radio" class="radio_animated" checked value="N" wire:model="is_tax_exempted" /> No
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7">
                    <div class="form-group">
                        <label class="col-form-label" >Start Date</label>
                        <input class="datepicker-hereX form-control digitsX" type="datetime-local" min="{{ date('Y-m-d') }}"  wire:model="start_date" placeholder="Starting Date ..." id="minMaxExampleX" data-language="en">
                        @if ($start_date && $end_date)
                            <small><blockquote class="blockquote" style="font-size: 12px;"><b>{{ $diff_months }} Months | {{ $diff_days }} days</b></blockquote></small>
                        @endif
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="col-form-label" >End Date</label>
                        <input class="datepicker-hereX form-control digitsX" type="datetime-local" min="{{ date('Y-m-d') }}" wire:model="end_date" placeholder="Ending Date ..." id="minMaxExampleX" data-language="en">
                    </div>
                </div>
            </div>
            </div>
            </div>
        </div>


        <div class="col-lg-8">
        <div class="card" style="height: 63vh; overflow:scroll;">
        <div class="card-body">
        <div class="row">
            @if($products != null)
            @foreach ($products as $product)
            @if($product->status == 'Active')
            <div class="card_radio">
                <input type="radio" class="radio_input" name="product_name" {{ $product->name == Config::get('custom.constants.constraints.motor_covers_default_product_name') ? 'checked' : '' }} wire:model="product_name" value="{{ $product->name }}" id="{{ $product->id }}">
                <label for="{{ $product->id }}" class="radio_label">
                    <h5 class="radio_h5">{{ $product->code == 'SP014001000000' ? 'Private' : ($product->code == 'SP014002000000' ? 'Cycles' : ($product->code == 'SP014003000000' ? 'Commercial' : ($product->code == 'SP014004000000' ? 'Passengers' : 'Special'))) }}</h5>
                        <i class="icofont icofont-{{ $product->code == 'SP014001000000' ? 'car-alt-1' : ($product->code == 'SP014002000000' ? 'motor-bike' : ($product->code == 'SP014003000000' ? 'truck' : ($product->code == 'SP014004000000' ? 'bus-alt-2' : 'ambulance-cross'))) }}" style="font-size: 52px;"></i>
                </label>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            @endif
            @endforeach
            @endif
        </div>

        <div class="row">
            <p><br />
            <div class="col-lg-4">
            <div class="form-group">
                <label class="col-form-label" >Cover Type</label><br />
                <input type="radio" class="radio_animated" value="comprehensive"  wire:model="cover_type" name="cover_type" /> Comprehensive
                &nbsp;&nbsp;
                <input type="radio" class="radio_animated" value="third" wire:model="cover_type" name="cover_type" /> Third Party
            </div>
            </div>

            @if($cover_type == 'comprehensive')
            <div class="col-lg-3">
            <div class="form-group">
                <label class="col-form-label" >Claim Record</label><br />
                <input type="radio" class="radio_animated" value="free"  wire:model="claim_record" name="claim_record" /> No Claim
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" class="radio_animated" checked value="with" wire:model="claim_record" name="claim_record" /> Has Claim
            </div>
            </div>
            @else 
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="col-form-label" >Third Party Type</label><br />
                    <input type="radio" class="radio_animated" value="tpo"  wire:model="third_party_type" name="third_party_type" /> TPO
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" class="radio_animated" checked value="tpft" wire:model="third_party_type" name="third_party_type" /> TPFT
                </div>
                </div>
            @endif

            @if($product_name == 'MOTOR MOTOR CYCLE')
            <div class="col-lg-2">
            <div class="form-group">
                <label class="col-form-label" >No. of Wheels</label><br />
                <input type="radio" class="radio_animated" value="two"  wire:model="no_wheels" name="no_wheels" /> 2
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" class="radio_animated" checked value="three" wire:model="no_wheels" name="no_wheels" /> 3
            </div>
            </div>
            @endif


            @if($product_name == 'MOTOR Commercial Vehicle')
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="col-form-label" >Type</label><br />
                    <input type="radio" class="radio_animated" value="oil"  wire:model="commercial_type" name="commercial_type" /> Tanker
                    &nbsp;
                    <input type="radio" class="radio_animated" value="trailers" wire:model="commercial_type" name="commercial_type" /> Trailer
                    &nbsp;
                    <input type="radio" class="radio_animated" value="general" wire:model="commercial_type" name="commercial_type" /> General
                </div>
                </div>
            @endif

            <!-- START MOTOR PRIVATE VEHICLE -->
            @if($product_name == 'MOTOR PRIVATE VEHICLE')
                <!-- MOTOR PRIVATE RISKS BASED ON ABOVE CRITERIAS -->
                @if($products != null && $vehicle !=null)
                @foreach ($products as $product)
                @if($product_name != null)
                @if($product_name == $product->name)
                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-{{ $this->getRibbonColor($product->code) }}">{{ strtoupper($product->name) }}</div>
                        <div class="row">
                            @foreach ($product->risks as $risk)
                            @if(Str::containsAll(strtolower($risk->name), [$cover_type, $cover_type == 'comprehensive' ? $claim_record : $third_party_type]))
                            <div class="card_radio2">
                                <input type="radio" id="{{ $risk->id }}" class="radio_input2" name="risk_id" value="{{ $risk }}" wire:model="risk_id">
                                <label for="{{ $risk->id }}" class="radio_label">
                                    <h5 class="radio_h52">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $vehicle['sitting_capacity'])['total_formated'] }} TZS ({{ $risk->premium_rate * 100 }}%)</h5>
                                    <small>{{ $risk->name }}</small>
                                </label>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endif
                @endforeach
                @endif
                <!-- END -->
                <!-- END -->

            @elseif($product_name == 'MOTOR MOTOR CYCLE')
                <div class="row">
                <!-- MOTOR CYCLE RISKS BASED ON ABOVE CRITERIAS -->
                @if($products != null && $vehicle !=null)
                @foreach ($products as $product)
                @if($product_name != null)
                @if($product_name == $product->name)
                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-{{ $this->getRibbonColor($product->code) }}">{{ strtoupper($product->name) }}</div>
                        <div class="row">
                            @foreach ($product->risks as $risk)
                            @if(Str::containsAll(strtolower($risk->name), [$cover_type, $cover_type == 'comprehensive' ? $claim_record : $third_party_type, $no_wheels]))
                            <div class="card_radio2">
                                <input type="radio" id="{{ $risk->id }}" class="radio_input2" name="risk_id" value="{{ $risk }}" wire:model="risk_id">
                                <label for="{{ $risk->id }}" class="radio_label">
                                    <h5 class="radio_h52">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $vehicle['sitting_capacity'])['total_formated'] }} TZS ({{ $risk->premium_rate * 100 }}%)</h5>
                                    <small>{{ $risk->name }}</small>
                                </label>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endif
                @endforeach
                @endif
                <!-- END -->
                </div>
            @elseif($product_name == 'MOTOR Commercial Vehicle')
            <div class="row">
                <!-- MOTOR Commercial RISKS BASED ON ABOVE CRITERIAS -->
                @if($products != null && $vehicle !=null)
                @foreach ($products as $product)
                @if($product_name != null)
                @if($product_name == $product->name)
                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-{{ $this->getRibbonColor($product->code) }}">{{ strtoupper($product->name) }}</div>
                        <div class="row">
                            @foreach ($product->risks as $risk)
                            @if(Str::containsAll(strtolower($risk->name), [$cover_type, $cover_type == 'comprehensive' ? $claim_record : $third_party_type, $commercial_type]))
                            <div class="card_radio2">
                                <input type="radio" id="{{ $risk->id }}" class="radio_input2" name="risk_id" value="{{ $risk }}" wire:model="risk_id">
                                <label for="{{ $risk->id }}" class="radio_label">
                                    <h5 class="radio_h52">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $vehicle['sitting_capacity'])['total_formated'] }} TZS ({{ $risk->premium_rate * 100 }}%)</h5>
                                    <small>{{ $risk->name }}</small>
                                </label>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endif
                @endforeach
                @endif
                <!-- END -->
                </div>
            @elseif($product_name == 'MOTOR PASSENGER CARRYING')
            <div class="row">
                <!-- MOTOR PASSENGER RISKS BASED ON ABOVE CRITERIAS -->
                @if($products != null && $vehicle !=null)
                @foreach ($products as $product)
                @if($product_name != null)
                @if($product_name == $product->name)
                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-{{ $this->getRibbonColor($product->code) }}">{{ strtoupper($product->name) }}</div>
                        <div class="row">
                            @foreach ($product->risks as $risk)
                            @if(Str::containsAll(strtolower($risk->name), [$cover_type, $cover_type == 'comprehensive' ? $claim_record : $third_party_type]))
                            <div class="card_radio2">
                                <input type="radio" id="{{ $risk->id }}" class="radio_input2" name="risk_id" value="{{ $risk }}" wire:model="risk_id">
                                <label for="{{ $risk->id }}" class="radio_label">
                                    <h5 class="radio_h52">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $vehicle['sitting_capacity'])['total_formated'] }} TZS ({{ $risk->premium_rate * 100 }}%)</h5>
                                    <small>{{ $risk->name }}</small>
                                </label>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endif
                @endforeach
                @endif
                <!-- END -->
                </div>
            @elseif($product_name == 'MOTOR SPECIAL TYPE VEHICLES')
            <div class="row">
                <!-- MOTOR SPECIAL RISKS BASED ON ABOVE CRITERIAS -->
                @if($products != null && $vehicle !=null)
                @foreach ($products as $product)
                @if($product_name != null)
                @if($product_name == $product->name)
                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-{{ $this->getRibbonColor($product->code) }}">{{ strtoupper($product->name) }}</div>
                        <div class="row">
                            @foreach ($product->risks as $risk)
                            @if(Str::containsAll(strtolower($risk->name), [$cover_type, $cover_type == 'comprehensive' ? $claim_record : $third_party_type]))
                            <div class="card_radio2">
                                <input type="radio" id="{{ $risk->id }}" class="radio_input2" name="risk_id" value="{{ $risk }}" wire:model="risk_id">
                                <label for="{{ $risk->id }}" class="radio_label">
                                    <h5 class="radio_h52">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $vehicle['sitting_capacity'])['total_formated'] }} TZS ({{ $risk->premium_rate * 100 }}%)</h5>
                                    <small>{{ $risk->name }}</small>
                                </label>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endif
                @endforeach
                @endif
                <!-- END -->
                </div>
            @else 
            <br />
            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i> - Invalid Category Provided 
            </div>
            @endif
        </p>
        </div>
        </div>
        </div>
        </div>

    </div>

    </fieldset>


    <fieldset style="display: {{ $pricing_step }};">
      <div class="row" style="font-size: 12px;">

        <div class="col-lg-12">
            @if($products != null && $vehicle !=null)
            @foreach ($products as $product)
            @if($product_name != null)
            @if($product_name == $product->name)
            <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-{{ $this->getRibbonColor($product->code) }}">{{ strtoupper($product->name) }}</div>
                    <div class="row">
                        @foreach ($product->risks as $risk)
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="media p-10 shadow-lg shadow-showcase">
                                    <div class="radio radio-primary me-3">
                                        <input id="{{ $risk->id }}" {{ $risk->status !='Active' ? 'disabled' : '' }} type="radio" name="risk_id_old" value="{{ $risk }}" wire:model="risk_id">
                                        <label for="{{ $risk->id }}"></label>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="mt-0 mega-title-badge">{{ $risk->code }} ({{ $risk->premium_rate*100 }}%)<span class="badge badge-{{ $this->getRibbonColor($product->code) }} pull-right digits">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $vehicle['sitting_capacity'])['total_formated'] }} TZS</span></h6>
                                        <p><small>{{ $risk->name }}</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @else 
            <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-{{ $this->getRibbonColor($product->code) }}">{{ strtoupper($product->name) }}</div>
                    <div class="row">
                        @foreach ($product->risks as $risk)
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="media p-10 shadow-lg shadow-showcase">
                                    <div class="radio radio-primary me-3">
                                        <input id="{{ $risk->id }}" type="radio" name="risk_id_old" value="{{ $risk->id }}">
                                        <label for="{{ $risk->id }}"></label>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="mt-0 mega-title-badge">{{ $risk->code }} ({{ $risk->premium_rate*100 }}%)<span class="badge badge-{{ $this->getRibbonColor($product->code) }} pull-right digits">{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, $risk, $is_tax_exempted, $vehicle['sitting_capacity'])['total'] }} TZS</span></h6>
                                        <p><small>{{ $risk->name }}</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @endif
        </div>
        

      </div>
    </fieldset>


    <fieldset style="display: {{ $confirmation_step }};">
        <div class="row">
            <div class="col-lg-6">
                <div style="display: {{ $is_successfull == false ? '' : 'none' }}">
                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-dark">Payment</div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="media p-15">
                                        <div class="radio radio-primary me-3">
                                            <input id="payment_method_1" type="radio" {{ $is_processing == true ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="1">
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
                                            <input id="payment_method_2" type="radio" {{ $is_processing == true ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="2">
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
                                            <input id="payment_method_3" type="radio" {{ $is_processing == true ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="3">
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
                                            <input id="payment_method_4" type="radio" {{ $is_processing == true ? 'disabled':'' }} name="payment_method" wire:model="payment_method" value="4">
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
                                <input class="form-control" {{ $is_processing == true ? 'disabled':'' }} type="number" step="0.01" min="0" placeholder="Amount Received ..." wire:model="amount_received">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group" style="display: {{ $payment_method != 1 ? 'block' : 'none' }};">
                            <label>Outstanding to be paid via <code>{{ $payment_method == 2 ? 'CHEQUE' : ($payment_method == 3 ? 'EFT' : 'IPF') }}</code></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input class="form-control" type="number" {{ $is_processing == true ? 'disabled':'' }} step="0.01" min="0" placeholder="Amount to be Paid ..." wire:model="amount_tobe_paid">
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                @if($processing_response != null)

                @if($processing_response['responseCode'] != 'SUCCESS')
                <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                    {!! $processing_response['message']  !!}
                </div>
                @else
                <div>
                    <div class="row" style="display: {{ count($processing_response) >= 1 ? 'block' : 'none' }}">
                        @livewire('components.results.quotation', ['quotation' => $processing_response['quotation']])  
                    </div>
                </div>
                @endif
                @endif

                <div wire:loading.delay>
                <div class="loader-box">
                    <div class="loader-7" style="width: 50px; height:50px;"></div>
                        <h5 class="f-w-100">Processing Your Quotation ...</h5>
                     </div>
                </div>

            </div>

            <div class="col-lg-6">
                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-{{ $is_successfull == true ? 'primary' : 'dark' }}">{{ $is_successfull == true ? 'Receipt' : 'Invoice' }}</div>
                        <div class="row">
                            @if($customer != null && $company != null && $vehicle != null && $risk_id != null)
                            <table cellpadding="0" cellspacing="0" border="0" align="left" style="width: 100%; margin-top: -20px; margin-bottom: 20px;">
                                <tbody>
                                    <tr>
                                        <td style="background-color: #fafafa; padding: 10px; letter-spacing: 0.3px; width: 50%;">
                                            <h5 style="font-size: 12px; font-weight: 600; color: #000; line-height: 16px; padding-bottom: 13px; border-bottom: 1px solid #e6e8eb; letter-spacing: -0.65px; margin-top: 0; margin-bottom: 13px;">
                                                {{ strtoupper($customer['first_name']) }} {{ strtoupper($customer['middle_name']) }}  {{ strtoupper($customer['last_name']) }} ({{ substr(strtoupper($customer['gender']),0,1) }})
                                            </h5>
                                            <p style="text-align: left; font-weight: normal; font-size: 14px; color: #aba8a8; line-height: 21px; margin-top: 0;">
                                                +255{{ ($customer['mobile']) }} | {{ $customer['email'] }},<br>
                                                {{ ($customer['id_number']) }} ({{ App\Http\Controllers\API\Auth\CustomersController::resolveIdType($customer['id_type']) }}), <br>
                                                P.O.BOX {{ $customer['postal_address'] }} {{ $customer['country_code'] }}.
                                            </p>
                                        </td>
                                        
                                        <td style="background-color: #fafafa; padding: 10px; letter-spacing: 0.3px; width: 50%;">
                                            <h5 style="font-size: 12px; font-weight: 600; color: #000; line-height: 16px; padding-bottom: 13px; border-bottom: 1px solid #e6e8eb; letter-spacing: -0.65px; margin-top: 0; margin-bottom: 13px;">
                                                {{ strtoupper($company['name']) }}
                                            </h5>
                                            <p style="text-align: left; font-weight: normal; font-size: 14px; color: #aba8a8; line-height: 21px; margin-top: 0;">
                                                +255{{ ($company['phone_number']) }} | {{ $company['email_address'] }},<br>
                                                {{ ($company['code']) }} | Reg # {{ $company['registration_number'] }} | TIN # {{ $company['tin'] }}, <br>
                                                P.O.BOX {{ $company['postal_address'] }} {{ $company['short_form'] }}.
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <table class="table table-xs" border="0" cellpadding="0" cellspacing="0" align="left">
                                <thead class="bg-primary">
                                    <tr align="left">
                                        <th>PRODUCT</th>
                                        <th>RISK DESCRIPTION</th>
                                        <th>QTY</th>
                                        <th>AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><small><b>{{ strtoupper($product_name) }}</b></small></td>
                                        <td valign="top">
                                            {{ json_decode($risk_id)->name }}
                                            <hr />
                                            <b>For Vehicle :</b><br />
                                            Registration  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#  : <b>{{ strtoupper($vehicle['registration_number']) }}</b><br />
                                            Chasis   Number #  : <b>{{ strtoupper($vehicle['chassis_number']) }}</b><br />
                                            Model & Make  &nbsp;&nbsp;#  : <b>{{ strtoupper($vehicle['model']) }} {{ strtoupper($vehicle['make']) }}</b><br /><br />
                                        </td>
                                        <td valign="top">
                                            <h5>1</h5>
                                        </td>
                                        <td valign="top" align="left">
                                            <b>{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, json_decode($risk_id), $is_tax_exempted, $vehicle['sitting_capacity'])['cost_formated'] }}</b>
                                        </td>
                                    </tr>
                                    <tr class="pad-left-right-space">
                                        <td  align="left">
                                            <p style="font-size: 15px;">Subtotal </p>
                                        </td>
                                        <td></td><td></td>
                                        <td align="left"><b>{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, json_decode($risk_id), $is_tax_exempted, $vehicle['sitting_capacity'])['cost_formated'] }}</b></td>
                                    </tr>
                                    <tr class="pad-left-right-space">
                                        <td align="left">
                                            <p style="font-size: 15px;">TAX </p>
                                        </td>
                                        <td></td><td></td>
                                        <td align="left"><b>{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, json_decode($risk_id), $is_tax_exempted, $vehicle['sitting_capacity'])['tax_formated'] }}</b></td>
                                    </tr>

                                    @if($is_successfull == true && $processing_response != null)
                                    <tr class="pad-left-right-space table-success">
                                        <td align="left">
                                            <p style="font-size: 14px;">Paid Amount</p>
                                        </td>
                                        <td></td><td></td>
                                        <td align="left"><b>{{ number_format($processing_response['payment']['payment']['paid_amount'], 2, '.', ',') }}</b></td>
                                    </tr>
                                    <tr class="pad-left-right-space table-success">
                                        <td class="m-b-5" align="left">
                                            <p style="font-size: 14px;">Oustanding Amount</p>
                                        </td>
                                        <td></td><td></td>
                                        <td align="left"><b>{{ number_format($processing_response['payment']['payment']['outstanding_amount'], 2, '.', ',') }}</b></td>
                                    </tr>
                                    @endif
                                    
                                    <tr class="pad-left-right-space">
                                        <td class="m-b-5" align="left">
                                            <p style="font-size: 16px;"><b>TOTAL</p>
                                        </td>
                                        <td></td><td></td>
                                        <td class="m-b-5" align="left"><b>{{  App\Http\Controllers\API\Pricings\MotorEstimationsController::estimate($diff_days, $sum_insured, json_decode($risk_id), $is_tax_exempted, $vehicle['sitting_capacity'])['total_formated'] }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </fieldset>

    <div class="f1-buttons">
        <hr />
        <button wire:click="goToPrevious" class="btn btn-outline-primary btn-previous pull-left" style="display: {{ $information_step == "block" || $is_successfull == true ? 'none' : 'block' }}" type="button" wire:loading.remove><i class="icofont icofont-arrow-left"></i> {{ $previous_text }}</button>
            
        <span wire:loading.remove class="checkboxx checkbox-dark" style="display: {{ $confirmation_step == 'block' && $is_successfull == false ? '' : 'none' }};">
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
        height: 124px;
        width: 165px;
        position: relative;
    }
    .card_radio2{
        height: 155px;
        width: 400px;
        position: relative;
        margin: 0.5em;
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
    .radio_input2{
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
    .radio_input2:before{
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
    .radio_input2:after{
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
        font-size: 12px;
        letter-spacing: 0.5px;
        margin: 15px 0 20px 0;
    }
    .radio_h52{
        font-weight: 600;
        font-size: 18px;
        letter-spacing: 0.5px;
        margin: 15px 0 20px 0;
    }
    .radio_input:hover{
        transform: scale(1.05);
    }
    .radio_input2:hover{
        transform: scale(1.05);
    }
    .radio_input:checked{
        border: 3px solid #3989c6;
    }
    .radio_input2:checked{
        border: 3px solid #3989c6;
    }
    .radio_input:checked:after{
        background-color: #3989c6;
    }
    .radio_input2:checked:after{
        background-color: #3989c6;
    }
</style>

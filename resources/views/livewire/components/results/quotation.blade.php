<div>
    <div class="ribbon-wrapper card">
        <div class="card-body">
            <div class="ribbon ribbon-clip ribbon-primary">Successfully Processed</div>
            <div class="row">
                    <ul class="nav nav-tabs border-tab nav-primary" id="info-tab" role="tablist">
                      <li class="nav-item"><a class="nav-link active" id="quotation-tab" data-bs-toggle="tab" href="#info-home" role="tab" aria-controls="info-home" aria-selected="true"><i class="icofont icofont-shield"></i>Quotation</a></li>
                      <li class="nav-item"><a class="nav-link" id="payment-tab" data-bs-toggle="tab" href="#info-profile" role="tab" aria-controls="info-profile" aria-selected="false"><i class="icofont icofont-credit-card"></i>Payment</a></li>
                      <li class="nav-item"><a class="nav-link" id="policy-tab" data-bs-toggle="tab" href="#policy" role="tab" aria-controls="policy-tab" aria-selected="false"><i class="icofont icofont-file-document"></i>Policy</a></li>
                      <li class="nav-item"><a class="nav-link" id="policy-tab" data-bs-toggle="tab" href="#claims" role="tab" aria-controls="claims-tab" aria-selected="false"><i class="icofont icofont-whisle"></i>Claims</a></li>
                      <li class="nav-item"><a class="nav-link" id="endorsements-tab" data-bs-toggle="tab" href="#endorsements" role="tab" aria-controls="endorsements-tab" aria-selected="false"><i class="icofont icofont-edit"></i>Endorsments</a></li>
                    </ul>
                    <div class="tab-content" id="info-tabContent">
                      <div class="tab-pane fade show active" id="info-home" role="tabpanel" aria-labelledby="quotation-tab">
                        <table class="table table-xs" border="0" cellpadding="0" cellspacing="0" align="left">
                            <tbody>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Reference # </p>
                                    </td>
                                    <td align="left"><href='#'><a href="{{ Route('quotation-profile', ['id' => $quotation['id']]) }}">{{ $quotation['reference_number']}}</b></a></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Request Type </p>
                                    </td>
                                    <td align="left"><b>{!! $quotation['cover_note_type'] == 1 ? '<span class="badge badge-primary">New</span>' : ($quotation['cover_note_type'] == 2 ? '<span class="badge badge-success">Renew</span>' : '<span class="badge badge-dark">Endorsement</span>') !!}</b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Company </p>
                                    </td>
                                    <td align="left"><b>{{ strtoupper($quotation['company']['name'])}} </b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Customer </p>
                                    </td>
                                    <td align="left"><b>{{ strtoupper($quotation['customer']['first_name'])}} {{ strtoupper($quotation['customer']['middle_name'])}} {{ strtoupper($quotation['customer']['last_name'])}} </b></td>
                                </tr>
                                @if($quotation['vehicle_id'] != null)
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Vehicle </p>
                                    </td>
                                    <td align="left"><a href='{{ Route('vehicle-profile', ['id' => $quotation['vehicle']['id']]) }}'>{{ strtoupper($quotation['vehicle']['registration_number'])}} / {{ strtoupper($quotation['vehicle']['chassis_number'])}} - {{ ($quotation['vehicle']['make'])}} {{ ($quotation['vehicle']['model'])}} </b></td>
                                </tr>
                                @endif
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Start Date </p>
                                    </td>
                                    <td align="left">
                                        <b>{{ $quotation['start_date']->format('d/m/Y H:i:s')}} </b>
                                        @if($quotation['status'] != 'Accepted' && $quotation['user']['company']['id'] == Auth::user()->company->id)
                                            <button class="btn btn-outline-primary btn-xs pull-right" data-bs-toggle="modal" data-bs-target="#modifyQuotation">Modify <i class="icofont icofont-ui-edit"></i></button>
                                        @endif
                                    </td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">End Date </p>
                                    </td>
                                    <td align="left">
                                        <b>{{ $quotation['end_date']->format('d/m/Y H:i:s')}} </b>
                                        @if($quotation['status'] != 'Accepted'  && $quotation['user']['company']['id'] == Auth::user()->company->id)
                                            <button class="btn btn-outline-primary btn-xs pull-right" data-bs-toggle="modal" data-bs-target="#modifyQuotation">Modify <i class="icofont icofont-ui-edit"></i></button>
                                        @endif
                                    </td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Type </p>
                                    </td>
                                    <td align="left"><b>{{ strtoupper($quotation['risk']['product']['name']) }} </b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Risk </p>
                                    </td>
                                    <td align="left"><b>{{ ($quotation['risk']['name']) }} </b></td>
                                </tr>
                                
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Description </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['description']}} </b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Sum Insured </p>
                                    </td>
                                    <td align="left">
                                        <b>TZS {{ number_format($quotation['sum_insured'], 2, '.',',')}} </b>
                                        @if($quotation['status'] != 'Accepted'  && $quotation['user']['company']['id'] == Auth::user()->company->id)
                                             <button class="btn btn-outline-primary btn-xs pull-right" data-bs-toggle="modal" data-bs-target="#modifyQuotation">Modify <i class="icofont icofont-ui-edit"></i></button>
                                        @endif
                                    </td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Premium excluding Tax </p>
                                    </td>
                                    <td align="left"><b>TZS {{ number_format($quotation['total_premium_excluding_tax'], 2, '.',',')}} </b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Premium including Tax </p>
                                    </td>
                                    <td align="left"><b>TZS {{ number_format($quotation['total_premium_including_tax'], 2, '.',',')}} </b></td>
                                </tr>
                                <span wire:poll.visible="refreshDetails">
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Sticker Number </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['sticker_number']}} </b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Covernote Ref # </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['covernote_reference_number']}} </b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Status </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['status']}}
                                    @if($quotation['tira_response_status'] != null)
                                    <br /> <font color='orange'>{{ $quotation['status'] != 'Accepted' ? '*** '.$quotation['tira_response_status'] : ''}}</font>
                                    @endif
                                    </b>
                                </td>
                                </tr>
                                @if($quotation['status'] != 'Accepted'  && ($quotation['user']['company']['id'] == Auth::user()->company->id || $quotation['company_id'] == Auth::user()->company->id))
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;"> </p>
                                    </td>
                                    <td align="left">
                                        <p>
                                            <button wire:click='resubmitToTIRA' class='btn btn-outline-primary btn-xs'>Resubmit to TIRA <i class='icofont icofont-refresh'></i></button>
                                        </p>
                                    </td>
                                </tr>
                                @endif

                                @if($resubmissionResponse != null && $quotation['status'] != 'Accepted')
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;"> </p>
                                    </td>
                                    <td align="left">
                                        <p>
                                           <span style="color:cadetblue; font-size:12px;">***  {!! $resubmissionResponse !!}</span>
                                           @if($resubmissionError != null)
                                           <br />
                                           <span style="color:red; font-size:12px;"> - {!! $resubmissionError !!}</span>
                                           @endif
                                        </p>
                                    </td>
                                </tr>
                                @endif
                                </span>
                            </tbody>
                        </table>
                      </div>
                      <div class="tab-pane fade" id="info-profile" role="tabpanel" aria-labelledby="payment-tab">
                        @if($quotation['payment'] != null)
                            <table class="table table-xs" border="0" cellpadding="0" cellspacing="0" align="left">
                            <tbody>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Reference Number </p>
                                    </td>
                                    <td align="left"><href='#'><b>{{ $quotation['payment']['reference_number']}}</b></a></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Received by </p>
                                    </td>
                                    <td align="left"><b>{{ strtoupper($quotation['payment']['user']['first_name'])}} {{ strtoupper($quotation['payment']['user']['middle_name'])}} {{ strtoupper($quotation['payment']['user']['last_name'])}}</b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Received At </p>
                                    </td>
                                    <td align="left"><b>{{ ($quotation['payment']['created_at']->format('d/m/Y H:i:s'))}}</b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Currency </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['payment']['currency_code']}} </b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Exchange Rate </p>
                                    </td>
                                    <td align="left"><b>{{ number_format($quotation['payment']['exchange_rate'],2,'.',',')}} </b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Expected Amount </p>
                                    </td>
                                    <td align="left"><b>{{ number_format($quotation['payment']['expected_amount'],2,'.',',')}} </b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Paid Amount </p>
                                    </td>
                                    <td align="left"><b>{{ number_format($quotation['payment']['paid_amount'], 2,'.',',')}} </b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Outstanding Amount </p>
                                    </td>
                                    <td align="left"><b>{{ number_format($quotation['payment']['outstanding_amount'],2, '.', ',')}}</b></td>
                                </tr>
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Payment Method </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['payment']['method'] == 1 ? 'CASH' : ($quotation['payment']['method'] == 2 ? 'CHEQUE' : ($quotation['payment']['method'] == 3 ? 'EFT' : 'IPF') )}} </b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Third Party Reference </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['payment']['thirdparty_reference_number']}}</b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Third Party Status </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['payment']['thirdparty_response_description']}}</b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Payer Phone Number </p>
                                    </td>
                                    <td align="left"><b>255{{ $quotation['payment']['payer_msisdn']}}</b></td>
                                </tr>

                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                        <p style="font-size: 15px;">Status </p>
                                    </td>
                                    <td align="left"><b>{{ $quotation['payment']['status']}}</b></td>
                                </tr>

                                @if($quotation['payment']['status'] != 'Full Paid'  && ($quotation['user']['company']['id'] == Auth::user()->company->id || $quotation['company_id'] == Auth::user()->company->id))
                                <tr class="pad-left-right-space">
                                    <td  align="left">
                                    </td>
                                    <td align="left"><br /><a class='btn btn-outline-primary btn-xs' data-bs-toggle="modal" data-bs-target="#newPaymentModal">Make Payment <i class="icofont icofont-credit-card"></i></a><br /><br /></td>
                                </tr>
                                @endif

                            </tbody>
                             </table>
                        @else
                             <span>
                                No Payment Records for this Quotation
                             </span>
                        @endif
                      </div>
                      <div class="tab-pane fade" id="policy" role="tabpanel" aria-labelledby="policy-tab">
                        @if(count($quotation['policies'])>0)
                        <table class="table table-xs">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Reference #</th>
                                    <th scope="col">Condtions</th>
                                    <th scope="col">Clause</th>
                                    <th scope="col">Exclusions</th>
                                    <th scope="col">Insurer Resp.</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($quotation['policies'] as $policy)
                                <tr>
                                    <th scope="row">{{$loop->index + 1}}.</th>
                                    <td>{{ $policy->reference_number }}</td>
                                    <td>{{$policy->special_conditions}}</td>
                                    <td>{{$policy->policy_operative_clause}}</td>
                                    <td>{{$policy->exclusions}}</td>
                                    <td title="{{ $policy->insurer_response }}">{{substr($policy->insurer_response, 0,23)}} ...</td>
                                    <td>{{$policy->created_at->format('d/m/y')}}</td>
                                    <td>{{$policy->status}}</td>
                                    <td>
                                        @if($quotation['company']['code'] == 'ICC121')
                                            <button title="Notify Insurer" {{ $policy->insurer_response == 'Acknowledged' ? 'disabled' : '' }} wire:click="notifyInsurer({{ $policy['id'] }})" class="btn btn-outline-primary btn-xs">Notify<i class="fa fa-bell"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                        @else 
                        <div class="alert alert-danger outline"><i class="icon-info-alt txt-danger"></i> No Policy Submitted yet ...</div>
                        @endif
                      </div>
                      <div class="tab-pane fade" id="claims" role="tabpanel" aria-labelledby="claims-tab">
                            @if(count($quotation['policies'])>0)
                                @foreach($quotation['policies'] as $policy)
                                @if(count($policy->claim_notifications)>0)
                                <table class="table table-xs">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Reference #</th>
                                            <th scope="col">Claim Ref #</th>
                                            <th scope="col">Report Date</th>
                                            <th scope="col">Loss Date</th>
                                            <th scope="col">Loss Nature</th>
                                            <th scope="col">Loss Type</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($policy->claim_notifications as $claim_notification)
                                    <tr>
                                        <th scope="row">{{$loop->index + 1}}.</th>
                                        <td><a href='{{ Route('claim-profile', ['id' => $claim_notification->id]) }}'>{{ $claim_notification->reference_number }}</a></td>
                                        <td><a href='{{ Route('claim-profile', ['id' => $claim_notification->id]) }}'>{{ $claim_notification->claim_reference_number }}</a></td>
                                        <td>{{$claim_notification->report_date->format('d/m/Y')}}</td>
                                        <td>{{$claim_notification->loss_date->format('d/m/Y')}}</td>
                                        <td>{{$claim_notification->loss_nature->title}}</td>
                                        <td>{{$claim_notification->loss_type->title}}</td>
                                        <td>{{$claim_notification->created_at->format('d/m/Y')}}</td>
                                        <td>{{$claim_notification->status}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                </table>
                                 <br />
                                 @else 
                                 <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                    <i class="icon-info-alt txt-danger"></i>
                                        No Any Claim Notification found yet
                                   </div>
                                 @endif
                                 @endforeach
                                 @else 
                                 <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                    <i class="icon-info-alt txt-danger"></i>
                                        No Policy submitted yet, Once Submitted and Accepted you will be able to raise a Claim
                                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                                   </div>
                                @endif
                       </div>
                      <div class="tab-pane fade" id="endorsements" role="tabpanel" aria-labelledby="endorsements-tab">
                        <p>
                            @if(count($quotation['quotations']) >0)
                            <div class="table-responsive">
                                <table class="table table-xs">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Reference #</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Start</th>
                                            <th scope="col">End</th>
                                            <th scope="col">Sticker</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotation['quotations'] as $quotation)
                                        <tr>
                                            <th scope="row">{{$loop->index + 1}}.</th>
                                            <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{ $quotation->reference_number }}</a></td>
                                            <td><small>{{strtoupper($quotation->risk->product->name)}}</small></td>
                                            <td>{{$quotation->start_date->format('d/m/y')}}</td>
                                            <td>{{$quotation->end_date->format('d/m/y')}}</td>
                                            <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{$quotation->sticker_number}}</a></td>
                                            <td>{{$quotation->status}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                 <div class="alert alert-danger outline"><i class="icon-info-alt txt-danger"></i> No Endorsement records yet ...</div>
                            @endif
                        </p>
                       </div>
                    </div>
            </div>
        </div>
    </div>
    
</div>


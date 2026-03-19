<div>
    <div class="row">
            <table class="table table-xs" border="0" cellpadding="0" cellspacing="0" align="left">
                <tbody>
                    <tr class="pad-left-right-space">
                        <td  align="left">
                            <p style="font-size: 15px;">Request Type </p>
                        </td>
                        <td align="left"><b>{!! $quotation['cover_note_type'] == 1 ? '<span class="badge badge-primary">New</span>' : ($quotation['cover_note_type'] == 2 ? '<span class="badge badge-success">Renew</span>' : '<span class="badge badge-dark">Endorsement</span>') !!}</b></td>
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
                        <td align="left"><b>{{ strtoupper($quotation['vehicle']['registration_number'])}} / {{ strtoupper($quotation['vehicle']['chassis_number'])}} - {{ ($quotation['vehicle']['make'])}} {{ ($quotation['vehicle']['model'])}} </b></td>
                    </tr>
                    @endif
                    <tr class="pad-left-right-space">
                        <td  align="left">
                            <p style="font-size: 15px;">Start Date </p>
                        </td>
                        <td align="left"><span class="badge badge-{!! ($quotation['cover_note_type'] == 1 ? 'primary' : ($quotation['cover_note_type'] == 2 ? 'success' : 'dark')) !!}">{{ $quotation['start_date']->format('d/m/Y H:i:s')}}</span></td>
                    </tr>

                    <tr class="pad-left-right-space">
                        <td  align="left">
                            <p style="font-size: 15px;">End Date </p>
                        </td>
                        <td align="left"><span class="badge badge-{!! $quotation['cover_note_type'] == 1 ? 'primary' : ($quotation['cover_note_type'] == 2 ? 'success' : 'dark') !!}">{{ $quotation['end_date']->format('d/m/Y H:i:s')}}</span></td>
                    </tr>

                    <tr class="pad-left-right-space">
                        <td  align="left">
                            <p style="font-size: 15px;">Product </p>
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
                        <td align="left"><span class="badge badge-{!! $quotation['cover_note_type'] == 1 ? 'primary' : ($quotation['cover_note_type'] == 2 ? 'success' : 'dark') !!}">{{ $quotation['description']}}</span></td>
                    </tr>

                    <tr class="pad-left-right-space">
                        <td  align="left">
                            <p style="font-size: 15px;">Sum Insured </p>
                        </td>
                        <td align="left"><b>TZS {{ number_format($quotation['sum_insured'], 2, '.',',')}} </b></td>
                    </tr>

                    <tr class="pad-left-right-space">
                        <td  align="left">
                            <p style="font-size: 15px;">Premium excluding Tax</p>
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
                    </span>

                    @if($quotation['status'] != 'Accepted')
                    <tr class="pad-left-right-space">
                        <td  align="left">
                            <p style="font-size: 15px;"> </p>
                        </td>
                        <td align="left">
                            <p>
                                <a wire:click='resubmitToTIRA' class='btn btn-outline-primary btn-xs'>Resubmit to TIRA <i class='icofont icofont-refresh'></i></a>
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

                </tbody>
            </table>
</div>
</div>

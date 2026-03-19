<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities." />
        <meta name="keywords" content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app" />
        <meta name="author" content="pixelstrap" />
        <link href="{{ public_path().'/assets/css/bootstrap.css' }}"  rel="stylesheet" id="bootstrap-css">
        <script src="{{ public_path().'/assets/js/bootstrap/bootstrap.min.js' }}"></script>
        <script src="{{ public_path().'/assets/js/jquery-3.5.1.min.js' }}"></script>
        <title>PolicyPro</title>
        <style>
        #invoice
        {
          padding: 30px;
        }

        .invoice {
        position: relative;
        background-color: #FFF;
        min-height: 680px;
        padding: 15px
        }

        .invoice header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid {{$quotation->company->color}}
        }

        .invoice .company-details {
        text-align: right
        }

        .invoice .company-details .name {
        margin-top: 0;
        margin-bottom: 0
        }

        .invoice .contacts {
        margin-bottom: 20px
        }

        .invoice .invoice-to {
        text-align: left
        }

        .invoice .invoice-to .to {
        margin-top: 0;
        margin-bottom: 0
        }

        .invoice .invoice-details {
        text-align: right
        }

        .invoice .invoice-details .invoice-id {
        margin-top: 0;
        color: {{$quotation->company->color}}
        }

        .invoice main {
        padding-bottom: 50px
        }

        .invoice main .thanks {
        margin-top: -110px;
        font-size: 2em;
        margin-bottom: 10px
        }

        .invoice main .notices {
        padding-left: 6px;
        border-left: 6px solid {{$quotation->company->color}}
        }

        .invoice main .notices .notice {
        font-size: 1.2em
        }

        .invoice table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px
        }

        .invoice table td,.invoice table th {
        padding: 15px;
        background: #eee;
        border-bottom: 1px solid #fff
        }

        .invoice table th {
        white-space: nowrap;
        font-weight: 400;
        font-size: 16px
        }

        .invoice table td h3 {
        margin: 0;
        font-weight: 400;
        font-size: 1em
        }

        .invoice table .qty,.invoice table .total,.invoice table .unit {
        text-align: center;
        font-size: 1.2em
        }

        .invoice table .no {
        font-size: 1.2em;
        }

        .invoice table .unit {
        background: #ddd
        }

        .invoice table .total {
        background: {{$quotation->company->color}};
        color: #fff
        }

        .invoice table tbody tr:last-child td {
        border: none
        }

        .invoice table tfoot td {
        background: 0 0;
        border-bottom: none;
        white-space: nowrap;
        text-align: right;
        padding: 10px 20px;
        font-size: 1.2em;
        border-top: 1px solid #aaa
        }

        .invoice table tfoot tr:first-child td {
        border-top: none
        }

        .invoice table tfoot tr:last-child td {
        font-size: 1.4em;
        border-top: 1px solid {{$quotation->company->color}}
        }

        .invoice table tfoot tr td:first-child {
        border: none
        }

        .invoice footer {
        width: 100%;
        text-align: center;
        font-size: 12px;
        color: #777;
        border-top: 1px solid #aaa;
        padding: 8px 0
        }

        .invoice footer {
        position: absolute;
        bottom: 10px;
        }

        .invoice>div:last-child {
        }

        .th{
          background-color : {{$quotation->company->color}};
          color:white;
        }

        .page-break {
        page-break-after: always;
        }
                        </style>
    </head>
    <body style="margin: 20px auto;">
      <div id="invoice">
        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        <div class="col company-details">
                            <img src="{{ public_path().'/assets/images/logo/'.strtolower($quotation->company->short_form).'.png' }}" data-holder-rendered="true" width="20%" />
                            <div>{{ strtoupper($quotation->company->name) }}</div>
                            <div> +255{{ $quotation->company->phone_number}} | {{ $quotation->company->email_address }}</div>
                            <div>P.O.BOX {{ $quotation->company->postal_address }} </div>
                        </div>
                    </div>
                </header>

                <main>
                    <div class="row contacts">
                        <div class="col invoice-to">
                            <div class="text-gray-light">TO :</div>
                            <h6 class="to"> {{ strtoupper($quotation->customer->first_name) }} {{ strtoupper($quotation->customer->middle_name) }}  {{ strtoupper($quotation->customer->last_name) }} </h6>
                            <div class="address">+255{{ $quotation->customer->mobile }} | {{ $quotation->customer->email }}</div>
                            <div>{{ $quotation->customer->id_number }} ({{ App\Http\Controllers\API\Auth\CustomersController::resolveIdType($quotation->customer->id_type) }})</div>
                            <div>P.O.BOX {{ $quotation->customer->postal_address }} {{ $quotation->customer->country_code}}</div>
                        </div>
                        <div class="col invoice-details">
                          <h2 class="to">Receipt</h2>
                            <div class="datex"><b>Ref :</b> {{ $quotation->payment->reference_number }}</div>
                            <div class="datex"><b>Date :</b> {{ $quotation->payment->created_at->format('d M Y H:i:s') }}</div>
                        </div>
                    </div>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <thead >
                            <tr>
                                <th style="background-color: {{$quotation->company->color}}; color:white;">#</th>
                                <th style="background-color: {{$quotation->company->color}}; color:white;">PRODUCT</th>
                                <th style="background-color: {{$quotation->company->color}}; color:white;" colspan="3">RISK DESCRIPTION</th>
                                <th style="background-color: {{$quotation->company->color}}; color:white;">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="no">01.</td>
                                <td class="text-left">
                                  <h3>{{ strtoupper($quotation->risk->product->name) }}</h3>
                                </td>
                                <td class="unitx" colspan="3">{{ $quotation->risk->name }}
                                  @if($quotation->vehicle_id != null)
                                   <br /><br />
                                    <b>For Vehicle :</b><br />
                                    Registration &nbsp;&nbsp;&nbsp;&nbsp; # : <b>{{ $quotation->vehicle->registration_number }}</b><br />
                                    Chasis Number     # : <b>{{ $quotation->vehicle->chassis_number }}</b><br />
                                    Model & Make      # : <b>{{ $quotation->vehicle->make }} {{ $quotation->vehicle->model }}</b><br />
                                  @endif
                                </td>
                                <td class="total">{{ number_format($quotation->total_premium_excluding_tax,2,'.',',') }}</td>
                            </tr>
                            <tr>
                                <td class="no"></td>
                                <td class="text-left" colspan="4"><b>Paid Amount</b></td>
                                <td class="total">{{ number_format($quotation->payment->paid_amount,2,'.',',') }}</td>
                            </tr>
                            <tr>
                              <td class="no"></td>
                              <td class="text-left" colspan="4"><b>Oustanding Amount</b></td>
                              <td class="total">{{ number_format($quotation->payment->outstanding_amount,2,'.',',') }}</td>
                          </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td colspan="4">Subtotal</td>
                                <td>{{ number_format($quotation->total_premium_excluding_tax,2,'.',',') }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="4">Tax {{ $quotation->risk->tax_rate * 100 }}%</td>
                                <td>{{ number_format(($quotation->total_premium_including_tax - $quotation->total_premium_excluding_tax),2,'.',',') }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="4">GRAND TOTAL</td>
                                <td>{{ number_format($quotation->total_premium_including_tax,2,'.',',') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="notices">
                        <div>NOTICE:</div>
                        <div class="notice">All Figures above are in Tanzania Shillings (TZS)</div>
                    </div>
                </main>
                <footer>
                    Thanks for taking of yourself.
                </footer>
            </div>
            <div></div>
        </div>
    </div>
    <div class="page-break"></div>

    <div id="invoice">
      <div class="invoice overflow-auto">
          <div style="min-width: 600px">
              <header>
                  <div class="row">
                      <div class="col company-details">
                          <img src="{{ public_path().'/assets/images/logo/'.strtolower($quotation->company->short_form).'.png' }}" data-holder-rendered="true" width="20%" />
                          <div>{{ strtoupper($quotation->company->name) }}</div>
                          <div> +255{{ $quotation->company->phone_number}} | {{ $quotation->company->email_address }}</div>
                          <div>P.O.BOX {{ $quotation->company->postal_address }} </div>
                      </div>
                  </div>
              </header>

              <main>
                  <div class="row contacts">
                      <div class="col invoice-to">
                          <div class="text-gray-light">TO :</div>
                          <h6 class="to"> {{ strtoupper($quotation->customer->first_name) }} {{ strtoupper($quotation->customer->middle_name) }}  {{ strtoupper($quotation->customer->last_name) }} </h6>
                          <div class="address">+255{{ $quotation->customer->mobile }} | {{ $quotation->customer->email }}</div>
                          <div>{{ $quotation->customer->id_number }} ({{ App\Http\Controllers\API\Auth\CustomersController::resolveIdType($quotation->customer->id_type) }})</div>
                          <div>P.O.BOX {{ $quotation->customer->postal_address }} {{ $quotation->customer->country_code}}</div>
                      </div>
                      <div class="col invoice-details">
                        <h2 class="to">Covernote</h2>
                          <div class="datex"><b>Ref :</b> {{ $quotation->reference_number }}</div>
                          <div class="datex"><b>Date :</b> {{ $quotation->created_at->format('d M Y H:i:s') }}</div>
                      </div>
                  </div>
                  <table border="0">
                      <thead >
                          <tr>
                              <th style="background-color: {{$quotation->company->color}}; color:white;">ITEM</th>
                              <th style="background-color: {{$quotation->company->color}}; color:white; text-align:right;" >DESCRIPTION</th>
                          </tr>
                      </thead>
                      <tbody>

                        <tr>
                          <td><h3>Covernote Type</h3></td>
                          <td align="right">
                            <h3><b>{{ $quotation->cover_note_type == 1 ? 'New' : ($quotation->cover_note_type == 2 ? 'Renew' : 'Endorsement') }}<b></h3>
                          </td>
                       </tr> 


                      <tr>
                        <td><h3>Covernote Start Date</h3></td>
                        <td align="right">
                          <h3><b>{{ $quotation->start_date->format('d M Y H:i:s') }}</b></h3>
                        </td>
                      </tr> 

                      <tr>
                        <td><h3>Covernote End Date</h3></td>
                        <td align="right">
                          <h3><b>{{ $quotation->end_date->format('d M Y H:i:s') }}</b></h3>
                        </td>
                      </tr> 
                    <tr>
                      <td><h3>Covernote Product</h3></td>
                      <td align="right">
                        <h3><b>{{ strtoupper($quotation->risk->product->name) }}</b></h3>
                      </td>
                    </tr> 

                  <tr>
                    <td><h3>Risk Covered</h3></td>
                    <td align="right">
                      <h3><b>{{ $quotation->risk->name }}</b></h3>
                    </td>
                  </tr> 

                  <tr>
                    <td><h3>Sum Insured</h3></td>
                    <td align="right">
                      <h3><b>{{ number_format($quotation->sum_insured, 2,'.',',') }}</b></h3>
                    </td>
                  </tr> 


                  <tr>
                    <td><h3>Premium Rate</h3></td>
                    <td align="right">
                      <h3><b>{{ $quotation->premium_rate * 100 }}%</b></h3>
                    </td>
                  </tr> 

                  <tr>
                    <td><h3>Premium Before Tax</h3></td>
                    <td align="right">
                      <h3><b>{{ number_format($quotation->total_premium_excluding_tax, 2,'.',',') }}</b></h3>
                    </td>
                  </tr> 

                  <tr>
                    <td><h3>Premium After Tax</h3></td>
                    <td align="right">
                      <h3><b>{{ number_format($quotation->total_premium_including_tax, 2,'.',',') }}</b></h3>
                    </td>
                  </tr> 

                  {{-- <tr>
                    <td><h3>Operative Clause</h3></td>
                    <td align="right">
                      <h3><b>{{ $quotation->operative_clause }}</b></h3>
                    </td>
                  </tr> 


                  <tr>
                    <td><h3>Subject Matter Reference</h3></td>
                    <td align="right">
                      <h3><b>{{ $quotation->subject_matter_reference }}</b></h3>
                    </td>
                  </tr>  --}}

                  <tr>
                    <td><h3>Covernote Reference #</h3></td>
                    <td align="right">
                      <h3><b>{{ $quotation->covernote_reference_number }}</b></h3>
                    </td>
                  </tr> 

                  <tr>
                    <td><h3>Sticker Number</h3></td>
                    <td align="right">
                      <h3><b>{{ $quotation->sticker_number }}</b></h3>
                    </td>
                  </tr> 

                  <tr>
                    <td><h3>Status</h3></td>
                    <td align="right">
                      <h3><b>{{ $quotation->status }}</b></h3>
                    </td>
                  </tr> 

                  </tbody>
                  </table>
                  <div class="notices">
                      <div>NOTICE:</div>
                      <div class="notice">All Figures above are in Tanzania Shillings (TZS)</div>
                  </div>
              </main>
              <footer>
                  Thanks for taking of yourself.
              </footer>
          </div>
          <div></div>
      </div>
  </div>

    </body>
</html>

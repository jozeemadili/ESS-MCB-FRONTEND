@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
@endpush

@section('content')
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-header">
                    <form action="{{ Route('policies-quotations-verify') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="form-group">
                            <label>Search By</label><br />
                            <input type="radio" checked class="radio_animated" value={{ old('motor_registration_number') }} onChange="searchingbyChanged(this)" id="searching_by" name="searching_by"> Plate #
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value={{ old('motor_chassis_number') }} onChange="searchingbyChanged(this)" id="searching_by" name="searching_by"> Chasis #
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value={{ old('covernote_reference_number') }} onChange="searchingbyChanged(this)" id="searching_by" name="searching_by"> Covernote #
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="radio_animated" value={{ old('sticker_number') }} value="" onChange="searchingbyChanged(this)" id="searching_by" name="searching_by"> Sticker #
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label></label><br />
                                <div class="input-group" style="" id="motor_registration_number" >
                                    <input name="motor_registration_number" class="form-control" type="text" placeholder="Registration Number ...">
                                </div>
                                <div class="input-group" style="display: none;"  id="motor_chassis_number" >
                                    <input name="motor_chassis_number" class="form-control" type="text" placeholder="Chasis Number ...">
                                </div>
                                <div class="input-group" style="display: none;" id="covernote_reference_number" >
                                    <input name="covernote_reference_number" class="form-control" type="text" placeholder="Covernote Reference Number ...">
                                </div>
                                <div class="input-group" style="display: none;" id="sticker_number" >
                                   <input name="sticker_number" class="form-control" type="text" placeholder="Sticker Number ...">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label></label><br />
                                <button type="submit" class="btn btn-outline-primary btn-sm">Verify <i class="icofont icofont-search"> </i></button>
                            </div>
                        </div>
                    </div>
                    </form>
                    <hr />
                  </div>
                  <div class="card-body" style="margin-top:-60px;">
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                          <i class="icon-info-alt txt-danger"></i>
                              {!! $error !!}
                          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                          </div>
                        @endforeach
                        
                        @if($message = Session::get('success'))
                          @if($message->data->CoverNoteHdr->ResponseStatusDesc == 'Successful')
                            <div class="row">
                                <div class="col-lg-7">
                                            <div class="card card-absolute">
                                                <div class="card-header bg-default">
                                                    <h5 class="text-dark">Covernote </h5>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-group">
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Type<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->CoverNoteTypeDesc }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Covernote #<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->CoverNoteNumber }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Covernote Ref #<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->CoverNoteReferenceNumber }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Insurer Company Code<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->InsurerCompanyCode }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Insurer Name<span class="badge badge-primary rounded-pill">{{  $message->data->CoverNoteDtl->InsurerCompanyName }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Covernote Issuing Date<span class="badge badge-primary rounded-pill">{{ date_format(date_create($message->data->CoverNoteDtl->CoverNoteIssueDate), 'd M Y H:i:s') }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Covernote Start Date<span class="badge badge-primary rounded-pill">{{ date_format(date_create($message->data->CoverNoteDtl->CoverNoteStartDate), 'd M Y H:i:s') }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Covernote End Date<span class="badge badge-primary rounded-pill">{{ date_format(date_create($message->data->CoverNoteDtl->CoverNoteEndDate), 'd M Y H:i:s') }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Description<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->CoverNoteDesc }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Operative Clause<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->OperativeClause }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Currency Code<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->CurrencyCode }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Sum Insured<span class="badge badge-primary rounded-pill">{{ number_format($message->data->CoverNoteDtl->RisksCovered->RiskCovered->SumInsured, 2, '.',',') }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Premium Rate<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->RisksCovered->RiskCovered->PremiumRate * 100 }}%</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Total Premium Excluding Tax<span class="badge badge-primary rounded-pill">{{ number_format($message->data->CoverNoteDtl->TotalPremiumExcludingTax, 2,'.',',') }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Total Premium Including<span class="badge badge-primary rounded-pill">{{ number_format($message->data->CoverNoteDtl->TotalPremiumIncludingTax,2, '.',',') }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Product Code<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->ProductCode }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Product Name<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->ProductName }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Risk Code<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->RisksCovered->RiskCovered->RiskCode }}</span></li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">Risk Name<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->RisksCovered->RiskCovered->RiskName }}</span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="card card-absolute">
                                        <div class="card-header bg-default">
                                            <h5 class="text-dark">Policy Holder & Property </h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                @if(isset($message->data->CoverNoteDtl->PolicyHolders->PolicyHolder))
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Name<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->PolicyHolderName }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Type<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->PolicyHolderTypeDesc }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">ID Number<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->PolicyHolderIdNumber }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">ID Type<span class="badge badge-primary rounded-pill">{{  $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->PolicyHolderIdTypeDesc }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Gender<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->Gender }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Country Code<span class="badge badge-primary rounded-pill">{{  $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->CountryCode  }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Region<span class="badge badge-primary rounded-pill">{{  $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->Region  }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">District<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->District }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Street<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->Street }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Phone Number<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->PolicyHolderPhoneNumber }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Postal Address<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->PostalAddress }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">Premium Rate<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->PolicyHolders->PolicyHolder->EmailAddress}}</span></li>
                                                @endif
                                                @if(isset($message->data->CoverNoteDtl->MotorDtl))
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">Vehicle Category<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->MotorDtl->MotorCategoryDesc}} | {{ $message->data->CoverNoteDtl->MotorDtl->MotorUsageDesc}} </span></li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">Vehicle Registration #<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->MotorDtl->RegistrationNumber}}</span></li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">Vehicle Chasis #<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->MotorDtl->ChassisNumber}}</span></li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">Vehicle Engine #<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->MotorDtl->EngineNumber}}</span></li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">Vehicle Make & Model<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->MotorDtl->Make}} {{ $message->data->CoverNoteDtl->MotorDtl->Model}}</span></li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">Vehicle Fuel Used<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->MotorDtl->FuelUsed}}</span></li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">Vehicle Body & Color<span class="badge badge-primary rounded-pill">{{ $message->data->CoverNoteDtl->MotorDtl->BodyType}} {{ $message->data->CoverNoteDtl->MotorDtl->Color}} </span></li>
                                                    
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                          @else
                          <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                          <i class="icofont icon-info-alt text-danger"></i>
                              {!! $message->data->CoverNoteHdr->ResponseStatusDesc !!}
                          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                          </div>
                          <br />
                          @endif
                          @endif

                          @if($message = Session::get('error'))
                          <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                          <i class="icon-info-alt text-danger"></i>
                              {!! $message !!}
                          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                          </div>
                          <br />
                          @endif
                  </div>
              </div>
          </div>
      </div>
  </div>
  @push('scripts')
 <script>
    function searchingbyChanged(searchingBy)
    {
        if(searchingBy.value == 'motor_registration_number'){
            document.getElementById("motor_registration_number").style.display = "";
            document.getElementById("motor_chassis_number").style.display = "none";
            document.getElementById("covernote_reference_number").style.display = "none";
            document.getElementById("sticker_number").style.display = "none";
        }
        else if(searchingBy.value == 'motor_chassis_number'){
            document.getElementById("motor_registration_number").style.display = "none";
            document.getElementById("motor_chassis_number").style.display = "";
            document.getElementById("covernote_reference_number").style.display = "none";
            document.getElementById("sticker_number").style.display = "none";
        }
        else  if(searchingBy.value == 'covernote_reference_number'){
            document.getElementById("motor_registration_number").style.display = "none";
            document.getElementById("motor_chassis_number").style.display = "none";
            document.getElementById("covernote_reference_number").style.display = "";
            document.getElementById("sticker_number").style.display = "none";
        }
        else{
            document.getElementById("motor_registration_number").style.display = "none";
            document.getElementById("motor_chassis_number").style.display = "none";
            document.getElementById("covernote_reference_number").style.display = "none";
            document.getElementById("sticker_number").style.display = "";
        }
    }
    </script>
  @endpush
@endsection
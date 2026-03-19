@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot


    @slot('breadcrumb_action_buttons')
    @if(Auth::user()->role == 'ADMIN' && Auth::user()->company->id == 1)
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
    @endif
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
                  
                        @if(count($products)>0)
                        
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Code</th>
                                    <th scope="col">Name</th>
									<!-- <th scope="col">Description</th> -->
                                    <th scope="col">Minimum Tenure (Months)</th>
                                    <th scope="col">Maximum Tenure (Months)</th>
                                    <th scope="col">processing Fee Rate (%)</th>
                                    <th scope="col">insurance Rate (%)</th>
                                    <th scope="col">minimum Amount</th>
                                    <th scope="col">maximum Amount</th>
                                    <th scope="col">Repayment Type</th>
                                    <th scope="col">Interest Rate (%)</th>
                                    <th scope="col">Reg Date</th>
								</tr>
            
							</thead>
							<tbody>
                                @foreach($products as $product)
								<tr>
                                <!-- `ID`, ``, `CURRENCY`, `CODE`, ``, ``, `DECOMMISSION_REASON`, `DESCRIPTION`, `INSURANCE_RATE`, `IS_EXECUTIVE`, ``, `MAXIMUM_TENURE`, ``, `MINIMUM_TENURE`, `NAME`, ``, ``,  -->
									<th scope="row">{{$loop->index + 1}}.</th>
									<td>{{$product['CODE']}}</td>
									<td><a href="get/condition/{{$product['ID']}}">{{strtoupper($product['NAME'])}}</a></td>
                                    <td>{{$product['MAXIMUM_TENURE']}}</td>
                                    <td>{{$product['MINIMUM_TENURE']}}</td>
                                    <td>{{$product['PROCESSING_FEE_RATE']}}</td>
                                    <td>{{$product['INSURANCE_RATE']}}</td>
                                    <td>{{number_format($product['MINIMUM_AMOUNT'], 2, '.', ',')}} {{$product['CURRENCY']}}</td>
                                    <td>{{number_format($product['MAXIMUM_AMOUNT'], 2, '.', ',')}} {{$product['CURRENCY']}}</td>
                                    <td>{{$product['REPAYMENT_TYPE']}}</td>
                                    <td>{{$product['INTEREST_RATE']}}</td>
                                    <td>{{ Carbon\Carbon::parse($product['DATE'])->diffForHumans()}}</td>
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
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <!-- <div class="modal-dialog" role="document"> -->
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Product Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="/v1/products/add">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="col-form-label" >Product Name</label>
                                <input class="form-control" type="text"  required  name="name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Product Code</label>
                                <input class="form-control" type="text"  required  name="code" >
                            </div>
                            <!-- <div class="form-group">
                                <label class="col-form-label" >Description</label>
                                <input class="form-control" type="text"  required  name="description" >
                            </div> -->
                            <div class="form-group">
                                <label class="col-form-label">Is Executive ? </label><br>
                                <input type="radio" checked class="radio_animated" value="1" name="isExecutive"> Yes
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="0" name="isExecutive"> No
                            </div>
                        </div>
                        <div class="col-lg-3">
                
                            <div class="form-group">
                                <label class="col-form-label" >Minimum Tenure (Months)</label>
                                <input class="form-control" type="number"  required  name="minimumTenure">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Maximum Tenure (Months)</label>
                                <input class="form-control" type="number"  required  name="maximumTenure" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Interest Rate (%)</label>
                                <input class="form-control" type="number" step="any" required  name="InterestRate" >
                            </div>
                        </div>
                        <div class="col-lg-3">
       
                            <div class="form-group">
                                <label class="col-form-label" >Processing Fee Rate (%)</label>
                                <input class="form-control" type="number"  required step="any"  name="processingFeeRate">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Insurance Rate (%)</label>
                                <input class="form-control" type="number" step="any" required  name="insuranceRate" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Minimum Amount</label>
                                <input class="form-control" type="number"  required  name="minimumAmount" >
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="col-form-label" >Maximum Amount</label>
                                <input class="form-control" type="number" required  name="maximumAmount">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Currency</label>
                                <select class="form-select" required  name="ccy">
                                <option value="">--- Choose Currency ---</option>    
                                <option>TZS</option>
                                    <option>USD</option>
                                    <option>GBP</option>
								</select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Repayment Type</label>
                                <select class="form-select" required  name="repaymentType">
                                <option value="">--- Repayment Type  ---</option>    
                                <option>Salary</option>
								</select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                       
                        </div>
                        <div class="col-lg-6">
                        <div class="form-group">
                                <div class="form-group">
                                    <label class="col-form-label" >Description</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW MODAL END -->

  @push('scripts')
  @endpush
@endsection
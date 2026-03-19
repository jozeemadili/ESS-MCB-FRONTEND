<div class="card">
	                    <div class="card-body">
	                      @if(isset($products))
	                            <div class="row mb-2">
	                                <div class="profile-title">
	                                    <div class="media">
	                                        <!-- <img class="img-70 rounded-circle" alt="" src="{{ asset('assets/images/dashboard/1.png') }}"> -->
	                                        <div class="media-body">
	                                            <h3 class="mb-1 f-20 txt-primary">{{$products->NAME}} </h3>
	                                            <p class="f-12">Product Details With Product Code <b>{{$products->CODE}} </b></p>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                            
                                <div class="table-responsive">
                    <table class="table table-sm">
                     
                    <!-- SELECT `ID`, `INTEREST_RATE`, `CURRENCY`, `CODE`, `DATE`, `DECOMMISSION_DATE`, `DECOMMISSION_REASON`, `DESCRIPTION`, `INSURANCE_RATE`, `IS_EXECUTIVE`, `MAXIMUM_AMOUNT`, `MAXIMUM_TENURE`, `MINIMUM_AMOUNT`, `MINIMUM_TENURE`, `NAME`, `PROCESSING_FEE_RATE`, `REPAYMENT_TYPE`, `STATUS` FROM `PRODUCT` WHERE 1 -->
                      <tr>
                        <th>Maximum Amount</th>
                        <td>{{number_format($products['MAXIMUM_AMOUNT'], 2, '.', ',')}}  {{$products->CURRENCY}}</td>
                      </tr>
                      <tr>
                        <th>Minimum Amount</th>
                        <td>{{number_format($products['MINIMUM_AMOUNT'], 2, '.', ',')}}  {{$products->CURRENCY}}</td>
                      </tr>
                      <tr>
                        <th> Maximum Tenure</th>
                        <td>{{$products->MAXIMUM_TENURE}} Months</i></td>
                      </tr>
                      <tr>
                        <th>Minimum Tenure</th>
                        <td>{{$products->MINIMUM_TENURE}} Months</i></td>
                      </tr>
                      <tr>
                        <th>Interest Rate</th>
                        <td>{{$products->INTEREST_RATE}} % </i></td>
                      </tr>
                      <tr>
                        <th>Insurance Rate</th>
                        <td>{{$products->INSURANCE_RATE}} %</i></td>
                      </tr>
                      <tr>
                        <th>Proccesing Fee Rate</th>
                        <td>{{$products->PROCESSING_FEE_RATE}} % </i></td>
                      </tr>
                      <tr>
                        <th>Repayment</th>
                        <td>{{$products->REPAYMENT_TYPE}}</i></td>
                      </tr>
                      <!-- `, `DECOMMISSION_DATE`, `DECOMMISSION_REASON`, `DESCRIPTION` -->
                      <tr>
                        <th> Registration Date</th>
                        <td>{{ $products->DATE->format('d M Y, H:i:s') }}</td>
                      </tr>
                      <tr>
                        <th>Status</th>
                        <td>{{$products->STATUS}}</td>
                      </tr>
                      <tr>
                        <th>Action</th>
                        <td>
                            @if($products->STATUS == 'Not Published' || $products->STATUS == 'Decommissioned')
                            <button wire:click="publishProduct" class="btn btn-outline-primary btn-xs pull-left"  type="button" wire:loading.remove> Publish <i class="icofont icofont-ui-rate-add"></i></button>
                            @elseif($products->STATUS == 'Published')
                            <button wire:click="decamisionProduct" class="btn btn-outline-danger btn-xs pull-left"  type="button" wire:loading.remove> Decamission <i class="icofont icofont-ui-rate-remove"></i></button>     
                            @else  
                              @endif
                              <button class="btn btn-outline-success btn-xs pull-right" data-bs-toggle="modal" data-bs-target="#newModalEdit"> Edit Product <i class="icofont icofont-ui-edit"></i></button>
                              
    
                        </td>
                      </tr>
                    </table>
                    
                    <div wire:loading.delay>
                        <div class="loader-box">
                            <div class="loader-7" style="width: 50px; height:50px;"></div>
                            <br/>
                                <h5 class="f-w-100">Processing ...</h5>
                            </div>
                        </div>
                    </div>
                      
                      @endif
                
                        

                              
              </div>
              </div>
              <div>
    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <!-- Edit Product Modal -->
    <div class="modal fade" id="newModalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Edit Product</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form method="POST" action="/v1/products/edit">
    @csrf
    <input type="hidden" name="productId" value="{{ $productId }}">

    <div class="form-group">
        <label>Product Name</label>
        <input class="form-control" type="text" name="name" value="{{ old('name', $products->NAME) }}">
        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Product Code</label>
        <input class="form-control" type="text" name="code" value="{{ old('code', $products->CODE) }}" disabled>
        @error('code') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Minimum Tenure (Months)</label>
        <input class="form-control" type="number" name="minimumTenure" value="{{ old('minimumTenure', $products->MINIMUM_TENURE) }}">
        @error('minimumTenure') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Maximum Tenure (Months)</label>
        <input class="form-control" type="number" name="maximumTenure" value="{{ old('maximumTenure', $products->MAXIMUM_TENURE) }}">
        @error('maximumTenure') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Interest Rate (%)</label>
        <input class="form-control" type="number" name="interestRate" step="any" value="{{ old('interestRate', $products->INTEREST_RATE) }}">
        @error('interestRate') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Processing Fee Rate (%)</label>
        <input class="form-control" type="number" name="processingFeeRate" step="any" value="{{ old('processingFeeRate', $products->PROCESSING_FEE_RATE) }}">
        @error('processingFeeRate') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Minimum Amount</label>
        <input class="form-control" type="number" name="minimumAmount" value="{{ old('minimumAmount', $products->MINIMUM_AMOUNT) }}">
        @error('minimumAmount') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Maximum Amount</label>
        <input class="form-control" type="number" name="maximumAmount" value="{{ old('maximumAmount', $products->MAXIMUM_AMOUNT) }}">
        @error('maximumAmount') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea class="form-control" name="description">{{ old('description', $products->description) }}</textarea>
        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <button class="btn btn-primary" type="submit">Update Product</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

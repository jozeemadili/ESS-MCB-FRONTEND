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

    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent

  <div class="container-fluid">
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
	    <div class="edit-profile">
	        <div class="row">
	            <div class="col-xl-4">

              @livewire('product-manage',['products' => $products, 'productId' => $id])

              </div>
              
              
	            <div class="col-xl-8">
                <div class="card">
                  <div class="card-body">

                    <div class="row">
                      <div class="profile-title">
                          <div class="media">
                              <div class="media-body">
                                  <h3 class="mb-1 f-20 txt-primary">Product Conditions</h3>
                                  <p class="f-12">List Of Conditions</p>
                                  @if($products->STATUS == 'Not Published' || $products->STATUS == 'Decommissioned')
                                  <div class="pull-right"><a href='' data-bs-toggle="modal" data-bs-target="#newModal">New +</a></div>
                                  @endif
                              </div>
                          </div>
                    </div>
                  </div>
                  
                  <div class="table-responsive">
                      @if(count($responseData)>0)
                      <table class="table table-xs">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">C NO</th>  
                          <th scope="col">Description</th>            
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
							        <tbody>
                        @foreach($responseData as $product)
                        <tr>
                          <th scope="row">{{$loop->index + 1}}.</th>	
                          <td><a href='#'>{{$product['CONDITION_NUMBER']}}</a></td>	
                          <td><a href='#'>{{$product['DESCRIPTION']}}</a></td>                
                          <td>
                                  @if($products->STATUS == 'Not Published' || $products->STATUS == 'Decommissioned')
                                      <div class="pull-right"> <a href='{!! Route('product-condition-update', ['id' => $product->ID, 'status' => 'Inactive']) !!}' class='btn btn-outline-danger btn-xs'>Delete <i class="icofont icofont-ui-delete"></i></a></div>
                                        @else  
                                        <div class="pull-right"> <a href='' class='btn btn-outline-primary btn-xs'> Published <i class="icofont icofont-ui-rating"></i></a></div>
                                  @endif
                                  
                          </td>                                                
                        </tr>
                        @endforeach
							        </tbody>
						          </table>
                      @else
                          <p>No Product Conditions.</p>
                      @endif

    
                        
					        </div>
                  </div>
                </div>
	            </div>
	        </div>

      
</div>
	</div>
  </div>

  

 						<!-- NEW MODAL START -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Condition Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="/v1/products/condition/add">
                    @csrf 
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="form-group">
                                <label class="col-form-label" hidden>id</label>
                                <input class="form-control" hidden type="number" value="{{$id}}"  required  name="id">
                            </div>    
                            <div class="form-group">
                                    <label class="col-form-label" >Description</label>
                                    <textarea class="form-control" type="text"  required  name="description"></textarea>
                              </div>
                            <!-- <div class="form-group">
                                <label class="col-form-label" >Description </label>
                                <input class="form-control" type="text"  required  name="description" >
                            </div> -->
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
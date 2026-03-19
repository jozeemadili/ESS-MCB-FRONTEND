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
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
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
                        {{ $message }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					@endif

                      <p>
                      <div class="table-responsive">
                        @if(count($plans)>0)
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
                                    <th scope="col">Product</th>
									<th scope="col">Class</th>
                                    <th scope="col">Type</th>
									<th scope="col">Sub-Type</th>
                                    <th scope="col">Reg At</th>
                                    <th scope="col">Status</th>
								</tr>
							</thead>
							<tbody>
                                @foreach($plans as $plan)
								<tr>
									<th scope="row">{{$loop->index + 1}}.</th>
									<td>{{strtoupper($plan->product->name)}}</td>
                                    <td><a href='#'>{{ucfirst($plan->cover_class)}}</a></td>
									<td><a href='#'>{{ucfirst($plan->cover_type)}}</a></td>
                                    <td><a href='#'>{{ucfirst($plan->cover_sub_type)}}</a></td>
                                    <td>{{$plan->created_at->format('d/m/y')}}</td>
                                    <td>{{$plan->status}}</td>
								</tr>
                                @endforeach
							</tbody>
						</table>
                        <br />
                        {{ $plans->links() }}

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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Plan Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="/v1/plans/add">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label" >Product</label>
                                <select class="form-control" required value="{{ old('product_id') }}" name="product_id">
                                <option value="">--- Choose Product ---</option>  
                                @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
								</select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Cover Class</label>
                                <input class="form-control" type="text" value="{{ old('cover_class') }}" required  name="cover_class">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Cover Type</label>
                                <input class="form-control" type="text" value="{{ old('cover_type') }}" required  name="cover_type" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Cover Sub-Type</label>
                                <input class="form-control" type="text" value="{{ old('cover_sub_type') }}" required  name="cover_sub_type" >
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
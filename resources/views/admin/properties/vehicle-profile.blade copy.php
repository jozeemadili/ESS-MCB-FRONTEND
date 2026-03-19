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
    <li><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li>
    <li><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
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
                        @if($vehicle)
						<table class="table table-xs">
							<thead>
								<tr>
                                    <th scope="col">Owner</th>
                                    <th scope="col">Reg #</th>
									<th scope="col">Chasis #</th>
                                    <th scope="col">Make</th>
                                    <th scope="col">Model</th>
                                    <th scope="col">Body</th>
                                    <th scope="col">Capacity</th>
                                    <th scope="col">Fuel</th>
                                    <th scope="col">YOM</th>
                                    <th scope="col">Usage</th>
								</tr>
							</thead>
							<tbody>
								<tr>
                                    <td><a href='#'><small>{{strtoupper(explode(' ',$vehicle->owner_name)[0] .' '. (explode(' ',$vehicle->owner_name)[count(explode(' ',$vehicle->owner_name))-1]))}}</small></a></td>
									<td><a href='#'>{{$vehicle->registration_number}}</a></td>
                                    <td><a href='#'>{{$vehicle->chassis_number}}</a></td>
                                    <td>{{$vehicle->make}}</td>
                                    <td>{{$vehicle->model}}</td>
                                    <td>{{$vehicle->body_type}}</td>
                                    <td>{{number_format($vehicle->engine_capacity, 2, '.',',')}}</td>
                                    <td>{{$vehicle->fuel_used}}</td>
                                    <td>{{$vehicle->year_of_manufacture}}</td>
                                    <td>{{($vehicle->motor_usage == 1 ? "Private" : "Commercial")}}</td>
								</tr>
							</tbody>
						</table>
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vehicle Registration</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="/v1/properties/vehicles/add">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label" >Registration Number</label>
                                <input class="form-control" type="text" value="{{ old('registration_number') }}"  name="registration_number" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Chassis Number</label>
                                <input class="form-control" type="text" value="{{ old('chassis_number') }}" required  name="chassis_number">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Make</label>
                                <input class="form-control" type="text" value="{{ old('make') }}" required  name="make" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Model</label>
                                <input class="form-control" type="text" value="{{ old('model') }}" required  name="model" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Model Number</label>
                                <input class="form-control" type="text" value="{{ old('model_number') }}" required  name="model_number" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Motor Category</label><br />
                                <input type="radio" checked class="radio_animated" value="1" name="motor_category"> Motor vehicle
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="2" name="motor_category"> Motor cycle
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Motor Category</label><br />
                                <input type="radio" class="radio_animated" value="1" name="motor_type"> Registered
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" checked class="radio_animated" value="2" name="motor_type"> In Transit
                            </div>

                        </div>
                        
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label" >Body Type</label>
                                <input class="form-control" type="text" value="{{ old('body_type') }}" required  name="body_type">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Color</label>
                                <input class="form-control" type="text" value="{{ old('color') }}" required  name="color" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Engine Number</label>
                                <input class="form-control" type="text" value="{{ old('engine_number') }}" required  name="engine_number" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Engine Capacity</label>
                                <input class="form-control" type="number" min="10" value="{{ old('engine_capacity') }}" required  name="engine_capacity">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Fuel Used</label>
                                <select class="form-select" required value="{{ old('fuel_used') }}" name="fuel_used">
                                    <option value="">--- Choose Fuel ---</option>    
                                        <option value="Petrol">Petrol</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Electricity">Electricity</option>
                                    </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Sitting Capacity</label>
                                <input class="form-control" type="number" min="1" max="200" value="{{ old('sitting_capacity') }}" required  name="sitting_capacity" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Motor Usage</label><br />
                                <input type="radio" class="radio_animated" value="1" name="motor_usage"> Private
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" checked class="radio_animated" value="2" name="motor_usage"> Commercial
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label" >Number of Axles</label>
                                <input class="form-control" type="number" step="0.01" min="1" value="{{ old('number_of_axles') }}" required  name="number_of_axles">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Axle Distance</label>
                                <input class="form-control" type="number" min="1" step="0.01" value="{{ old('axle_distance') }}" required  name="axle_distance" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Year of Manufacture</label>
                                <input class="form-control" type="number" min="1900" max="{{ date('Y') }}" value="{{ old('year_of_manufacture') }}" required  name="year_of_manufacture" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Tare Weight</label>
                                <input class="form-control" type="number" min="0" step="0.01" value="{{ old('tare_weight') }}" required  name="tare_weight">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Gross Weight</label>
                                <input class="form-control" type="number" min="0" step="0.01" value="{{ old('gross_weight') }}" required  name="gross_weight" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Owner Name</label>
                                <input class="form-control" type="text" minlength="3" value="{{ old('owner_name') }}" required  name="owner_name" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Owner Category</label><br />
                                <input type="radio" class="radio_animated" value="1" name="owner_category"> Sole proprietor
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" checked class="radio_animated" value="2" name="owner_category"> Corporate
                             </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label" >Owner Address</label>
                                <textarea class="form-control" value="{{ old('owner_address') }}"  name="owner_address"></textarea>
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


  <!-- SEARCH MODAL START -->
  <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vehicle Search</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ url()->current() }}">
                    @csrf

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">Search By</label><br>
                                <input type="radio" checked class="radio_animated" value="registration_number" name="search_by" id="search_by" onChange="searchBy(this)"> Registration No.
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="chasis_number" name="search_by" id="search_by" onChange="searchBy(this)"> Chasis No.
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input class="form-control" type="text" value="{{ old('reference_number') }}" required id="reference_number">
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Search</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

  @push('scripts')
  <script>
    document.getElementById("reference_number").placeholder = "Enter Vehicle's Registration Number ...";
    document.getElementById('reference_number').name = 'registration_number';
    function searchBy(search_by)
    {
        if(search_by.value == "chasis_number"){
            document.getElementById("reference_number").placeholder = "Enter Vehicle's Chasis Number ...";
            document.getElementById('reference_number').name = 'chasis_number';
        }
        else{
            document.getElementById("reference_number").placeholder = "Enter Vehicle's Registration Number ...";
            document.getElementById('reference_number').name = 'registration_number';
        }
    }
    </script>
  @endpush
@endsection
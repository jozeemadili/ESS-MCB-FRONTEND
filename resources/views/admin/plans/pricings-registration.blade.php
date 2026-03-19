@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2.css') }}">
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
                        @if(count($pricings)>0)
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
                                    <th scope="col">Plan</th>
									<th scope="col">Risk</th>
                                    <th scope="col">Comm. Rate</th>
									<th scope="col">Disc. Rate</th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Reg At</th>
                                    <th scope="col">Status</th>
								</tr>
							</thead>
							<tbody>
                                @foreach($pricings as $pricing)
								<tr>
									<th scope="row">{{$loop->index + 1}}.</th>
									<td><a href='#'>{{strtoupper($pricing->plan->cover_class)}}</a></td>
                                    <td><a href='#'>{{ucfirst($pricing->risk->name)}}</a></td>
									<td>{{number_format($pricing->commision_rate, 2, '.',',')}}</td>
                                    <td>{{number_format($pricing->discount_rate, 2, '.',',')}}</td>
                                    <td>{{ucfirst($pricing->company->name)}}</td>
                                    <td>{{$pricing->created_at->format('d/m/y')}}</td>
                                    <td>{{$pricing->status}}</td>
								</tr>
                                @endforeach
							</tbody>
						</table>
                        <br />
                        {{ $pricings->links() }}

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
 <div class="modal fade" id="newModal" style="overflow:hidden;" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Pricing Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="/v1/plans/pricings/add">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">

                            @livewire('components.plan-risk')

                            <div class="form-group">
                                <input class="form-control" type="hidden" value="{{ Auth::user()->company_id }}" required  name="company_id">
                            </div>

                            <div class="form-group">
                                <label class="col-form-label" >Commission Rate</label>
                                <input class="form-control" type="number" step="0.01" value="{{ old('commision_rate') }}" required  name="commision_rate">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Discount Rate</label>
                                <input class="form-control" type="number" step="0.01" value="{{ old('discount_rate') }}" required  name="discount_rate">
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
 <script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>
 <script src="{{ asset('assets/js/select2/select2-custom.js') }}"></script>
 @endpush
@endsection
<div class="rowx">
    <div class="col-lg-12">
        <div class="card card-absolute">
            <div class="card-body">
               <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">Identity No.<span class="badge badge-primary rounded-pill">{{ $customer->id_number }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Identity Type<span class="badge badge-primary rounded-pill">{{ App\Http\Controllers\API\Auth\CustomersController::resolveIdType($customer->id_type) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">First Name<span class="badge badge-primary rounded-pill">{{ strtoupper($customer->first_name) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Middle Name<span class="badge badge-primary rounded-pill">{{ strtoupper($customer->middle_name) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Last Name<span class="badge badge-primary rounded-pill">{{  strtoupper($customer->last_name) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Gender<span class="badge badge-primary rounded-pill">{{ $customer->gender }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Date of Birth<span class="badge badge-primary rounded-pill">{{ $customer->dob->format('d/m/Y') }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Joined At<span class="badge badge-primary rounded-pill">{{ $customer->created_at->format('d/m/Y H:i:s') }}</span></li>
                        {{-- <li class="list-group-item d-flex justify-content-between align-items-center">Role<span class="badge badge-primary rounded-pill">{{ $customer->role }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Status<span class="badge badge-primary rounded-pill">{{ $customer->status }}</span></li> --}}
                    </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Customer Type<span class="badge badge-primary rounded-pill">{{ $customer->type == 1 ? 'Individual' : 'Corporate' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Country Code<span class="badge badge-primary rounded-pill">{{ $customer->country_code }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Phone Number<span class="badge badge-primary rounded-pill">{{ $customer->mobile }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Email<span class="badge badge-primary rounded-pill">{{ $customer->email }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Postal Address<span class="badge badge-primary rounded-pill">{{ $customer->postal_address }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Region<span class="badge badge-primary rounded-pill">{{ $customer->ward->district->region->name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">District<span class="badge badge-primary rounded-pill">{{ $customer->ward->district->name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Ward<span class="badge badge-primary rounded-pill">{{ $customer->ward->name }}</span></li>
                        </ul>
                        </div>
                </div>
            </div>
        </div>
    </div>
    </div>
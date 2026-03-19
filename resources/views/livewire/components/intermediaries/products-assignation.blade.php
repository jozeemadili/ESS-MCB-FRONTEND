<div>
    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="onebyone-tab" data-bs-toggle="tab" href="#onebyone" role="tab" aria-controls="onebyone" aria-selected="true"><i class="icofont icofont-worker"></i>One by One</a></li>
        <li class="nav-item"><a class="nav-link" id="profile-top-tab" data-bs-toggle="tab" href="#bulky" role="tab" aria-controls="bulky" aria-selected="false"><i class="icofont icofont-worker-group"></i>Wizard</a></li>
    </ul>

    <div class="tab-content" id="top-tabContent">

        <div class="tab-pane fade active show" id="onebyone" role="tabpanel" aria-labelledby="onebyone-tab">
         
            <div class="row">

            <div class="col-lg-5">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Search by Name, Code, and Premium rate ..." wire:model="riskquery">
                @if(count($risks)>0)
                <br />
                <div class="list-group">
                    @foreach ($risks as $risk)
                    <a wire:click="selectrisk({{ $risk }})" class="list-group-item list-group-item-action" href="javascript:void(0)" data-bs-original-title="">
                        <div class="d-flex w-100 justify-content-between">
                            <small><b>{{ $risk->code }} - {{ ($risk->name) }}</b></small>
                            <span class="badge badge-success rounded-pill counter">{{ $loop->index+1 }}</span>
                        </div>
                    <small class="text-muted">{{ $risk->product->name }} </small>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            </div>

            <div class="col-lg-7">

                <div class="ribbon-wrapper card">
                    <div class="card-body">
                        <div class="ribbon ribbon-clip ribbon-primary">Selected Risk(s)</div>
                        <div class="row">
                            
                            <div class="table-responsive"   style="height:600px; overflow:scroll;">
                            <table class="table table-xs">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>S/N</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedrisks as $selectedrisk)
                                        <tr>
                                            <td><input type="checkbox" class="checkbox_animated" wire:model="iselectedrisks" value="{{ json_encode($selectedrisk) }}"></td>
                                            <td>{{ $loop->index + 1 }}.</td>
                                            <td>{{ $selectedrisk['code'] }}</td>
                                            <td>{{ $selectedrisk['name'] }}</td>
                                            <td>{{ ($selectedrisk['premium_rate'] * 100) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        
                            <div style="">
                                <button wire:click="assignSelectedRisks" {{ count($iselectedrisks) > 0 ? '' : 'disabled' }}  class="btn btn-outline-primary pull-right mt-2">Confirm and Assign Risks</button>
                            </div>
                                        
                         </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-12">

            </div>

            </div>

        </div>

        <div class="tab-pane fade" id="bulky" role="tabpanel" aria-labelledby="profile-top-tab">

            <div wire:ignore>
            <center>
                <div wire:loading.delay>
                    <div class="loader-box">
                        <div class="loader-7" style="width: 50px; height:50px;"></div>
                    </div>
                    <h5 class="f-w-100">Processing ...</h5>
                </div>
            </center>
            
            {{-- @if(count($selectedgroup)>0) --}}
                    <button wire:click="assignMultipleRisks" class="btn btn-outline-primary mb-4 mr-2 ml-2" style="width: 100%">Add All Associated Active Risk(s) in Selected product(s)</button>
            {{-- @endif --}}

            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="default-according style-1" id="accordionoc">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-white" data-bs-toggle="collapse" data-bs-target="#collapseicon" aria-expanded="true" aria-controls="collapse11">
                                                <i class="icofont icofont-car"></i> MOTORS
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="collapse show" id="collapseicon" aria-labelledby="collapseicon" data-bs-parent="#accordionoc" style="">
                                        <div class="card-body">
                                            @if(count($riskgroups['motors']) > 0)
                                            <table class="table table-xs">
                                                <thead>
                                                <tr>
                                                      <th scope="col"></th>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Code</th>
                                                      <th scope="col">Name</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            @foreach ($riskgroups['motors'] as $group)
                                                <tr>
                                                    <td><input type="checkbox" class="checkbox_animated" wire:model.defer="selectedgroup" value="{{ json_encode($group) }}"></td>
                                                    <td>{{ $loop->index + 1 }}.</td>
                                                    <td>{{ $group['code'] }}</td>
                                                    <td>{{ $group['name'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            </table>
                                            @else 
                                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                                <i class="icon-info-alt txt-danger"></i>
                                                    No Risk Groups Found Under this Category
                                            </div>
                                            @endif
                                            </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed text-white" data-bs-toggle="collapse" data-bs-target="#collapseicon1" aria-expanded="false">
                                                <i class="icofont icofont-fruits"></i> GOODS IN TRANSIT
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="collapse" id="collapseicon1" aria-labelledby="headingeight" data-bs-parent="#accordionoc">
                                        <div class="card-body">
                                            @if(count($riskgroups['goods']) > 0)
                                            <table class="table table-xs">
                                                <thead>
                                                <tr>
                                                      <th scope="col"></th>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Code</th>
                                                      <th scope="col">Name</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            @foreach ($riskgroups['goods'] as $group)
                                                <tr>
                                                    <td><input type="checkbox" class="checkbox_animated" wire:model.defer="selectedgroup" value="{{ json_encode($group) }}"></td>
                                                    <td>{{ $loop->index + 1 }}.</td>
                                                    <td>{{ $group['code'] }}</td>
                                                    <td>{{ $group['name'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            </table>
                                            @else 
                                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                                <i class="icon-info-alt txt-danger"></i>
                                                    No Risk Groups Found Under this Category
                                            </div>
                                            @endif
                                            </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed text-white" data-bs-toggle="collapse" data-bs-target="#collapseicon2" aria-expanded="false" aria-controls="collapseicon2">
                                                <i class="icofont icofont-engineer"></i> ENGINEERING
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="collapse" id="collapseicon2" data-bs-parent="#accordionoc">
                                        <div class="card-body">
                                            @if(count($riskgroups['engineering']) > 0)
                                            <table class="table table-xs">
                                                <thead>
                                                <tr>
                                                      <th scope="col"></th>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Code</th>
                                                      <th scope="col">Name</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            @foreach ($riskgroups['engineering'] as $group)
                                                <tr>
                                                    <td><input type="checkbox" class="checkbox_animated" wire:model.defer="selectedgroup" value="{{ json_encode($group) }}"></td>
                                                    <td>{{ $loop->index + 1 }}.</td>
                                                    <td>{{ $group['code'] }}</td>
                                                    <td>{{ $group['name'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            </table>
                                            @else 
                                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                                <i class="icon-info-alt txt-danger"></i>
                                                    No Risk Groups Found Under this Category
                                            </div>
                                            @endif
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="default-according style-1" id="accordionoc">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-white" data-bs-toggle="collapse" data-bs-target="#collapseicon00" aria-expanded="true" aria-controls="collapse11">
                                                <i class="icofont icofont-fire"></i> FIRE
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="collapse" id="collapseicon00" aria-labelledby="collapseicon" data-bs-parent="#accordionoc" style="">
                                        <div class="card-body">
                                            @if(count($riskgroups['fire']) > 0)
                                            <table class="table table-xs">
                                                <thead>
                                                <tr>
                                                      <th scope="col"></th>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Code</th>
                                                      <th scope="col">Name</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            @foreach ($riskgroups['fire'] as $group)
                                                <tr>
                                                    <td><input type="checkbox" class="checkbox_animated" wire:model.defer="selectedgroup" value="{{ json_encode($group) }}"></td>
                                                    <td>{{ $loop->index + 1 }}.</td>
                                                    <td>{{ $group['code'] }}</td>
                                                    <td>{{ $group['name'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            </table>
                                            @else 
                                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                                <i class="icon-info-alt txt-danger"></i>
                                                    No Risk Groups Found Under this Category
                                            </div>
                                            @endif
                                            </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed text-white" data-bs-toggle="collapse" data-bs-target="#collapseicon11" aria-expanded="false">
                                                <i class="icofont icofont-energy-water"></i> MARINE
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="collapse" id="collapseicon11" aria-labelledby="headingeight" data-bs-parent="#accordionoc">
                                        <div class="card-body">
                                            @if(count($riskgroups['marine']) > 0)
                                            <table class="table table-xs">
                                                <thead>
                                                <tr>
                                                      <th scope="col"></th>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Code</th>
                                                      <th scope="col">Name</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            @foreach ($riskgroups['marine'] as $group)
                                                <tr>
                                                    <td><input type="checkbox" class="checkbox_animated" wire:model.defer="selectedgroup" value="{{ json_encode($group) }}"></td>
                                                    <td>{{ $loop->index + 1 }}.</td>
                                                    <td>{{ $group['code'] }}</td>
                                                    <td>{{ $group['name'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            </table>
                                            @else 
                                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                                <i class="icon-info-alt txt-danger"></i>
                                                    No Risk Groups Found Under this Category
                                            </div>
                                            @endif
                                            </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed text-white" data-bs-toggle="collapse" data-bs-target="#collapseicon22" aria-expanded="false" aria-controls="collapseicon2">
                                                <i class="icofont icofont-building"></i> MISCLANEOUS & ACCIDENTS
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="collapse" id="collapseicon22" data-bs-parent="#accordionoc">
                                        <div class="card-body">
                                            @if(count($riskgroups['misclaneous']) > 0)
                                            <table class="table table-xs">
                                                <thead>
                                                <tr>
                                                      <th scope="col"></th>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Code</th>
                                                      <th scope="col">Name</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            @foreach ($riskgroups['misclaneous'] as $group)
                                                <tr>
                                                    <td><input type="checkbox" class="checkbox_animated" wire:model.defer="selectedgroup" value="{{ json_encode($group) }}"></td>
                                                    <td>{{ $loop->index + 1 }}.</td>
                                                    <td>{{ $group['code'] }}</td>
                                                    <td>{{ $group['name'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            </table>
                                            @else 
                                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                                <i class="icon-info-alt txt-danger"></i>
                                                    No Risk Groups Found Under this Category
                                            </div>
                                            @endif
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

      </div>

</div>

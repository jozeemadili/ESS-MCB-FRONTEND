<div class="col-lg-12">
        <div class="card card-absolute">
            <div class="card-header bg-default">
                <h5 class="text-dark">Actions </h5>
            </div>
            <div class="card-body">

            @if($vehicle->TRANSACTION_STATUS === 'PENDING')
                @if(Auth::user()->role == 'Qality Assurance Officer')
                <div class="row">  
                        <div class="col-lg-6">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal"> Qality Assurance Officer Approve Request <i class="icofont icofont-tick-boxed"></i></button>
                        </div>
                        <div class="col-lg-6">
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#newModalReject"> Qality Assurance Officer Reject Request <i class="icofont icofont-trash"></i></button>
                        </div>
                </div>
                @else
                <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        Awaiting Quality Assurance Action
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                </div>
                @endif
            @endif

            @if($vehicle->TRANSACTION_STATUS === 'APPROVED')
                <div class="alert alert-secondary outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        Awaiting Approval From His Office
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
            @endif       

            @if($vehicle->TRANSACTION_STATUS === 'CONFERMED' )
                @if(Auth::user()->role == 'Credit Admin' && !isset($approval_datils))
                    <div class="row">  
                            <div class="col-lg-6">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">Credit Admin Approve Request <i class="icofont icofont-tick-boxed"></i></button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Admin Reject Request <i class="icofont icofont-trash"></i></button>
                            </div>
                    </div>
                    @elseif($approval_datils && $approval_datils->status == 'INITIATED')

                    @elseif($approval_datils && $approval_datils->status == 'DISBURSED')
                    
                    
                    @else
                    
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Admin Action
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                @endif 
            @if(isset($approval_datils))
                @if(Auth::user()->role == 'Credit Manager' && $approval_datils->status=='INITIATED')
                    <div class="row">  
                            <div class="col-lg-6">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal"> Credit Manager Approve Request <i class="icofont icofont-tick-boxed"></i></button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Manager Reject Request <i class="icofont icofont-trash"></i></button>
                            </div>
                    </div>
                    @elseif($approval_datils->status=='DISBURSED')
                    <div class="card-body">
                    <div class="row">
                            <div class="col-lg-12">
                                <ul class="list-group"> 
                                    <li class="list-group-item d-flex justify-content-between align-items-center">Initated At<span class="badge badge-primary rounded-pill">{{ $approval_datils->initiator_date->format('d M Y H:i:s') }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center"> Disbursed At<span class="badge badge-primary rounded-pill">{{ $approval_datils->checker_date->format('d M Y H:i:s')  }}</span></li> 
                                    <li class="list-group-item d-flex justify-content-between align-items-center">STATUS<span class="badge badge-primary rounded-pill">{{ $approval_datils->status  }}</span></li> 
                            </ul>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Manager Action
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                @endif

            @endif
        @endif
                
            </div>
        </div>
    </div>
<div class="card card-absolute">
    <div class="card-header bg-default">
        <h5 class="text-dark">Previously submitted Claims </h5>
    </div>
    <div class="card-body">  
    @if(count($quotations)>0)
            @foreach($quotations as $quotation)
            @if(count($quotation->policies)>0)
                @foreach($quotation->policies as $policy)
                @if(count($policy->claim_notifications)>0)
                <h6 class="f-w-100"> <small>Policy # {{  $policy->reference_number }}</small></h6><hr />
                <div class="table-responsive mb-0">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Reference #</th>
                            <th scope="col">Claim Ref #</th>
                            <th scope="col">Report Date</th>
                            <th scope="col">Loss Date</th>
                            <th scope="col">Loss Nature</th>
                            <th scope="col">Loss Type</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($policy->claim_notifications as $claim_notification)
                    <tr>
                        <th scope="row">{{$loop->index + 1}}.</th>
                        <td><a href='{{ Route('claim-profile', ['id' => $claim_notification->id]) }}'>{{ $claim_notification->reference_number }}</a></td>
                        <td><a href='{{ Route('claim-profile', ['id' => $claim_notification->id]) }}'>{{ $claim_notification->claim_reference_number }}</a></td>
                        <td>{{$claim_notification->report_date->format('d/m/Y')}}</td>
                        <td>{{$claim_notification->loss_date->format('d/m/Y')}}</td>
                        <td>{{$claim_notification->loss_nature->title}}</td>
                        <td>{{$claim_notification->loss_type->title}}</td>
                        <td>{{$claim_notification->created_at->format('d/m/Y')}}</td>
                        <td>{{$claim_notification->status}}</td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
                 </div><br />
                @endif
                @endforeach
                @endif
            @endforeach
            @else 
            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i>
                    No Records Found yet
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
               </div>
            @endif
        </div>
</div>
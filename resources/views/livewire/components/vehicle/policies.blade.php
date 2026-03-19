<div class="card card-absolute">
    <div class="card-header bg-default">
        <h5 class="text-dark">Previously submitted Policies </h5>
    </div>
    <div class="card-body">
        
    @if(count($quotations)>0)
            @foreach($quotations as $quotation)
            @if(count($quotation->policies)>0)
            <h6 class="f-w-100"> <small>Quotation # {{  $quotation->reference_number }}</small></h6><hr />
            <div class="table-responsive mb-0">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Reference #</th>
                        <th scope="col">Special Condtions</th>
                        <th scope="col">Operative Clause</th>
                        <th scope="col">Exclusions</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($quotation->policies as $policy)
                    <tr>
                        <th scope="row">{{$loop->index + 1}}.</th>
                        <td><a href='#'>{{ $policy->reference_number }}</a></td>
                        <td>{{$policy->special_conditions}}</td>
                        <td>{{$policy->policy_operative_clause}}</td>
                        <td>{{$policy->exclusions}}</td>
                        <td>{{$policy->created_at->format('d/m/Y')}}</td>
                        <td>{{$policy->status}}</td>
                    </tr>
                @endforeach
                    </tbody>
                </table>
            </div><br />
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
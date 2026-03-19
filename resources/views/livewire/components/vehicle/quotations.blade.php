<div class="card card-absolute">
    <div class="card-header bg-default">
        <h5 class="text-dark">Previously submitted Quotations ({{ count($quotations) }}) </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            @if(count($quotations)>0)
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Reference #</th>
                        <th scope="col">Company</th>
                        <th scope="col">Start</th>
                        <th scope="col">End</th>
                        <th scope="col">Sum Insured</th>
                        <th scope="col">Premium</th>
                        <th scope="col">Sticker</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotations as $quotation)
                    <tr>
                        <th scope="row">{{$loop->index + 1}}.</th>
                        <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{ $quotation->reference_number }}</a></td>
                        <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{strtoupper(explode(' ',$quotation->company->name)[0])}}</a></td>
                        <td>{{$quotation->start_date->format('d/m/Y')}}</td>
                        <td>{{$quotation->end_date->format('d/m/Y')}}</td>
                        <td>{{number_format($quotation->sum_insured, 2, '.',',')}}</td>
                        <td>{{number_format($quotation->total_premium_including_tax, 2, '.',',')}}</td>
                        <td><a href='{{route('quotation-profile', ['id' => $quotation->id])}}'>{{$quotation->sticker_number}}</a></td>
                        <td>{{$quotation->status}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br />
        
            @else 
            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i>
                    No Records Found yet
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
               </div>
            @endif
        </div>
    </div>
</div>
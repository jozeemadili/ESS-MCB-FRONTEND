<div class="table-responsive">
    <table class="table table-xs">
        <thead>
            <tr>
                <th></th>
                <th>S/N</th>
                <th>Type</th>
                <th>Name</th>
                <th>Code</th>
                <th>TIN #</th>
                <th>Licence #</th>
                <th>Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($intermediaries as $intermediary)
                <tr>
                    <td><input type="checkbox" class="checkbox_animated" wire:model="selectedIntermediaries" value="{{ $intermediary->id }}"></td>
                    <td>{{ $loop->index + 1 }}.</td>
                    <td>{{ $intermediary->company->category }}</td>
                    <td><a href='{{route('intermediary-profile', ['id' => $intermediary->id])}}'>{{ strtoupper($intermediary->company->name) }}</a></td>
                    <td>{{ $intermediary->company->code }}</td>
                    <td>{{ $intermediary->company->tin }}</td>
                    <td>{{ $intermediary->company->license_number }}</td>
                    <td>{{ $intermediary->company->phone_number }}</td>
                    <td>{{ $intermediary->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;">
        <button  {{ count($selectedIntermediaries) > 0 ? '' : 'disabled' }}  data-bs-toggle="modal" data-bs-target="#productsAssignation" class="btn btn-outline-primary pull-right">Assign Risks</button>
    </div>

<!-- PRODUCTS ASSIGNATION MODAL START -->
 <div class="modal fade" id="productsAssignation" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Assign Risks ({{ count($selectedIntermediaries) }}) Selected Intermediary(s) </h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <div wire:ignore style="padding:7px;">
                    @livewire('components.intermediaries.products-assignation', ['intermediaries' => $selectedIntermediaries ])
                </div>
            </div>
        </div>
    </div>
    </div>
 <!-- END -->

</div>

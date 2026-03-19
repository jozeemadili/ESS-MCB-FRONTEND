<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th></th>
                <th>Type</th>
                <th>Name</th>
                <th>Code</th>
                <th>TIN #</th>
                <th>Licence #</th>
                <th>Registration #</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($intermediaries as $intermediary)
                <tr>
                    <td><input type="checkbox" class="checkbox_animated" wire:model="selectedIntermediaries" value="{{ $intermediary->id }}"></td>
                    <td>{{ $intermediary->category }}</td>
                    <td>{{ strtoupper($intermediary->name) }}</td>
                    <td>{{ $intermediary->code }}</td>
                    <td>{{ $intermediary->tin }}</td>
                    <td>{{ $intermediary->license_number }}</td>
                    <td>{{ $intermediary->registration_number }}</td>
                    <td>{{ $intermediary->email }}</td>
                    <td>{{ $intermediary->phone_number }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;">
        <button wire:click="submitSelected" {{ count($selectedIntermediaries) > 0 ? '' : 'disabled' }} class="btn btn-outline-primary pull-right">Confirm and Register</button>
        <button class="btn btn-outline-secondary pull-right" type="button" data-bs-dismiss="modal" style="margin-right: 5px;">Cancel</button>
    </div>

</div>

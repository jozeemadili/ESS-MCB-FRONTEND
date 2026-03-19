<div>
    <div class="form-group">
        <label class="col-form-label" >Plan</label>
        <select class="form-control" required value="{{ old('plan_id') }}" wire:model="plan_id" name="plan_id" id="plan_id">
        <option value="">--- Choose Plan ---</option>  
        @foreach($plans as $plan)
            <option value="{{$plan->id}}">{{strtoupper($plan->product->name)}} - {{$plan->cover_class}} - {{$plan->cover_type}}  - {{$plan->cover_sub_type}}</option>
        @endforeach
        </select>
    </div>
    <div class="form-group" style="display: {{ $show_risk }}">
        <label class="col-form-label" >Risk </label>
        <select class="form-control js-example-basic-singlex" required value="{{ old('risk_id') }}" name="risk_id" id="risk_id">
        <option value="">--- Choose Risk ---</option>  
        @foreach($this->getRisks() as $risk)
            <option value="{{$risk->id}}">{{strtoupper($risk->code)}} - {{$risk->name}}</option>
        @endforeach
        </select>
    </div>
</div>


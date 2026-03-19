<div>
   <div class="row">
        <div class="col-lg-6">
            <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">New</div>
                    <div class="row">

                        <div class="form-group">
                            <label>Amount</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">TZS</span>
                                <input type="number" wire:model="amount" step="0.01" class="form-control" placeholder="Amount to Top up or Deduct ..." aria-label="Amount" aria-describedby="basic-addon1">
                            </div>
                        <p>
                            <h6 class="f-w-300"> 
                                {!!  floatval($amount)>0 ? "<font color='green'>Adding TZS ".number_format(abs(floatval($amount)), 2, '.', ',')."</font>" : "<font color='red'>Deducting TZS ".number_format(abs(floatval($amount)), 2, '.', ',')."</font>" !!}
                            </h6>
                        </p>
                        <p>
                            <div class="blockquote-footer"> {{ $amount_in_words }} </div>
                        </p>

                        <p>
                            <h6 class="f-w-300"> 
                                {!!  floatval($new_balance)>0 ? "<font color='green'>New Balance : TZS ".number_format(abs(floatval($new_balance)), 2, '.', ',')."</font>" : "<font color='red'>New Balance : TZS ".number_format(abs(floatval($new_balance)), 2, '.', ',')."</font>" !!}
                            </h6>
                        </p>
                        <p>
                            <div class="blockquote-footer"> {{ $new_balance_in_words }} </div>
                        </p>

                        </div>

                        <div class="form-group">
                            <button wire:click="submitFloat" class="btn btn-outline-primary" {{ (!$amount || $amount == null || $amount == 0) ? 'disabled' :'' }}>Confirm and Submit</button>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-dark">History</div>
                    <div class="row">
                        <div class="table-responsive" wire:poll.visible="refreshFloats">
                            @if(count($floats)>0)
                            <table class="table table-xs">
                              <thead>
                              <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Prev. Balance</th>
                                    <th scope="col">New Balance</th>
                                    <th scope="col">Prev. Amount</th>
                                    <th scope="col">New Amount</th>
                              </tr>
                              </thead>
                              <tbody>
                                    @foreach($floats as $float)
                                      <tr>
                                          <th scope="row">{{$loop->index + 1}}.</th>
                                          <td>{{number_format($float->previous_balance, 2, '.', ',')}}</td>
                                          <td>{{number_format($float->current_balance, 2, '.', ',')}}</td>
                                          <td>{{number_format($float->previous_amount, 2, '.', ',')}}</td>
                                          <td>{{number_format($float->current_amount, 2, '.', ',')}}</td>
                                      </tr>
                                    @endforeach
                              </tbody>
                            </table>
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
            </div>
        </div>

   </div>
</div>


@push('scripts')
@endpush
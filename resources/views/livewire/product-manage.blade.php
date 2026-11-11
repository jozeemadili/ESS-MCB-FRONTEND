<div>
  @if(isset($products))
    @php
      $st = $products->STATUS ?: 'Not Published';
      $stClass = $st == 'Published' ? 'ok' : ($st == 'Decommissioned' ? 'bad' : 'wait');
    @endphp
    <div class="pd-card">
      <div class="pd-hero">
        <small>Product code {{ $products->CODE }}</small>
        <h4>{{ strtoupper($products->NAME) }}</h4>
        <span class="pd-pill {{ $stClass }}">{{ $st }}</span>
        @if($products->IS_EXECUTIVE)<span class="pd-pill" style="background:rgba(255,255,255,.2);color:#fff;">Executive</span>@endif
      </div>
      <div class="pd-kv">
        <div><span>Minimum amount</span><b>{{ number_format($products['MINIMUM_AMOUNT'], 2) }} {{ $products->CURRENCY }}</b></div>
        <div><span>Maximum amount</span><b>{{ number_format($products['MAXIMUM_AMOUNT'], 2) }} {{ $products->CURRENCY }}</b></div>
        <div><span>Minimum tenure</span><b>{{ $products->MINIMUM_TENURE }} months</b></div>
        <div><span>Maximum tenure</span><b>{{ $products->MAXIMUM_TENURE }} months</b></div>
        <div><span>Interest rate</span><b>{{ $products->INTEREST_RATE }}%</b></div>
        <div><span>Processing fee rate</span><b>{{ $products->PROCESSING_FEE_RATE }}%</b></div>
        <div><span>Insurance rate</span><b>{{ $products->INSURANCE_RATE }}%</b></div>
        <div><span>Repayment</span><b>{{ $products->REPAYMENT_TYPE }}</b></div>
        <div class="wide"><span>Registered</span><b>{{ optional($products->DATE)->format('d M Y, H:i') }}</b>
          @if($products->DATE)<small class="pd-mut"> · {{ $products->DATE->diffForHumans() }}</small>@endif</div>
        @if($products->DESCRIPTION)
          <div class="wide"><span>Description</span><b style="font-weight:500">{{ $products->DESCRIPTION }}</b></div>
        @endif
        @if($st == 'Decommissioned' && $products->DECOMMISSION_REASON)
          <div class="wide"><span>Decommission reason</span><b style="font-weight:500">{{ $products->DECOMMISSION_REASON }}</b></div>
        @endif
      </div>

      @if($st == 'Not Published' || $st == 'Decommissioned')
        <div class="pd-note"><i class="fa fa-info-circle"></i> Conditions can only be added or deleted before the product is published.</div>
      @endif

      <div class="pd-actions" wire:loading.remove>
        @if($st == 'Not Published' || $st == 'Decommissioned')
          <button wire:click="publishProduct" class="btn pd-btn publish" type="button"><i class="fa fa-cloud-upload"></i> Publish to ESS</button>
        @elseif($st == 'Published')
          <button wire:click="decamisionProduct" class="btn pd-btn danger" type="button"><i class="fa fa-ban"></i> Decommission</button>
        @endif
        <button class="btn pd-btn outline" type="button" data-bs-toggle="modal" data-bs-target="#newModalEdit"><i class="fa fa-pencil"></i> Edit product</button>
      </div>
      <div wire:loading.delay class="w-100">
        <div class="d-flex align-items-center gap-2 p-3 pd-mut"><span class="spinner-border spinner-border-sm"></span> Sending to ESS…</div>
      </div>
    </div>

    @if (session()->has('message'))
      <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <!-- Edit Product Modal -->
    <div class="modal fade pd" id="newModalEdit" tabindex="-1" aria-hidden="true" wire:ignore.self>
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content pd-modal">
          <form method="POST" action="/v1/products/edit" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
            @csrf
            <input type="hidden" name="productId" value="{{ $productId }}">
            <div class="modal-header">
              <div><h5 class="modal-title">Edit product</h5><small class="pd-mut">Code {{ $products->CODE }} cannot be changed.</small></div>
              <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="pd-sec">Product</div>
              <div class="row g-3">
                <div class="col-md-8"><label>Product name</label><input class="form-control" type="text" required name="name" value="{{ old('name', $products->NAME) }}"></div>
                <div class="col-md-4"><label>Product code</label><input class="form-control" type="text" value="{{ $products->CODE }}" disabled></div>
              </div>
              <div class="pd-sec">Limits</div>
              <div class="row g-3">
                <div class="col-md-3"><label>Minimum tenure (months)</label><input class="form-control" type="number" min="1" required name="minimumTenure" value="{{ old('minimumTenure', $products->MINIMUM_TENURE) }}"></div>
                <div class="col-md-3"><label>Maximum tenure (months)</label><input class="form-control" type="number" min="1" required name="maximumTenure" value="{{ old('maximumTenure', $products->MAXIMUM_TENURE) }}"></div>
                <div class="col-md-3"><label>Minimum amount</label><input class="form-control" type="number" step="any" required name="minimumAmount" value="{{ old('minimumAmount', $products->MINIMUM_AMOUNT) }}"></div>
                <div class="col-md-3"><label>Maximum amount</label><input class="form-control" type="number" step="any" required name="maximumAmount" value="{{ old('maximumAmount', $products->MAXIMUM_AMOUNT) }}"></div>
              </div>
              <div class="pd-sec">Pricing</div>
              <div class="row g-3">
                <div class="col-md-6"><label>Interest rate (% a year)</label><input class="form-control" type="number" step="any" required name="interestRate" value="{{ old('interestRate', $products->INTEREST_RATE) }}"></div>
                <div class="col-md-6"><label>Processing fee rate (%)</label><input class="form-control" type="number" step="any" required name="processingFeeRate" value="{{ old('processingFeeRate', $products->PROCESSING_FEE_RATE) }}"></div>
                <div class="col-12"><label>Description</label><textarea class="form-control" rows="3" name="description">{{ old('description', $products->DESCRIPTION) }}</textarea></div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
              <button class="btn pd-btn" type="submit">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>

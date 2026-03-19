@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/sweetalert2.css')}}">
<style>
.swal-wide
{
    width:1250px !important;
}
</style>
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot

    @slot('breadcrumb_action_buttons')
        @if(Auth::user()->role == 'System Admin' || Auth::user()->role == 'Insurer Admin')
         <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newIntermediaryModal">New <i class="icofont icofont-plus-circle"></i></button></li>
        @endif
    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">

                  <div class="card-body">
                      <p>
                      <div class="table-responsive">
                        @livewire('components.intermediaries.home')
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newIntermediaryModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog  modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Choose Intermediary(s) to Work with</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                @livewire('components.intermediaries.registration')
            </div>
        </div>
    </div>
    </div>
 <!-- NEW MODAL END -->

  @push('scripts')
  <script src="{{asset('assets/js/sweet-alert/sweetalert.min.js')}}"></script>
  <script>
  
    window.addEventListener('swal:modal', event => { 
        swal({
          title: event.detail.message,
          text: event.detail.text,
          icon: event.detail.type,
          buttons:false,
          customClass:'swal-wide'
        });
    });
      
    window.addEventListener('swal:confirm', event => { 
        swal({
          title: event.detail.message,
          text: event.detail.text,
          icon: event.detail.type,
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            window.livewire.emit('remove');
          }
        });
    });
     </script>
  @endpush
@endsection
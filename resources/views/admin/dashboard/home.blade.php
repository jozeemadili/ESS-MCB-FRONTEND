@extends('layouts.admin.master')
@section('title', 'Dashboard')
@push('css')
@endpush
    @section('content')
      <!-- Container-fluid starts-->
      <div class="container-fluid dashboard-default-sec">

      <div class="row">
      
      </div>

      <div class="row">
      <div class="col-lg-7">
          @livewire('components.reports.salescharts')
        </div>
        <div class="col-lg-5">
            @livewire('components.reports.quotationstatus')
        </div>
      </div>
      

      </div>
      <!-- Container-fluid Ends-->
@endsection

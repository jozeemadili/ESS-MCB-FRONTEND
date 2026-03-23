<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>
    <?php $__env->startSection('content'); ?>
      <!-- Container-fluid starts-->
      <div class="container-fluid dashboard-default-sec">

      <div class="row">
      
      </div>

      <div class="row">
      <div class="col-lg-7">
          <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.salescharts')->html();
} elseif ($_instance->childHasBeenRendered('c9NcRkS')) {
    $componentId = $_instance->getRenderedChildComponentId('c9NcRkS');
    $componentTag = $_instance->getRenderedChildComponentTagName('c9NcRkS');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('c9NcRkS');
} else {
    $response = \Livewire\Livewire::mount('components.reports.salescharts');
    $html = $response->html();
    $_instance->logRenderedChild('c9NcRkS', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        </div>
        <div class="col-lg-5">
            <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.quotationstatus')->html();
} elseif ($_instance->childHasBeenRendered('ShZA6E2')) {
    $componentId = $_instance->getRenderedChildComponentId('ShZA6E2');
    $componentTag = $_instance->getRenderedChildComponentTagName('ShZA6E2');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('ShZA6E2');
} else {
    $response = \Livewire\Livewire::mount('components.reports.quotationstatus');
    $html = $response->html();
    $_instance->logRenderedChild('ShZA6E2', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        </div>
      </div>
      

      </div>
      <!-- Container-fluid Ends-->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/UPDATED PROJECTS/EXTERNAL/MCB ESS/FRONT END/ESS-MCB-FRONTEND/resources/views/admin/dashboard/home.blade.php ENDPATH**/ ?>
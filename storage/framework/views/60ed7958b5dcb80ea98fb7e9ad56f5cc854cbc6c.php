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
} elseif ($_instance->childHasBeenRendered('YTMYuEH')) {
    $componentId = $_instance->getRenderedChildComponentId('YTMYuEH');
    $componentTag = $_instance->getRenderedChildComponentTagName('YTMYuEH');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('YTMYuEH');
} else {
    $response = \Livewire\Livewire::mount('components.reports.salescharts');
    $html = $response->html();
    $_instance->logRenderedChild('YTMYuEH', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        </div>
        <div class="col-lg-5">
            <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.quotationstatus')->html();
} elseif ($_instance->childHasBeenRendered('6V25AII')) {
    $componentId = $_instance->getRenderedChildComponentId('6V25AII');
    $componentTag = $_instance->getRenderedChildComponentTagName('6V25AII');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('6V25AII');
} else {
    $response = \Livewire\Livewire::mount('components.reports.quotationstatus');
    $html = $response->html();
    $_instance->logRenderedChild('6V25AII', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        </div>
      </div>
      

      </div>
      <!-- Container-fluid Ends-->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/UPDATED PROJECTS/EXTERNAL/MCB ESS/FRONT END/ESS-MCB-FRONTEND/resources/views/admin/dashboard/home.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
      <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3>
    <?php $__env->endSlot(); ?>

    
    
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        <?php echo e($error); ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  
                  <?php if($message = Session::get('success')): ?>
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        <?php echo $message; ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					<?php endif; ?>
      <div class="row">
        <?php if(isset($vehicle)): ?>
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-controls="vehicles" aria-selected="true"><i class="icofont icofont-user-alt-3"></i>Applicant Details</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" id="profile-top-tab" data-bs-toggle="tab" href="#quotations" role="tab" aria-controls="quotations" aria-selected="false"><i class="icofont icofont-paper"></i>Loans</a></li> -->
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#policies" role="tab" aria-controls="policies" aria-selected="false"><i class="icofont icofont-shield"></i>Repayments</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" id="claims-top-tab" data-bs-toggle="tab" href="#claimstab" role="tab" aria-controls="claims" aria-selected="false"><i class="icofont icofont-whisle"></i>Claims</a></li>
                        <li class="nav-item"><a class="nav-link" id="photos-top-tab" data-bs-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="false"><i class="icofont icofont-image"></i>Photos</a></li> -->
                    </ul>
      
                      <div class="tab-content" id="top-tabContent">

                        <div class="tab-pane fade active show" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab">
                          
                          <p><?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.vehicle.details', ['vehicle' => $vehicle, 'approval_datils' => $approval_datils])->html();
} elseif ($_instance->childHasBeenRendered('XCfNCI7')) {
    $componentId = $_instance->getRenderedChildComponentId('XCfNCI7');
    $componentTag = $_instance->getRenderedChildComponentTagName('XCfNCI7');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('XCfNCI7');
} else {
    $response = \Livewire\Livewire::mount('components.vehicle.details', ['vehicle' => $vehicle, 'approval_datils' => $approval_datils]);
    $html = $response->html();
    $_instance->logRenderedChild('XCfNCI7', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?></p>
                        </div>

                       

                      </div>

                  </div>
              </div>
          </div>
          <?php else: ?>
          <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                       User Does not exit
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
          <?php endif; ?>
      </div>
  </div>




  <?php $__env->startPush('scripts'); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/UPDATED PROJECTS/EXTERNAL/MCB ESS/FRONT END/ESS-MCB-FRONTEND/resources/views/admin/properties/staff-profile.blade.php ENDPATH**/ ?>
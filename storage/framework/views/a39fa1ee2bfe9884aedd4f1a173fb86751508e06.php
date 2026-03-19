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

    <?php $__env->slot('breadcrumb_action_buttons'); ?>

    <?php $__env->endSlot(); ?>
    
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>

  <div class="container-fluid">
  <div class="card-body">
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
	    <div class="edit-profile">
	        <div class="row">
	            <div class="col-xl-4">

              <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('product-manage',['products' => $products, 'productId' => $id])->html();
} elseif ($_instance->childHasBeenRendered('ExhsBKc')) {
    $componentId = $_instance->getRenderedChildComponentId('ExhsBKc');
    $componentTag = $_instance->getRenderedChildComponentTagName('ExhsBKc');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('ExhsBKc');
} else {
    $response = \Livewire\Livewire::mount('product-manage',['products' => $products, 'productId' => $id]);
    $html = $response->html();
    $_instance->logRenderedChild('ExhsBKc', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

              </div>
              
              
	            <div class="col-xl-8">
                <div class="card">
                  <div class="card-body">

                    <div class="row">
                      <div class="profile-title">
                          <div class="media">
                              <div class="media-body">
                                  <h3 class="mb-1 f-20 txt-primary">Product Conditions</h3>
                                  <p class="f-12">List Of Conditions</p>
                                  <?php if($products->STATUS == 'Not Published' || $products->STATUS == 'Decommissioned'): ?>
                                  <div class="pull-right"><a href='' data-bs-toggle="modal" data-bs-target="#newModal">New +</a></div>
                                  <?php endif; ?>
                              </div>
                          </div>
                    </div>
                  </div>
                  
                  <div class="table-responsive">
                      <?php if(count($responseData)>0): ?>
                      <table class="table table-xs">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">C NO</th>  
                          <th scope="col">Description</th>            
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
							        <tbody>
                        <?php $__currentLoopData = $responseData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                          <th scope="row"><?php echo e($loop->index + 1); ?>.</th>	
                          <td><a href='#'><?php echo e($product['CONDITION_NUMBER']); ?></a></td>	
                          <td><a href='#'><?php echo e($product['DESCRIPTION']); ?></a></td>                
                          <td>
                                  <?php if($products->STATUS == 'Not Published' || $products->STATUS == 'Decommissioned'): ?>
                                      <div class="pull-right"> <a href='<?php echo Route('product-condition-update', ['id' => $product->ID, 'status' => 'Inactive']); ?>' class='btn btn-outline-danger btn-xs'>Delete <i class="icofont icofont-ui-delete"></i></a></div>
                                        <?php else: ?>  
                                        <div class="pull-right"> <a href='' class='btn btn-outline-primary btn-xs'> Published <i class="icofont icofont-ui-rating"></i></a></div>
                                  <?php endif; ?>
                                  
                          </td>                                                
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							        </tbody>
						          </table>
                      <?php else: ?>
                          <p>No Product Conditions.</p>
                      <?php endif; ?>

    
                        
					        </div>
                  </div>
                </div>
	            </div>
	        </div>

      
</div>
	</div>
  </div>

  

 						<!-- NEW MODAL START -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Condition Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="/v1/products/condition/add">
                    <?php echo csrf_field(); ?> 
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="form-group">
                                <label class="col-form-label" hidden>id</label>
                                <input class="form-control" hidden type="number" value="<?php echo e($id); ?>"  required  name="id">
                            </div>    
                            <div class="form-group">
                                    <label class="col-form-label" >Description</label>
                                    <textarea class="form-control" type="text"  required  name="description"></textarea>
                              </div>
                            <!-- <div class="form-group">
                                <label class="col-form-label" >Description </label>
                                <input class="form-control" type="text"  required  name="description" >
                            </div> -->
                        </div>
                        
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW MODAL END -->



  <?php $__env->startPush('scripts'); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/products/risks-registration.blade.php ENDPATH**/ ?>
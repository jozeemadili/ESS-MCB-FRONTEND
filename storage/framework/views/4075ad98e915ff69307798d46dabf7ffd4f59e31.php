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
    <?php if(Auth::user()->role == 'ADMIN' && Auth::user()->company->id == 1): ?>
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
    <?php endif; ?>
    <?php $__env->endSlot(); ?>
    
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">

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

                      <p>
                      <div class="table-responsive">
                  
                        <?php if(count($products)>0): ?>
                        
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Code</th>
                                    <th scope="col">Name</th>
									<!-- <th scope="col">Description</th> -->
                                    <th scope="col">Minimum Tenure (Months)</th>
                                    <th scope="col">Maximum Tenure (Months)</th>
                                    <th scope="col">processing Fee Rate (%)</th>
                                    <th scope="col">insurance Rate (%)</th>
                                    <th scope="col">minimum Amount</th>
                                    <th scope="col">maximum Amount</th>
                                    <th scope="col">Repayment Type</th>
                                    <th scope="col">Interest Rate (%)</th>
                                    <th scope="col">Reg Date</th>
								</tr>
            
							</thead>
							<tbody>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
                                <!-- `ID`, ``, `CURRENCY`, `CODE`, ``, ``, `DECOMMISSION_REASON`, `DESCRIPTION`, `INSURANCE_RATE`, `IS_EXECUTIVE`, ``, `MAXIMUM_TENURE`, ``, `MINIMUM_TENURE`, `NAME`, ``, ``,  -->
									<th scope="row"><?php echo e($loop->index + 1); ?>.</th>
									<td><?php echo e($product['CODE']); ?></td>
									<td><a href="get/condition/<?php echo e($product['ID']); ?>"><?php echo e(strtoupper($product['NAME'])); ?></a></td>
                                    <td><?php echo e($product['MAXIMUM_TENURE']); ?></td>
                                    <td><?php echo e($product['MINIMUM_TENURE']); ?></td>
                                    <td><?php echo e($product['PROCESSING_FEE_RATE']); ?></td>
                                    <td><?php echo e($product['INSURANCE_RATE']); ?></td>
                                    <td><?php echo e(number_format($product['MINIMUM_AMOUNT'], 2, '.', ',')); ?> <?php echo e($product['CURRENCY']); ?></td>
                                    <td><?php echo e(number_format($product['MAXIMUM_AMOUNT'], 2, '.', ',')); ?> <?php echo e($product['CURRENCY']); ?></td>
                                    <td><?php echo e($product['REPAYMENT_TYPE']); ?></td>
                                    <td><?php echo e($product['INTEREST_RATE']); ?></td>
                                    <td><?php echo e(Carbon\Carbon::parse($product['DATE'])->diffForHumans()); ?></td>
								</tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
                        <br />
                        

                        <?php else: ?> 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        <?php endif; ?>
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <!-- <div class="modal-dialog" role="document"> -->
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Product Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="/v1/products/add">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="col-form-label" >Product Name</label>
                                <input class="form-control" type="text"  required  name="name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Product Code</label>
                                <input class="form-control" type="text"  required  name="code" >
                            </div>
                            <!-- <div class="form-group">
                                <label class="col-form-label" >Description</label>
                                <input class="form-control" type="text"  required  name="description" >
                            </div> -->
                            <div class="form-group">
                                <label class="col-form-label">Is Executive ? </label><br>
                                <input type="radio" checked class="radio_animated" value="1" name="isExecutive"> Yes
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="0" name="isExecutive"> No
                            </div>
                        </div>
                        <div class="col-lg-3">
                
                            <div class="form-group">
                                <label class="col-form-label" >Minimum Tenure (Months)</label>
                                <input class="form-control" type="number"  required  name="minimumTenure">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Maximum Tenure (Months)</label>
                                <input class="form-control" type="number"  required  name="maximumTenure" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Interest Rate (%)</label>
                                <input class="form-control" type="number" step="any" required  name="InterestRate" >
                            </div>
                        </div>
                        <div class="col-lg-3">
       
                            <div class="form-group">
                                <label class="col-form-label" >Processing Fee Rate (%)</label>
                                <input class="form-control" type="number"  required step="any"  name="processingFeeRate">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Insurance Rate (%)</label>
                                <input class="form-control" type="number" step="any" required  name="insuranceRate" >
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Minimum Amount</label>
                                <input class="form-control" type="number"  required  name="minimumAmount" >
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="col-form-label" >Maximum Amount</label>
                                <input class="form-control" type="number" required  name="maximumAmount">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Currency</label>
                                <select class="form-select" required  name="ccy">
                                <option value="">--- Choose Currency ---</option>    
                                <option>TZS</option>
                                    <option>USD</option>
                                    <option>GBP</option>
								</select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Repayment Type</label>
                                <select class="form-select" required  name="repaymentType">
                                <option value="">--- Repayment Type  ---</option>    
                                <option>Salary</option>
								</select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                       
                        </div>
                        <div class="col-lg-6">
                        <div class="form-group">
                                <div class="form-group">
                                    <label class="col-form-label" >Description</label>
                                    <textarea class="form-control" type="text" value="<?php echo e(old('street')); ?>" required  name="description"></textarea>
                                </div>
                            </div>
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
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/products/products-registration.blade.php ENDPATH**/ ?>
<div class="card">
	                    <div class="card-body">
	                      <?php if(isset($products)): ?>
	                            <div class="row mb-2">
	                                <div class="profile-title">
	                                    <div class="media">
	                                        <!-- <img class="img-70 rounded-circle" alt="" src="<?php echo e(asset('assets/images/dashboard/1.png')); ?>"> -->
	                                        <div class="media-body">
	                                            <h3 class="mb-1 f-20 txt-primary"><?php echo e($products->NAME); ?> </h3>
	                                            <p class="f-12">Product Details With Product Code <b><?php echo e($products->CODE); ?> </b></p>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                            
                                <div class="table-responsive">
                    <table class="table table-sm">
                     
                    <!-- SELECT `ID`, `INTEREST_RATE`, `CURRENCY`, `CODE`, `DATE`, `DECOMMISSION_DATE`, `DECOMMISSION_REASON`, `DESCRIPTION`, `INSURANCE_RATE`, `IS_EXECUTIVE`, `MAXIMUM_AMOUNT`, `MAXIMUM_TENURE`, `MINIMUM_AMOUNT`, `MINIMUM_TENURE`, `NAME`, `PROCESSING_FEE_RATE`, `REPAYMENT_TYPE`, `STATUS` FROM `PRODUCT` WHERE 1 -->
                      <tr>
                        <th>Maximum Amount</th>
                        <td><?php echo e(number_format($products['MAXIMUM_AMOUNT'], 2, '.', ',')); ?>  <?php echo e($products->CURRENCY); ?></td>
                      </tr>
                      <tr>
                        <th>Minimum Amount</th>
                        <td><?php echo e(number_format($products['MINIMUM_AMOUNT'], 2, '.', ',')); ?>  <?php echo e($products->CURRENCY); ?></td>
                      </tr>
                      <tr>
                        <th> Maximum Tenure</th>
                        <td><?php echo e($products->MAXIMUM_TENURE); ?> Months</i></td>
                      </tr>
                      <tr>
                        <th>Minimum Tenure</th>
                        <td><?php echo e($products->MINIMUM_TENURE); ?> Months</i></td>
                      </tr>
                      <tr>
                        <th>Interest Rate</th>
                        <td><?php echo e($products->INTEREST_RATE); ?> % </i></td>
                      </tr>
                      <tr>
                        <th>Insurance Rate</th>
                        <td><?php echo e($products->INSURANCE_RATE); ?> %</i></td>
                      </tr>
                      <tr>
                        <th>Proccesing Fee Rate</th>
                        <td><?php echo e($products->PROCESSING_FEE_RATE); ?> % </i></td>
                      </tr>
                      <tr>
                        <th>Repayment</th>
                        <td><?php echo e($products->REPAYMENT_TYPE); ?></i></td>
                      </tr>
                      <!-- `, `DECOMMISSION_DATE`, `DECOMMISSION_REASON`, `DESCRIPTION` -->
                      <tr>
                        <th> Registration Date</th>
                        <td><?php echo e($products->DATE->format('d M Y, H:i:s')); ?></td>
                      </tr>
                      <tr>
                        <th>Status</th>
                        <td><?php echo e($products->STATUS); ?></td>
                      </tr>
                      <tr>
                        <th>Action</th>
                        <td>
                            <?php if($products->STATUS == 'Not Published' || $products->STATUS == 'Decommissioned'): ?>
                            <button wire:click="publishProduct" class="btn btn-outline-primary btn-xs pull-left"  type="button" wire:loading.remove> Publish <i class="icofont icofont-ui-rate-add"></i></button>
                            <?php elseif($products->STATUS == 'Published'): ?>
                            <button wire:click="decamisionProduct" class="btn btn-outline-danger btn-xs pull-left"  type="button" wire:loading.remove> Decamission <i class="icofont icofont-ui-rate-remove"></i></button>     
                            <?php else: ?>  
                              <?php endif; ?>
                              <button class="btn btn-outline-success btn-xs pull-right" data-bs-toggle="modal" data-bs-target="#newModalEdit"> Edit Product <i class="icofont icofont-ui-edit"></i></button>
                              
    
                        </td>
                      </tr>
                    </table>
                    
                    <div wire:loading.delay>
                        <div class="loader-box">
                            <div class="loader-7" style="width: 50px; height:50px;"></div>
                            <br/>
                                <h5 class="f-w-100">Processing ...</h5>
                            </div>
                        </div>
                    </div>
                      
                      <?php endif; ?>
                
                        

                              
              </div>
              </div>
              <div>
    <!-- Success Message -->
    <?php if(session()->has('message')): ?>
        <div class="alert alert-success">
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="newModalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Edit Product</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form method="POST" action="/v1/products/edit">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="productId" value="<?php echo e($productId); ?>">

    <div class="form-group">
        <label>Product Name</label>
        <input class="form-control" type="text" name="name" value="<?php echo e(old('name', $products->NAME)); ?>">
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Product Code</label>
        <input class="form-control" type="text" name="code" value="<?php echo e(old('code', $products->CODE)); ?>" disabled>
        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Minimum Tenure (Months)</label>
        <input class="form-control" type="number" name="minimumTenure" value="<?php echo e(old('minimumTenure', $products->MINIMUM_TENURE)); ?>">
        <?php $__errorArgs = ['minimumTenure'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Maximum Tenure (Months)</label>
        <input class="form-control" type="number" name="maximumTenure" value="<?php echo e(old('maximumTenure', $products->MAXIMUM_TENURE)); ?>">
        <?php $__errorArgs = ['maximumTenure'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Interest Rate (%)</label>
        <input class="form-control" type="number" name="interestRate" step="any" value="<?php echo e(old('interestRate', $products->INTEREST_RATE)); ?>">
        <?php $__errorArgs = ['interestRate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Processing Fee Rate (%)</label>
        <input class="form-control" type="number" name="processingFeeRate" step="any" value="<?php echo e(old('processingFeeRate', $products->PROCESSING_FEE_RATE)); ?>">
        <?php $__errorArgs = ['processingFeeRate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Minimum Amount</label>
        <input class="form-control" type="number" name="minimumAmount" value="<?php echo e(old('minimumAmount', $products->MINIMUM_AMOUNT)); ?>">
        <?php $__errorArgs = ['minimumAmount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Maximum Amount</label>
        <input class="form-control" type="number" name="maximumAmount" value="<?php echo e(old('maximumAmount', $products->MAXIMUM_AMOUNT)); ?>">
        <?php $__errorArgs = ['maximumAmount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea class="form-control" name="description"><?php echo e(old('description', $products->description)); ?></textarea>
        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <button class="btn btn-primary" type="submit">Update Product</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/laravel/resources/views/livewire/product-manage.blade.php ENDPATH**/ ?>
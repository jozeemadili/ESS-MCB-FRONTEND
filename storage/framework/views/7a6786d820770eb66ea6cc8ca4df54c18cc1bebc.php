<div class="row">

    <div class="col-lg-2">
    <a href='<?php echo Route('loans-applications'); ?>'> 
        <div class="card income-card card-primary">
          <br />
          <div class="card-body text-center">
            <div class="round-box">
                <i class="icofont icofont-users-social" style="font-size: 40px;"></i>
            </div>
            <h5><?php echo e($summary['allApplication']); ?></h5>
            <p>Total Loans </p>
          </div><br />
        </div>
        </a>
      </div>
           <div class="col-lg-2">
           <a href='<?php echo Route('loans-pendingall'); ?>'> 
        <div class="card income-card card-secondary">
          <br />
          <div class="card-body text-center">
            <div class="round-box">
                <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
            </div>
            <h5><?php echo e($summary['Pending']); ?></h5>
            <p>Pending </p>
            
          </div><br />
        </div>
        </a>
        
      </div>

      <div class="col-lg-2">
      <a href='<?php echo Route('loans-approved'); ?>'> 
        <div class="card income-card card-primary">
          <br />
          <div class="card-body text-center">
            <div class="round-box">
                <i class="icofont icofont-tick-boxed" style="font-size: 40px;"></i>
            </div>
            
            <h5><?php echo e($summary['Approved']); ?></h5>
            <p>Approved </p>
            
          </div><br />
        </div>
        </a>
      </div>

      <div class="col-lg-2">
      <a href='<?php echo Route('loans-disbursed'); ?>'> 
        <div class="card income-card card-secondary">
          <br />
          <div class="card-body text-center">
            <div class="round-box">
                <i class="icofont icofont-money" style="font-size: 40px;"></i>
            </div>
            <h5><?php echo e($summary['Disbursed']); ?></h5>
            <p>Disbursed </p>
          </div><br />
        </div>
        </a>
      </div>
      <div class="col-lg-2">
      <a href='<?php echo Route('loans-cancelled'); ?>'> 
        <div class="card income-card card-secondary">
          <br />
          <div class="card-body text-center">
            <div class="round-box">
                <i class="icofont icofont-stop" style="font-size: 40px;"></i>
            </div>
            <h5><?php echo e($summary['cannceled']); ?></h5>
            <p>Cancelled </p>
          </div><br />
        </div>
        </a>
      </div>
      <div class="col-lg-2">
      <a href='<?php echo Route('loans-rejected'); ?>'> 
        <div class="card income-card card-secondary">
          <br />
          <div class="card-body text-center">
            <div class="round-box">
                <i class="icofont icofont-ui-love-remove" style="font-size: 40px;"></i>
            </div>
            <h5><?php echo e($summary['Rejected']); ?></h5>
            <p>Rejected </p>
          </div><br />
        </div>
        </a>
      </div>

</div>
<?php /**PATH /var/www/html/laravel/resources/views/livewire/components/reports/summary.blade.php ENDPATH**/ ?>
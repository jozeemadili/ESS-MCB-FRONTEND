<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One FSP_MONTHLY_DEDUCTIONS message from ESS (BatchNumber of TotalBatch for a payroll date), saved by the ESS gateway. */
class PayrollBatch extends Model
{
    protected $table = 'BATCH_APPLICATION_PAYMENT';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    public function deductions()
    {
        return $this->hasMany(PayrollDeduction::class, 'BATCH_APPLICATION_PAYMENT_ID', 'ID');
    }
}

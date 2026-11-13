<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Monthly payroll deduction from ESS (FSP_MONTHLY_DEDUCTIONS, API doc 2.6.3), saved by the ESS gateway: one row per
 * loan and payroll date (CHECK_DATE) with the amount deducted and the balance left. FIRST_DATE holds the first name.
 */
class PayrollDeduction extends Model
{
    protected $table = 'LOAN_APPLICATION_PAYMENT';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $casts = [
        'BALANCE_AMOUNT'      => 'float',
        'DEDUCTION_AMOUNT'    => 'float',
        'HAS_STOP_PAY'        => 'bool',
        'LOAN_APPLICATION_ID' => 'int',
    ];

    public function loan()
    {
        return $this->belongsTo(LOANAPPLICATION::class, 'LOAN_APPLICATION_ID', 'ID');
    }

    public function batch()
    {
        return $this->belongsTo(PayrollBatch::class, 'BATCH_APPLICATION_PAYMENT_ID', 'ID');
    }

    /** Deductions visible to the user: Branch Operations see their own branch only. */
    public static function visibleTo($user)
    {
        $q = self::query();
        if ($user && $user->role == 'Branch Operations') {
            $q->whereHas('loan', fn ($l) => $l->where('INIT_BRANCH', $user->branch_id));
        }
        return $q;
    }

    public static function tableReady(): bool
    {
        static $ready = null;
        if ($ready === null) {
            try {
                $ready = Schema::hasTable('LOAN_APPLICATION_PAYMENT') && Schema::hasTable('BATCH_APPLICATION_PAYMENT');
            } catch (\Throwable $e) {
                $ready = false;
            }
        }
        return $ready;
    }
}

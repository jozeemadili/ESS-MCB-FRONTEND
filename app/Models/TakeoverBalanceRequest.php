<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Payoff quote MCB gave ESS when another bank (FSP2) asked to take over an MCB loan
 * (TAKEOVER_PAY_OFF_BALANCE_REQUEST, API doc 2.5.1/2.5.2). Saved by the ESS gateway.
 * STATUS: QUOTED, then PAID when FSP2's payment notification arrives.
 */
class TakeoverBalanceRequest extends Model
{
    protected $table = 'TAKEOVER_BALANCE_REQUEST';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $casts = ['TOTAL_PAYOFF_AMOUNT' => 'float', 'PRINCIPAL_BALANCE' => 'float', 'LOAN_APPLICATION_ID' => 'int'];
    protected $dates = ['FINAL_PAYMENT_DATE', 'REQUEST_DATE'];

    public function loan()
    {
        return $this->belongsTo(LOANAPPLICATION::class, 'LOAN_APPLICATION_ID', 'ID');
    }

    /** The table is created by the updated ESS gateway; until then the old screen is shown. */
    public static function tableReady(): bool
    {
        static $ready = null;
        if ($ready === null) {
            try { $ready = Schema::hasTable('TAKEOVER_BALANCE_REQUEST'); } catch (\Throwable $e) { $ready = false; }
        }
        return $ready;
    }
}

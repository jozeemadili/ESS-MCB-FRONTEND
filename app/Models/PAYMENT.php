<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PAYMENT
 * 
 * @property int $ID
 * @property string|null $FSP1_BANK_ACCOUNT
 * @property string|null $FSP1_BANK_ACCOUNT_NAME
 * @property string|null $FSP1_CODE
 * @property Carbon|null $FSP1_FINAL_PAYMENT_DATE
 * @property string|null $FSP1_LOAN_NUMBER
 * @property string|null $FSP1_PAYMENT_REFERENCE_NUMBER
 * @property string|null $CURRENCY
 * @property Carbon|null $CREATED_AT
 * @property Carbon|null $DATE_PROCESSED
 * @property string|null $FUNDING
 * @property string|null $LOAN_ID
 * @property string|null $STATUS
 * @property float|null $TAKE_OVER_AMOUNT
 * @property Carbon|null $TAKE_OVER_DATE
 * @property int|null $TAKE_OVER_ID
 *
 * @package App\Models
 */
class PAYMENT extends Model
{
	protected $table = 'PAYMENT';
	protected $primaryKey = 'ID';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID' => 'int',
		'TAKE_OVER_AMOUNT' => 'float',
		'TAKE_OVER_ID' => 'int'
	];

	protected $dates = [
		'FSP1_FINAL_PAYMENT_DATE',
		'CREATED_AT',
		'DATE_PROCESSED',
		'TAKE_OVER_DATE'
	];

	protected $fillable = [
		'FSP1_BANK_ACCOUNT',
		'FSP1_BANK_ACCOUNT_NAME',
		'FSP1_CODE',
		'FSP1_FINAL_PAYMENT_DATE',
		'FSP1_LOAN_NUMBER',
		'FSP1_PAYMENT_REFERENCE_NUMBER',
		'CURRENCY',
		'CREATED_AT',
		'DATE_PROCESSED',
		'FUNDING',
		'LOAN_ID',
		'STATUS',
		'TAKE_OVER_AMOUNT',
		'TAKE_OVER_DATE',
		'TAKE_OVER_ID'
	];

	/** Takeover where MCB is FSP2: the loan booked from LOAN_TAKEOVER_OFFER_REQUEST. */
	public function loan()
	{
		return $this->belongsTo(LOANAPPLICATION::class, 'LOAN_APPLICATION_ID', 'ID');
	}

	/** The FSP1 / transfer columns are added by the updated ESS gateway. */
	public static function takeoverReady(): bool
	{
		static $ready = null;
		if ($ready === null) {
			try { $ready = \Illuminate\Support\Facades\Schema::hasColumn('PAYMENT', 'CBS_REFERENCE') && \Illuminate\Support\Facades\Schema::hasColumn('PAYMENT', 'FSP1_LOAN_NUMBER'); } catch (\Throwable $e) { $ready = false; }
		}
		return $ready;
	}

	/** Takeover payment for a loan (by LOAN_ID), newest first. */
	public static function forLoan($loan)
	{
		if (!$loan || !self::takeoverReady()) {
			return null;
		}
		return self::where('LOAN_ID', $loan->LOAN_ID)->orderByDesc('ID')->first();
	}

	/** Draft MT103 for the transfer to FSP1 (Credit Operation replaces it with the real advice if different). */
	public function draftMt103($loan, string $reference = 'REFERENCE'): string
	{
		$amount = number_format((float) $this->TAKE_OVER_AMOUNT, 2, ',', '');
		return '{1:F01MWCOTZTZAXXX}{2:I103' . str_pad(substr((string) $this->FSP1_SWIFT_CODE, 0, 8), 8, 'X') . 'XXXXN}{4:'
			. "\n:20:" . $reference
			. "\n:23B:CRED"
			. "\n:32A:" . now('Africa/Dar_es_Salaam')->format('ymd') . 'TZS' . $amount
			. "\n:50K:/" . config('ess.settlement_account', '001000347312') . "\nMWALIMU COMMERCIAL BANK PLC"
			. "\n:59:/" . $this->FSP1_BANK_ACCOUNT . "\n" . $this->FSP1_BANK_ACCOUNT_NAME
			. "\n:70:" . $this->FSP1_PAYMENT_REFERENCE_NUMBER . ' TAKEOVER LOAN ' . $this->FSP1_LOAN_NUMBER
			. "\n:71A:OUR\n-}";
	}
}

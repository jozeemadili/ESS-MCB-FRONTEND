<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class INCOMMINGPAYMENT
 * 
 * @property int $ID
 * @property string|null $CBS_DESCRIPTION
 * @property string|null $CBS_REFERENCE
 * @property string|null $FSP_REFERENCE_NUMBER
 * @property string|null $LOAN_NUMBER
 * @property string|null $PAYMENT_ADVICE
 * @property string|null $PAYMENT_ADVICE_ATTACHMENT
 * @property string|null $PAYMENT_DATE
 * @property string|null $PAYMENT_REFERENCE_NUMBER
 * @property string|null $REASON
 * @property Carbon|null $REQUEST_DATE
 * @property string|null $STATUS
 * @property string|null $STATUS_DESCRIPTION
 * @property string|null $TOTAL_PAYOFF_AMOUNT
 *
 * @package App\Models
 */
class INCOMMINGPAYMENT extends Model
{
	protected $table = 'INCOMMING_PAYMENT';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID' => 'int'
	];

	protected $dates = [
		'REQUEST_DATE',
		'ACK_DATE'
	];

	protected $fillable = [
		'ID',
		'CBS_DESCRIPTION',
		'CBS_REFERENCE',
		'FSP_REFERENCE_NUMBER',
		'LOAN_NUMBER',
		'PAYMENT_ADVICE',
		'PAYMENT_ADVICE_ATTACHMENT',
		'PAYMENT_DATE',
		'PAYMENT_REFERENCE_NUMBER',
		'REASON',
		'REQUEST_DATE',
		'STATUS',
		'STATUS_DESCRIPTION',
		'TOTAL_PAYOFF_AMOUNT',
		'APPLICATION_NUMBER',
		'MESSAGE_ID',
		'LOAN_APPLICATION_ID',
		'BALANCE_REQUEST_ID',
		'ACK_STATUS',
		'ACK_REMARKS',
		'ACK_BY',
		'ACK_DATE'
	];

	/** Payments still to be confirmed to ESS (PAYMENT_ACKNOWLEDGMENT_NOTIFICATION; ESS expects it within two hours). */
	public const PENDING_STATUSES = ['RECEIVED', 'ACK_FAILED'];

	/** Payments received by the ESS gateway (they carry the ESS MsgId) that are not yet confirmed; older rows are legacy. */
	public static function pending($query)
	{
		return $query->whereIn('STATUS', self::PENDING_STATUSES)->whereNotNull('MESSAGE_ID');
	}

	public function isPending(): bool
	{
		return in_array($this->STATUS, self::PENDING_STATUSES) && !empty($this->MESSAGE_ID);
	}

	public function loan()
	{
		return $this->belongsTo(LOANAPPLICATION::class, 'LOAN_APPLICATION_ID', 'ID');
	}

	public function quote()
	{
		return $this->belongsTo(TakeoverBalanceRequest::class, 'BALANCE_REQUEST_ID', 'ID');
	}

	/** The confirmation columns are added by the updated ESS gateway. */
	public static function ackReady(): bool
	{
		static $ready = null;
		if ($ready === null) {
			try { $ready = \Illuminate\Support\Facades\Schema::hasColumn('INCOMMING_PAYMENT', 'ACK_STATUS'); } catch (\Throwable $e) { $ready = false; }
		}
		return $ready;
	}

	public static function pendingCount(): int
	{
		static $count = null;
		if ($count === null) {
			$count = self::ackReady() ? self::pending(self::query())->count() : 0;
		}
		return $count;
	}
}

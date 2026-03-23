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
		'REQUEST_DATE'
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
		'TOTAL_PAYOFF_AMOUNT'
	];
}

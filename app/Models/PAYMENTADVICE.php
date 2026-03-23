<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PAYMENTADVICE
 * 
 * @property int $ID
 * @property string|null $FSP_REFERENCE_NUMBER
 * @property string|null $LOAN_NUMBER
 * @property string|null $PAYMENT_ADVICE
 * @property string|null $PAYMENT_ADVICE_ATTACHMENT
 * @property string|null $PAYMENT_DATE
 * @property string|null $PAYMENT_REFERENCE_NUMBER
 * @property string|null $REASON
 * @property string|null $TOTAL_PAYOFF_AMOUNT
 *
 * @package App\Models
 */
class PAYMENTADVICE extends Model
{
	protected $table = 'PAYMENT_ADVICE';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $fillable = [
		'FSP_REFERENCE_NUMBER',
		'LOAN_NUMBER',
		'PAYMENT_ADVICE',
		'PAYMENT_ADVICE_ATTACHMENT',
		'PAYMENT_DATE',
		'PAYMENT_REFERENCE_NUMBER',
		'REASON',
		'TOTAL_PAYOFF_AMOUNT'
	];
}

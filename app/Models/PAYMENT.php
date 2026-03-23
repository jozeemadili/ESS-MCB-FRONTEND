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
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PRODUCT
 * 
 * @property int $ID
 * @property int|null $INTEREST_RATE
 * @property string|null $CURRENCY
 * @property string|null $CODE
 * @property Carbon|null $DATE
 * @property Carbon|null $DECOMMISSION_DATE
 * @property string|null $DECOMMISSION_REASON
 * @property string|null $DESCRIPTION
 * @property int|null $INSURANCE_RATE
 * @property bool|null $IS_EXECUTIVE
 * @property float|null $MAXIMUM_AMOUNT
 * @property int|null $MAXIMUM_TENURE
 * @property float|null $MINIMUM_AMOUNT
 * @property int|null $MINIMUM_TENURE
 * @property string|null $NAME
 * @property int|null $PROCESSING_FEE_RATE
 * @property string|null $REPAYMENT_TYPE
 * @property string|null $STATUS
 *
 * @package App\Models
 */
// class PRODUCT extends Model
class Product extends Model
{
	protected $table = 'PRODUCT';
	protected $primaryKey = 'ID';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID' => 'int',
		'INTEREST_RATE' => 'float',
		'INSURANCE_RATE' => 'float',
		'IS_EXECUTIVE' => 'bool',
		'MAXIMUM_AMOUNT' => 'float',
		'MAXIMUM_TENURE' => 'int',
		'MINIMUM_AMOUNT' => 'float',
		'MINIMUM_TENURE' => 'int',
		'PROCESSING_FEE_RATE' => 'float'
	];

	protected $dates = [
		'DATE',
		'DECOMMISSION_DATE'
	];

	protected $fillable = [
		'INTEREST_RATE',
		'CURRENCY',
		'CODE',
		'DATE',
		'DECOMMISSION_DATE',
		'DECOMMISSION_REASON',
		'DESCRIPTION',
		'INSURANCE_RATE',
		'IS_EXECUTIVE',
		'MAXIMUM_AMOUNT',
		'MAXIMUM_TENURE',
		'MINIMUM_AMOUNT',
		'MINIMUM_TENURE',
		'NAME',
		'PROCESSING_FEE_RATE',
		'REPAYMENT_TYPE',
		'STATUS'
	];
}

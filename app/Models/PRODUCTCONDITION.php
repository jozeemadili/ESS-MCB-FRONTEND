<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PRODUCTCONDITION
 * 
 * @property int $ID
 * @property string|null $CONDITION_NUMBER
 * @property Carbon|null $DATE
 * @property string|null $DESCRIPTION
 * @property Carbon|null $EFFECTIVE_DATE
 * @property int|null $PRODUCT_ID
 * @property string|null $STATUS
 *
 * @package App\Models
 */

class PRODUCTCONDITION extends Model
{
	protected $table = 'PRODUCT_CONDITION';
	protected $primaryKey = 'ID';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID' => 'int',
		'PRODUCT_ID' => 'int'
	];

	protected $dates = [
		'DATE',
		'EFFECTIVE_DATE'
	];

	protected $fillable = [
		'CONDITION_NUMBER',
		'DATE',
		'DESCRIPTION',
		'EFFECTIVE_DATE',
		'PRODUCT_ID'
	];
}

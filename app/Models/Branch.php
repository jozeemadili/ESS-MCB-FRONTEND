<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BRANCH
 * 
 * @property int $ID
 * @property string $BRANCH_CODE
 * @property string $BRANCH_NAME
 * @property string|null $DISTRICT_CODE
 * @property string|null $STATUS
 *
 * @package App\Models
 */
class BRANCH extends Model
{
	protected $table = 'BRANCH';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID' => 'int'
	];

	protected $fillable = [
		'ID',
		'BRANCH_CODE',
		'BRANCH_NAME',
		'DISTRICT_CODE',
		'STATUS'
	];
}

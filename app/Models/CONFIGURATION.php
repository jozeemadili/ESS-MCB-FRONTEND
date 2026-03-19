<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CONFIGURATION
 * 
 * @property int $ID
 * @property string|null $BASE_URL
 * @property string|null $DISBURSEMENT_ACCOUNT
 * @property string|null $HMCIS_URL
 *
 * @package App\Models
 */
class CONFIGURATION extends Model
{
	protected $table = 'CONFIGURATION';
	protected $primaryKey = 'ID';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID' => 'int'
	];

	protected $fillable = [
		'BASE_URL',
		'DISBURSEMENT_ACCOUNT',
		'HMCIS_URL'
	];
}

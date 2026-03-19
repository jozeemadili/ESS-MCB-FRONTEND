<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DisburmentApprovalStage
 * 
 * @property int $id
 * @property string $loan_id
 * @property string $status
 * @property int|null $initiator_id
 * @property Carbon|null $initiator_date
 * @property int|null $checker_id
 * @property Carbon|null $checker_date
 *
 * @package App\Models
 */
class DisburmentApprovalStage extends Model
{
	protected $table = 'disburment_approval_stages';
	public $timestamps = false;

	protected $casts = [
		'initiator_id' => 'int',
		'checker_id' => 'int'
	];

	protected $dates = [
		'initiator_date',
		'checker_date'
	];

	protected $fillable = [
		'loan_id',
		'status',
		'initiator_id',
		'initiator_date',
		'checker_id',
		'checker_date'
	];
}

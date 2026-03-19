<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LOANAPPLICATION
 * 
 * @property int $ID
 * @property string|null $APPLICATION_NUMBER
 * @property string|null $APPROVAL_DESCRIPTION
 * @property string|null $APPROVED_BY
 * @property Carbon|null $APPROVED_DATE
 * @property string|null $BANK_ACCOUNT_NUMBER
 * @property float|null $BASIC_SALARY
 * @property string|null $BEN_BRANCH
 * @property string|null $BEN_CURRENCY
 * @property string|null $BRANCH_NAME
 * @property string|null $CBS_RESPONSE_CODE
 * @property string|null $CBS_RESPONSE_DESCRIPTION
 * @property int|null $CHECK_NUMBER
 * @property Carbon|null $CONFIRMATION_DATE
 * @property Carbon|null $CONTRACT_END_DATE
 * @property Carbon|null $CONTRACT_START_DATE
 * @property Carbon|null $APPLICATION_DATE
 * @property string|null $DESIGNATION_CODE
 * @property string|null $DESIGNATION_NAME
 * @property float|null $DESIRED_DEDUCTION_AMOUNT
 * @property string|null $DISBURSEMENT_RECEIPT
 * @property string|null $EMAIL_ADDRESS
 * @property Carbon|null $EMPLOYMENT_DATE
 * @property string|null $FIRST_NAME
 * @property string|null $INIT_BRANCH
 * @property string|null $INIT_CURRENCY
 * @property string|null $LOAN_REQUEST_INITIATOR
 * @property float|null $INSURANCE
 * @property float|null $INTEREST_CODE
 * @property string|null $LAST_NAME
 * @property string|null $LOAN_REQUEST_CHECKER
 * @property Carbon|null $LOAN_DISBURSED_DATE
 * @property string|null $LOAN_ID
 * @property string|null $LOAN_PURPOSE
 * @property string|null $LOAN_STATUS
 * @property string|null $MARITAL_STATUS
 * @property string|null $MIDDLE_NAME
 * @property string|null $MSISDN
 * @property string|null $NEAREST_BRANCH_NAME
 * @property float|null $NET_SALARY
 * @property string|null $NATIONAL_ID
 * @property float|null $ONE_THIRD_AMOUNT
 * @property float|null $OTHER_CHARGES
 * @property string|null $PHYSICAL_ADDRESS
 * @property float|null $PROCESSING_FEE
 * @property string|null $PRODUCT_CODE
 * @property string|null $REASON
 * @property string|null $REFERENCE_NUMBER
 * @property float|null $REQUESTED_AMOUNT
 * @property int|null $RETIREMENT_DATE
 * @property string|null $SEX
 * @property int|null $TENURE
 * @property string|null $TERMS_OF_EMPLOYMENT
 * @property float|null $TOTAL_AMOUNT
 * @property float|null $TOTAL_AMOUNT_TO_PAY
 * @property float|null $TOTAL_EMPLOYMENT_DEDUCTION
 * @property string|null $TRANSACTION_STATUS
 * @property string|null $VOTE_CODE
 * @property string|null $VOTE_NAME
 * @property string|null $LOAN_REFERENCE
 * @property string|null $LOAN_TYPE
 * @property float|null $OUT_STANDING_BALANCE
 * @property string|null $CUSTOMER_ID
 * @property Carbon|null $INSTALMENT_START_DATE
 * @property float|null $PRODUCT_INTEREST_RATE
 * @property float|null $PRODUCT_PROCESSING_FEE
 * @property Carbon|null $LAST_PAYMENT_DATE
 * @property Carbon|null $MATURITY_DATE
 * @property float|null $MONTHLY_DEDUCTION
 * @property string|null $ALTENATIVE_ACCOUNT
 * @property string|null $funding
 * @property int|null $generalLedger
 * @property string|null $LINAC_ACCOUNT_NUMBER
 * @property string|null $LINAC_BRANCH
 * @property Carbon|null $LOAN_LIQUIDATION_DATE
 * @property string|null $nearestBranchCode
 * @property string|null $ORIGINAL_LOAN_ID
 * @property string|null $SETTLEMENT_AMOUNT
 * @property string|null $SWIFT_CODE
 * @property int|null $CHECK_DIGIT
 * @property string|null $LIQUIDATION_MAKER
 * @property string|null $LIQUIDATION_CHECKER
 * @property string|null $LIQUIDATION_MAKER_REMARKS
 * @property string|null $LIQUIDATION_CHECKER_REMARKS
 * @property string|null $LIQUIDATION_STATUS
 * @package App\Models
 */
class LOANAPPLICATION extends Model
{
	protected $table = 'LOAN_APPLICATION';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'BASIC_SALARY' => 'float',
		'CHECK_NUMBER' => 'int',
		'DESIRED_DEDUCTION_AMOUNT' => 'float',
		'INSURANCE' => 'float',
		'INTEREST_CODE' => 'float',
		'NET_SALARY' => 'float',
		'ONE_THIRD_AMOUNT' => 'float',
		'OTHER_CHARGES' => 'float',
		'PROCESSING_FEE' => 'float',
		'REQUESTED_AMOUNT' => 'float',
		'RETIREMENT_DATE' => 'int',
		'TENURE' => 'int',
		'TOTAL_AMOUNT' => 'float',
		'TOTAL_AMOUNT_TO_PAY' => 'float',
		'TOTAL_EMPLOYMENT_DEDUCTION' => 'float',
		'OUT_STANDING_BALANCE' => 'float',
		'PRODUCT_INTEREST_RATE' => 'float',
		'PRODUCT_PROCESSING_FEE' => 'float',
		'MONTHLY_DEDUCTION' => 'float',
		'generalLedger' => 'int',
		'CHECK_DIGIT' => 'int'
	];

	protected $dates = [
		'APPROVED_DATE',
		'CONFIRMATION_DATE',
		'CONTRACT_END_DATE',
		'CONTRACT_START_DATE',
		'APPLICATION_DATE',
		'EMPLOYMENT_DATE',
		'LOAN_DISBURSED_DATE',
		'INSTALMENT_START_DATE',
		'LAST_PAYMENT_DATE',
		'MATURITY_DATE',
		'LOAN_LIQUIDATION_DATE'
	];

	protected $fillable = [
		'APPLICATION_NUMBER',
		'APPROVAL_DESCRIPTION',
		'APPROVED_BY',
		'APPROVED_DATE',
		'BANK_ACCOUNT_NUMBER',
		'BASIC_SALARY',
		'BEN_BRANCH',
		'BEN_CURRENCY',
		'BRANCH_NAME',
		'CBS_RESPONSE_CODE',
		'CBS_RESPONSE_DESCRIPTION',
		'CHECK_NUMBER',
		'CONFIRMATION_DATE',
		'CONTRACT_END_DATE',
		'CONTRACT_START_DATE',
		'APPLICATION_DATE',
		'DESIGNATION_CODE',
		'DESIGNATION_NAME',
		'DESIRED_DEDUCTION_AMOUNT',
		'DISBURSEMENT_RECEIPT',
		'EMAIL_ADDRESS',
		'EMPLOYMENT_DATE',
		'FIRST_NAME',
		'INIT_BRANCH',
		'INIT_CURRENCY',
		'LOAN_REQUEST_INITIATOR',
		'INSURANCE',
		'INTEREST_CODE',
		'LAST_NAME',
		'LOAN_REQUEST_CHECKER',
		'LOAN_DISBURSED_DATE',
		'LOAN_ID',
		'LOAN_PURPOSE',
		'LOAN_STATUS',
		'MARITAL_STATUS',
		'MIDDLE_NAME',
		'MSISDN',
		'NEAREST_BRANCH_NAME',
		'NET_SALARY',
		'NATIONAL_ID',
		'ONE_THIRD_AMOUNT',
		'OTHER_CHARGES',
		'PHYSICAL_ADDRESS',
		'PROCESSING_FEE',
		'PRODUCT_CODE',
		'REASON',
		'REFERENCE_NUMBER',
		'REQUESTED_AMOUNT',
		'RETIREMENT_DATE',
		'SEX',
		'TENURE',
		'TERMS_OF_EMPLOYMENT',
		'TOTAL_AMOUNT',
		'TOTAL_AMOUNT_TO_PAY',
		'TOTAL_EMPLOYMENT_DEDUCTION',
		'TRANSACTION_STATUS',
		'VOTE_CODE',
		'VOTE_NAME',
		'LOAN_REFERENCE',
		'LOAN_TYPE',
		'OUT_STANDING_BALANCE',
		'CUSTOMER_ID',
		'INSTALMENT_START_DATE',
		'PRODUCT_INTEREST_RATE',
		'PRODUCT_PROCESSING_FEE',
		'LAST_PAYMENT_DATE',
		'MATURITY_DATE',
		'MONTHLY_DEDUCTION',
		'ALTENATIVE_ACCOUNT',
		'funding',
		'generalLedger',
		'LINAC_ACCOUNT_NUMBER',
		'LINAC_BRANCH',
		'LOAN_LIQUIDATION_DATE',
		'nearestBranchCode',
		'ORIGINAL_LOAN_ID',
		'SETTLEMENT_AMOUNT',
		'SWIFT_CODE',
		'CHECK_DIGIT',
		'LIQUIDATION_MAKER',
		'LIQUIDATION_CHECKER',
		'LIQUIDATION_MAKER_REMARKS',
		'LIQUIDATION_CHECKER_REMARKS',
		'LIQUIDATION_STATUS'
	];
}

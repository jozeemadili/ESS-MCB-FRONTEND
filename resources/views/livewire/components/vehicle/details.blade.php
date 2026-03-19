<div class="rowx">
    <div class="col-lg-12">
    <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">Identification Details</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">

                        <li class="list-group-item d-flex justify-content-between align-items-center">ID<span class="badge badge-primary rounded-pill">{{ $vehicle->ID }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Application NO<span class="badge badge-primary rounded-pill">{{ $vehicle->APPLICATION_NUMBER }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Customer ID<span class="badge badge-primary rounded-pill">{{ $vehicle->CUSTOMER_ID }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Vote Code<span class="badge badge-primary rounded-pill">{{ $vehicle->VOTE_CODE }}</span></li>
 
                    </ul>
                    </div>
                    
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Loan ID<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_ID }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Reference NO<span class="badge badge-primary rounded-pill">{{ $vehicle->REFERENCE_NUMBER }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Check NO<span class="badge badge-primary rounded-pill">{{ $vehicle->CHECK_NUMBER }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Vote Name<span class="badge badge-primary rounded-pill">{{ $vehicle->VOTE_NAME }}</span></li>
                 
                        </ul>
                    </div>
                    </div>
                </div>
     </div>
     <!-- end ribon -->
     <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">Applicant Information</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">First Name.<span class="badge badge-primary rounded-pill">{{ $vehicle->FIRST_NAME }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Middle  Name.<span class="badge badge-primary rounded-pill">{{ $vehicle->MIDDLE_NAME }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Last Name<span class="badge badge-primary rounded-pill">{{  $vehicle->LAST_NAME }}</span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Email<span class="badge badge-primary rounded-pill">{{ $vehicle->EMAIL_ADDRESS}}</span></li>           
                    </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Phone<span class="badge badge-primary rounded-pill">{{ $vehicle->MSISDN }}</span></l>
                            <li class="list-group-item d-flex justify-content-between align-items-center">National ID<span class="badge badge-primary rounded-pill">{{ $vehicle->NATIONAL_ID }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">physical Address<span class="badge badge-primary rounded-pill">{{ $vehicle->PHYSICAL_ADDRESS }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Gender<span class="badge badge-primary rounded-pill">{{ $vehicle->SEX }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Marital Status<span class="badge badge-primary rounded-pill">{{ $vehicle->MARITAL_STATUS }}</span></li>
                         </ul>
                    </div>
                    </div>
                </div>
     </div>
     <!-- end ribon -->
     <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">Employment Details</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Employment Date<span class="badge badge-primary rounded-pill">{{ $vehicle->EMPLOYMENT_DATE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Months to Retire<span class="badge badge-primary rounded-pill">{{ $vehicle->RETIREMENT_DATE }} (Months)</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Terms Of Employment<span class="badge badge-primary rounded-pill">{{ $vehicle->TERMS_OF_EMPLOYMENT }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Designation Code<span class="badge badge-primary rounded-pill">{{ $vehicle->DESIGNATION_CODE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Designation Name<span class="badge badge-primary rounded-pill">{{ $vehicle->DESIGNATION_NAME }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Confirmation Date<span class="badge badge-primary rounded-pill">{{ $vehicle->CONFIRMATION_DATE}}</span></l>
                    

                          </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Basic Salary<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->BASIC_SALARY, 2,'.',',') }} TZS</span></l>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Net Salary<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->NET_SALARY, 2,'.',',') }} TZS</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">One Third Salary<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->ONE_THIRD_AMOUNT, 2,'.',',') }} TZS</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Total Employement Deduction<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->TOTAL_EMPLOYMENT_DEDUCTION, 2,'.',',') }} TZS</span></li>
                            @if(isset($vehicle->CONTRACT_START_DATE))
                            <li class="list-group-item d-flex justify-content-between align-items-center">Contract Start Date<span class="badge badge-primary rounded-pill">{{ $vehicle->CONTRACT_START_DATE->format('d M Y') }}</span></li>
                            @else
                            <li class="list-group-item d-flex justify-content-between align-items-center">Contract Start Date<span class="badge badge-primary rounded-pill"></span></li>
                            @endif
                            @if(isset($vehicle->CONTRACT_END_DATE))
                            <li class="list-group-item d-flex justify-content-between align-items-center">CONTRACT_END_DATE<span class="badge badge-primary rounded-pill">{{ $vehicle->CONTRACT_END_DATE->format('d M Y') }}</span></li>
                            @else
                            <li class="list-group-item d-flex justify-content-between align-items-center">Contract End Date<span class="badge badge-primary rounded-pill"></span></li>
                            @endif
                        </ul>
                    </div>
                    </div>
                </div>
     </div>
     <!-- end ribon -->
     <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">Loan Details</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Loan Type<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_TYPE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Loan Status<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_STATUS }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Loan Purpose<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_PURPOSE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Requested Amount<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->REQUESTED_AMOUNT, 2,'.',',') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Loan Reference<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_REFERENCE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Loan Tenure<span class="badge badge-primary rounded-pill">{{ $vehicle->TENURE }} (Months)</span></li>

                          </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Total Amount<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->TOTAL_AMOUNT, 2,'.',',') }} TZS</span></l>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Total Amount To Pay<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->TOTAL_AMOUNT_TO_PAY, 2,'.',',') }} TZS</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Out Standing Balance<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->OUT_STANDING_BALANCE, 2,'.',',') }} TZS</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Desired Deduction Amount<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->DESIRED_DEDUCTION_AMOUNT, 2,'.',',') }} TZS</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Application Date<span class="badge badge-primary rounded-pill">{{ $vehicle->APPLICATION_DATE->format('d M Y') }}</span></li>
                            @if(isset($vehicle->INSTALMENT_START_DATE))
                            <li class="list-group-item d-flex justify-content-between align-items-center">Installment Start Date<span class="badge badge-primary rounded-pill">{{ $vehicle->INSTALMENT_START_DATE}}</span></li>
                            @else
                            <li class="list-group-item d-flex justify-content-between align-items-center">Installment Start Date<span class="badge badge-primary rounded-pill"></span></li>
                            @endif
                         
                  
                        </ul>
                    </div>
                    </div>
                </div>
     </div>
     <!-- end ribon -->
       <!-- end ribon -->
       <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">Charges and Fees</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">OTHER_CHARGES<span class="badge badge-primary rounded-pill">{{number_format($vehicle->OTHER_CHARGES, 2,'.',',')  }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">PROCESSING_FEE<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->PROCESSING_FEE, 2,'.',',') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">PRODUCT_INTEREST_RATE<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->PRODUCT_INTEREST_RATE, 2,'.',',') }}%</span></li>
 
                       </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">INSURANCE<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->INSURANCE, 2,'.',',') }}</span></l>
                            <li class="list-group-item d-flex justify-content-between align-items-center">PRODUCT_PROCESSING_FEE<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->PRODUCT_PROCESSING_FEE, 2,'.',',') }}%</span></li>
                            <!-- <li class="list-group-item d-flex justify-content-between align-items-center">TOTAL_AMOUNT_TO_PAY<span class="badge badge-primary rounded-pill">{{ number_format($vehicle->TOTAL_AMOUNT_TO_PAY, 2,'.',',') }}</span></li> -->
                        </ul>
                    </div>
                    </div>
                </div>
     </div>
     <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary"> Bank and Branch Details</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">BANK_ACCOUNT_NUMBER<span class="badge badge-primary rounded-pill">{{ $vehicle->BANK_ACCOUNT_NUMBER }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">BRANCH_NAME<span class="badge badge-primary rounded-pill">{{ $vehicle->BRANCH_NAME }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">INIT_BRANCH<span class="badge badge-primary rounded-pill">{{ $vehicle->INIT_BRANCH }}</span></li>
                          </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">BEN_BRANCH<span class="badge badge-primary rounded-pill">{{ $vehicle->BEN_BRANCH }}</span></l>
                            <li class="list-group-item d-flex justify-content-between align-items-center">NEAREST_BRANCH_NAME<span class="badge badge-primary rounded-pill">{{ $vehicle->NEAREST_BRANCH_NAME }}</span></li>
                         </ul>
                    </div>
                    </div>
                </div>
     </div>
     <!-- end ribon -->
          
     <!-- <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">Currency and Financial Codes</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">BEN_CURRENCY<span class="badge badge-primary rounded-pill">{{ $vehicle->BEN_CURRENCY }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">INIT_CURRENCY<span class="badge badge-primary rounded-pill">{{ $vehicle->INIT_CURRENCY }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">PRODUCT_CODE<span class="badge badge-primary rounded-pill">{{ $vehicle->PRODUCT_CODE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">VOTE_CODE<span class="badge badge-primary rounded-pill">{{ $vehicle->VOTE_CODE }}</span></li>
                 
                          </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">VOTE_NAME<span class="badge badge-primary rounded-pill">{{ $vehicle->VOTE_NAME }}</span></l>
                            <li class="list-group-item d-flex justify-content-between align-items-center">PRODUCT_INTEREST_RATE<span class="badge badge-primary rounded-pill">{{ $vehicle->PRODUCT_INTEREST_RATE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">PRODUCT_PROCESSING_FEE<span class="badge badge-primary rounded-pill">{{ $vehicle->PRODUCT_PROCESSING_FEE }}</span></li>
               
                         </ul>
                    </div>
                    </div>
                </div>
     </div> -->
     <!-- <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">Dates</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                                    </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                          
                           
                        </ul>
                    </div>
                    </div>
                </div>
     </div> -->
     <!-- end ribon -->
     <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary"> Approval and Confirmation</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                          <li class="list-group-item d-flex justify-content-between align-items-center">TRANSACTION_STATUS<span class="badge badge-primary rounded-pill">{{ $vehicle->TRANSACTION_STATUS }}</span></l>
                     
                            <li class="list-group-item d-flex justify-content-between align-items-center">APPROVAL_DESCRIPTION<span class="badge badge-primary rounded-pill">{{ $vehicle->APPROVAL_DESCRIPTION }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">APPROVED_BY<span class="badge badge-primary rounded-pill">{{ $vehicle->APPROVED_BY }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">APPROVED_DATE<span class="badge badge-primary rounded-pill">{{ $vehicle->APPROVED_DATE }}</span></li>
                            
                          </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">REASON<span class="badge badge-primary rounded-pill">{{ $vehicle->REASON }}</span></l>
                            <li class="list-group-item d-flex justify-content-between align-items-center">LOAN_REQUEST_INITIATOR<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_REQUEST_INITIATOR }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">LOAN_REQUEST_CHECKER<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_REQUEST_CHECKER }} </span></li>
                          </ul>
                    </div>
                    </div>
                </div>
     </div>
     <!-- end ribon -->
 
   
 
   
     <!-- end ribon -->
     <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-primary">CBS (Core Banking System) Response</div>
                    <div class="row">
                    <div class="col-lg-6">
                    <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">CBS_RESPONSE_CODE<span class="badge badge-primary rounded-pill">{{ $vehicle->CBS_RESPONSE_CODE }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">CBS_RESPONSE_DESCRIPTION<span class="badge badge-primary rounded-pill">{{ $vehicle->CBS_RESPONSE_DESCRIPTION }}</span></li>
                       </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul class="list-group">
                                  <li class="list-group-item d-flex justify-content-between align-items-center">DISBURSEMENT_RECEIPT<span class="badge badge-primary rounded-pill">{{ $vehicle->DISBURSEMENT_RECEIPT }}</span></li>
                                  @if(isset($vehicle->LOAN_DISBURSED_DATE))
                            <li class="list-group-item d-flex justify-content-between align-items-center">LOAN_DISBURSED_DATE<span class="badge badge-primary rounded-pill">{{ $vehicle->LOAN_DISBURSED_DATE->format('d M Y') }}</span></li>
                            @else
                            <li class="list-group-item d-flex justify-content-between align-items-center">LOAN_DISBURSED_DATE<span class="badge badge-primary rounded-pill"></span></li>
                            @endif
                        </ul>
                    </div>
                    </div>
                </div>
     </div>
     <!-- end ribon -->
     </div>
     <div class="ribbon-wrapper card">
                <div class="card-body">
                    <div class="ribbon ribbon-clip ribbon-secondary">Actions</div>
                    <div class="row">

                @if($vehicle->TRANSACTION_STATUS === 'Pending')
                @if(Auth::user()->role == 'Credit Officer')
                <div class="row">  
                        <div class="col-lg-6">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal"> Credit Officer Approve Request <i class="icofont icofont-tick-boxed"></i></button>
                        </div>
                        <div class="col-lg-6">
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Officer Reject Request <i class="icofont icofont-trash"></i></button>
                        </div>
                </div>
                @else
                <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                    Awaiting CO review and verification
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                </div>
                @endif
                    @endif
         
                        @if($vehicle->TRANSACTION_STATUS === 'Cancelled')
                            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                                <i class="icofont icofont-minus-circle"></i>
                                    This Request Has been Canceled
                                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                                </div>
                        @endif   

            @if($vehicle->TRANSACTION_STATUS === 'Approved' )
           
                @if(Auth::user()->role == 'Credit Admin' && !isset($approval_datils))

                @if(!empty($vehicle->DISBURSEMENT_RECEIPT))
                         <!-- The LINAC_ACCOUNT_NUMBER is not null or empty -->
                         <div class="col-lg-12">
                            <br/> <br/> <br/>
                         <li class="list-group-item d-flex justify-content-between align-items-center">Loan Account<span class="badge badge-primary rounded-pill">{{ $vehicle->DISBURSEMENT_RECEIPT ?? 'Not set' }}</span></li>
                         <br/> <br/> <br/>
                         </div>
                    @else
                    <div class="row">  
                    <div class="col-lg-2">
                        </div>
                            <div class="col-lg-8">
                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalUpdateLoanDetails">Update Loan Account Details </button>
                            </div>
                            <div class="col-lg-2">
                        </div>
                    </div>
                    <br/><br/>
                    <br/>
                    @endif
                    @if(!empty($vehicle->DISBURSEMENT_RECEIPT))

                    <div class="row">  
                            <div class="col-lg-6">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">Credit Admin  Disbursement Initiation <i class="icofont icofont-tick-boxed"></i></button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Admin Reject Disbursement Initiation <i class="icofont icofont-trash"></i></button>
                            </div>
                    </div>
                    @endif
                    @elseif($approval_datils && $approval_datils->status == 'INITIATED')
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Operation Action For Approval
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    @elseif($approval_datils && $approval_datils->status == 'INITIATED_REJECTION')
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Operation Action For Rejection
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    @elseif($approval_datils && $approval_datils->status == 'DISBURSED')
                    @else
                @endif 

            @if(isset($approval_datils))
                @if(Auth::user()->role == 'Credit Operation' && $approval_datils->status=='INITIATED')

                <div class="row"> 
                            <div class="col-lg-6">
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalUpdateLoanDetailsLoanAcc">Update Loan Account Details </button>
                            </div>
                </div>
<br/><br/>
                    <div class="row">  
                            <div class="col-lg-6">
                                <button class="btn btn-primary  w-100" data-bs-toggle="modal" data-bs-target="#newModal"> Credit Operation Approve Request <i class="icofont icofont-tick-boxed"></i></button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-danger  w-100" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Operation Reject Request <i class="icofont icofont-trash"></i></button>
                            </div>
                    </div>
                  
                    @elseif(Auth::user()->role == 'Credit Operation' && $approval_datils->status=='INITIATED_REJECTION')
                    <div class="row">  
                            
                            <div class="col-lg-12">
                                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#newModalReject"> Credit Operation Reject Request <i class="icofont icofont-trash"></i></button>
                            </div>
                           
                    </div>
                    @elseif($approval_datils->status=='DISBURSED' || $vehicle->TRANSACTION_STATUS =='Disbursed')
                    <div class="card-body">
                    <div class="row">
                            <div class="col-lg-12">
                                <ul class="list-group"> 
                                    <!-- <li class="list-group-item d-flex justify-content-between align-items-center">Initated At<span class="badge badge-primary rounded-pill">{{ $approval_datils->initiator_date->format('d M Y H:i:s') }}</span></li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center"> Disbursed At<span class="badge badge-primary rounded-pill">{{ $approval_datils->checker_date->format('d M Y H:i:s')  }}</span></li>  -->
                                    <li class="list-group-item d-flex justify-content-between align-items-center">Request Completed<span class="badge badge-primary rounded-pill">{{ $approval_datils->status  }}</span></li> 
                            </ul>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i>
                            Awaiting Credit Operation Action
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                @endif
                <!-- CO  INITIATE LIQUIDATA
TO CA  COMOT
WITH POPUP -->
            @endif
            @endif
            @if($vehicle->TRANSACTION_STATUS === 'Disbursed' )
            
        @if(Auth::user()->role == 'Credit Officer')
        <div class="col-lg-12">    
            <!-- <button  class="btn btn-outline-primary w-100"  type="button"> Initiate Liqudation <i class="icofont icofont-ui-rate-add"></i></button> -->
            @if($vehicle->LIQUIDATION_STATUS === 'Initiated' )
            <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Status<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_STATUS  }}</span></li> 
            @else
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalLiqudation"> Initiate Liqudation </button>
            @endif
                         
        </div>
        @elseif(Auth::user()->role == 'Credit Admin' )
        @if($vehicle->LIQUIDATION_STATUS === 'Initiated' )
        <div class="col-lg-12">    
            <!-- <button  class="btn btn-outline-primary w-100"  type="button" > Complete Liqudation <i class="icofont icofont-ui-rate-add"></i></button> -->
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newModalLiqudationComplete"> Complete Liqudation </button>
        </div>
        @elseif($vehicle->LIQUIDATION_STATUS === 'Complete')
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Initiator<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_MAKER  }}</span></li> 
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Initiator Remarks<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_MAKER_REMARKS  }}</span></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Checker<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_CHECKER  }}</span></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">Liquidataion Checker Remarks<span class="badge badge-primary rounded-pill">{{ $vehicle->LIQUIDATION_CHECKER_REMARKS  }}</span></li>

        <button wire:click="liqudateLoanAccount({{$vehicle->ID}})" class="btn btn-outline-primary w-100"  type="button" wire:loading.remove> Notify Utumish <i class="icofont icofont-ui-rate-add"></i></button>
        @else
        <li class="list-group-item d-flex justify-content-between align-items-center">Request Completed / You can not Liquidate This Loan Contact Credit Officer To initiate<span class="badge badge-primary rounded-pill">{{ $approval_datils->status ?? 'Not set' }}</span></li> 
        @endif
        @else
                 
        @endif

            <br/>
             <div wire:loading.delay>
             <div class="loader-box">
                 <div class="loader-7" style="width: 50px; height:50px;"></div>
                 <br/>
                     <h5 class="f-w-100">Processing ...</h5>
                 </div>
             </div>
             <br/>
             <br/>
             @endif
            <!-- ------ -->
                    </div>
                </div>
     </div>
     <!-- end ribon -->
    

       <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModalLiqudation" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Descriptions</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-liqiudation') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Initiated"  required  name="status">
                            </div>
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Details</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" > Submit Liqudation Initiation</button>
                </form>
            </div>
        </div>
    </div>
    </div>

          <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModalLiqudationComplete" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Descriptions</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-liqiudation') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Complete"  required  name="status">
                            </div>
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Details</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" > Submit </button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->
   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Approve</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Approved"  required  name="status">
                            </div>
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Approve Reason</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm To Submit</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="newModalReject" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-danger text-white">
                <h5 class="modal-title">REJECT</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="REJECTED"  required  name="status">
                            </div>
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Reject Reason</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="reason"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>

                <!-- <button wire:click="loanInitialApproval" class="btn btn-outline-primary  pull-right"  type="button" wire:loading.remove> Confirm To Submit <i class="icofont icofont-ui-rate-add"></i></button> -->
                       
                <button class="btn btn-primary" type="submit" >Submit Rejection</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

 <!-- SEARCH MODAL START -->
 <div class="modal fade" id="newModalReject" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-danger text-white">
                <h5 class="modal-title">Reject</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form action="{{ Route('update-staff') }}" method="post">
               
                    @csrf

                    <div class="row">
                        
                        <div class="col-lg-12">
                        <div class="form-group">
                                    <label class="col-form-label" >Reject Reason</label>
                                    <textarea class="form-control" type="text" value="{{ old('street') }}" required  name="conditions"></textarea>
                                </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-danger" type="submit" >Confirm To Reject</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

     <!-- SEARCH MODAL START -->
     <div class="modal fade" id="newModalUpdateLoanDetails" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Update Loan Account Details</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-loanAccount') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                        <label class="col-form-label">Loan Account</label>
                        <input class="form-control" type="text" name="loanAccount" required pattern="\d{10}" maxlength="10" title="Please enter exactly 10 digits">
                    </div>
                    <div class="form-group">
    <label class="col-form-label">Check Digit</label>
    <input class="form-control" type="number" name="checkDigit" required min="0" max="99" title="Please enter a number between 0 and 99">
</div>



                           
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Approved"  required  name="status">
                            </div>
                        
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm To Submit</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

  <!-- SEARCH MODAL START -->
  <div class="modal fade" id="newModalUpdateLoanDetailsLoanAcc" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">Update Loan Account </h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                
                <form action="{{ Route('update-staff-loanAccountonly') }}" method="post">
                    @csrf

                    <div class="row">
                    <div class="form-group">
                        <label class="col-form-label">Loan Account</label>
                        <input class="form-control" type="text" name="loanAccount" required pattern="\d{10}" maxlength="10" title="Please enter exactly 10 digits">
                    </div>
                   
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="number" value="{{ $vehicle->ID }}"  required  name="id">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label"hidden >id</label>
                                <input class="form-control" hidden type="text" value="Approved"  required  name="status">
                            </div>
                        
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm To Submit</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->
    						
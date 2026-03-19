<table>
    <thead>
        <tr>
        <th>#</th>

        <th> Customer name</th>
        <th> Loan amount</th>
        <th> Loan Account</th>
        <th> Period</th>
        <th> Bank Branch</th>
        <th> Region</th>
        <th> District</th>
        <th> Loan Purpose</th>
        <th> Application Date</th>
        <th> Disbursement date</th>
        <th> Application Number</th>
        <th> Deposit Account number</th>
        <th> Phone number</th>
        <th> Vote number</th>
        <th> Vote name</th>
        <th> Deductible Amount</th>
        <th> Loan processing fee</th>
        <th> Insurance</th>
        <th> Loan Status</th>
    </tr>
    </thead>
    <tbody>
    
    @foreach ($loans as $loan)
        <tr>
            <th scope="row">{{$loop->index + 1}}.</th>
            <td>{{strtoupper($loan['FIRST_NAME'])}}  {{strtoupper($loan['MIDDLE_NAME'])}} {{strtoupper($loan['LAST_NAME'])}}</td>
            <td>{{number_format($loan['REQUESTED_AMOUNT'], 2,'.',',')}}</td>
            <td>{{$loan['DISBURSEMENT_RECEIPT']}}</td>
            <td>{{$loan['TENURE']}}</td>
            <td>{{$loan['INIT_BRANCH']}}</td>
            <td></td>
            <td></td>
            <td>{{$loan['LOAN_PURPOSE']}}</td>
            @if(isset($loan['APPLICATION_DATE']))
            <td>{{$loan['APPLICATION_DATE']->format('d M Y H:i:s')}}</td>   
            @else
            <td></td>
            @endif
            @if(isset($loan['LOAN_DISBURSED_DATE']))
            <td>{{$loan['LOAN_DISBURSED_DATE']->format('d M Y H:i:s')}}</td>   
            @else
            <td></td>
            @endif
            <td>{{$loan['APPLICATION_NUMBER']}}</td>
            <td>{{$loan['BANK_ACCOUNT_NUMBER']}}</td>
            <td>{{$loan['MSISDN']}}</td>
            <td>{{$loan['VOTE_CODE']}}</td>
            <td>{{$loan['VOTE_NAME']}}</td>
            <td>{{number_format($loan['MONTHLY_DEDUCTION'], 2,'.',',')}}</td>
            <td>{{number_format($loan['PROCESSING_FEE'], 2,'.',',')}}</td>
            <td>{{number_format($loan['INSURANCE'], 2,'.',',')}}</td>
            <td>{{$loan['LOAN_STATUS']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>


<table>
  <thead>
    <tr><th colspan="16"><b>{{ $title }}</b></th></tr>
    <tr><th colspan="16">Exported {{ now('Africa/Dar_es_Salaam')->format('d M Y H:i') }} · {{ $rows->count() }} records · total deducted {{ number_format($rows->sum('DEDUCTION_AMOUNT'), 2) }}</th></tr>
    <tr>
      <th>Payroll date</th><th>Check number</th><th>First name</th><th>Middle name</th><th>Last name</th><th>National ID</th>
      <th>Loan number</th><th>Application number</th><th>Loan type</th><th>Vote</th><th>Department</th>
      <th>Deduction amount</th><th>Balance left</th><th>Stop pay</th><th>Stop pay reason</th><th>Received at MCB</th>
    </tr>
  </thead>
  <tbody>
  @foreach($rows as $r)
    <tr>
      <td>{{ $r->CHECK_DATE }}</td>
      <td>{{ $r->CHECK_NUMBER }}</td>
      <td>{{ $r->FIRST_DATE }}</td>
      <td>{{ $r->MIDDLE_NAME }}</td>
      <td>{{ $r->LAST_NAME }}</td>
      <td>{{ $r->NATIONAL_ID }}</td>
      <td>{{ $r->LOAN_ID }}</td>
      <td>{{ optional($r->loan)->APPLICATION_NUMBER }}</td>
      <td>{{ optional($r->loan)->LOAN_TYPE }}</td>
      <td>{{ $r->VOTE_NAME }}</td>
      <td>{{ $r->DEPARTMENT_NAME }}</td>
      <td>{{ $r->DEDUCTION_AMOUNT }}</td>
      <td>{{ $r->BALANCE_AMOUNT }}</td>
      <td>{{ $r->HAS_STOP_PAY ? 'Yes' : 'No' }}</td>
      <td>{{ $r->HAS_STOP_PAY_REASON }}</td>
      <td>{{ $r->REQUEST_DATE }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

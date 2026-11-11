<table>
    <thead>
        <tr>
            <th>#</th>
            @foreach ($columns as $header => $value)
            <th> {{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach ($loans as $loan)
        @php $payment = $payments[$loan->ID] ?? null; @endphp
        <tr>
            <th scope="row">{{ $loop->index + 1 }}.</th>
            @foreach ($columns as $value)
            <td>{{ $value($loan, $payment) }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

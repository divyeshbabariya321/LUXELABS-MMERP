@if (count($paymentData) > 0)
    @foreach ($paymentData as $history)
        <tr>
            <td>{{ $history->id }}</td>
            <td>{{ $history->amount }}</td>
            <td>{{ $history->date }}</td>
            <td>{{ $history->description }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="4">No payment history found</td>
    </tr>
@endif
@foreach ($data as $key => $refferal)
    <tr>
        <td>{{ $refferal->id }}</td>
        <td>{{ $refferal->referrer_first_name . ' ' . $refferal->referrer_last_name }}</td>
        <td>{{ $refferal->referrer_email }}</td>
        <td>{{ $refferal->referrer_phone }}</td>
        <td>{{ $refferal->referee_first_name . ' ' . $refferal->referee_last_name }}</td>
        <td>{{ $refferal->referee_email }}</td>
        <td>{{ $refferal->referee_phone }}</td>
        <td>{{ $refferal->website }}</td>
        <td>{{ wordWrap($refferal->status, 30) }}</td>
        <td>{{ Carbon\Carbon::parse($refferal->created_at)->format('d-m-y H:i') }}</td>
        <td>
            {{ html()->form('DELETE', route('referfriend.destroy', [$refferal->id]))->style('display:inline')->open() }}
            <button type="submit" class="btn btn-image p-0"><i class="fa fa-trash"></i></button>
            {{ html()->form()->close() }}
            <button class="btn btn-image view_error p-0" data-toggle="modal" data-id="{{ $refferal->id }}"> <i
                    class="fa fa-eye"></i> </button>
        </td>
    </tr>
@endforeach

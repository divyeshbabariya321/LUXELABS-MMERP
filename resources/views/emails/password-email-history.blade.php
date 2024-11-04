@foreach($passwordEmails as $email)
{{$email}}
<tr>
    <td>
        {{ $email->model_type }}
    </td>
    <td>
        {{ $email->to }}
    </td>
    <td style="word-wrap: break-word;">
        {{ $email->from }}
    </td>
    <td>
        {{ $email->subject  }}
    </td>
    <td>
        <?= $email->message ?>
    </td>
    <td>
        {{ $email->error_message  }}
    </td>

    <td>
    {{ $email->created_at->format('d-m-y H:i:s') }}
    </td>
</tr>
@endforeach

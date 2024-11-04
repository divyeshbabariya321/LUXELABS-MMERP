<table>
    <tr>
        <td>To</td>
        <td>
            <input type="email" required id="email_to_mail" class="form-control" name="to_mail" value="{{ $data->customer->email }}">
        </td>
    </tr>
    <tr>
        <td>From</td>
        <td>
            <input type="email" required id="email_from_mail" class="form-control" name="from_mail" value="{{ $from }}">
        </td>
    </tr>
    <tr>
        <td>Preview</td>
        <td>
            <textarea name="editableFile" rows="10" id="customEmailContent">{{ $preview }}</textarea>
        </td>
    </tr>
</table>
@foreach($members as $member)
    <tr>
        <td style="vertical-align:middle;">{{ $loop->iteration }}</td>
        <td style="vertical-align:middle;">{{ $member->id }}</td>
        <td style="vertical-align:middle;">{{ $member->time_doctor_user_id }}</td>
        <td style="vertical-align:middle;">{{ $member->email }}</td>
        <td style="vertical-align:middle;">{{ $member->account_detail->time_doctor_email }}</td>
        <td style="vertical-align:middle;">{{ $member->account_detail->created_at }}</td>
        <td style="vertical-align:middle;">
            <div class="form-group"style="margin-top: -10px;margin-bottom:-10px;">
                <select onchange="saveUser(this)"class="form-control">
                <option value="unassigned">Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}|{{ $member->time_doctor_user_id }}" <?= ($member->user_id == $user->id) ? 'selected' : '' ?>>{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
@endforeach
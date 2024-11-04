@foreach($users as $key => $user)
                      <tr>
                            <td><input type="checkbox" class="checkbox_ch" id="u{{ $user->id }}" name="userIds[]" value="{{ $user->id }}"></td>
                            <td>{{$key+1}}</td>
                            <td><label for="u{{ $user->id }}"> {{ $user->name }} </label></td>
                            <td><label for="u{{ $user->id }}" > {{ $user->email }}</label></td>
                            <td>Send WhatsApp</td>
                            <td>
                                <button class="btn btn-xs btn-none-border show_password_history" data-id="{{ $user->id }}" data-email="{{ $user->email }}" title="Password Email History"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-xs btn-none-border send_password_email" data-id="{{ $user->id }}" data-email="{{ $user->email }}" title="Send Email" data-toggle="modal" data-target="#passwordSendEmailModal"><i class="fa fa-envelope"></i></button>
                            </td>
                      </tr>
                    @endforeach
@foreach ($data as $key => $user)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td class="number">
                        <select class="form-control ui-autocomplete-input whatsapp_number" data-user-id="{{ $user->id }}">
                            <option>-- Select --</option>
                            @foreach($whatsapp as $wp)
                            @if(!empty($wp->number))
                            <option value="{{ is_string($wp) ? $wp : $wp->number }}" @if((!empty($wp->number) && $user->whatsapp_number == $wp->number) || ((!empty($wp) && is_string($wp) && $user->whatsapp_number == $wp))) selected=selected @endif>
                                {{ is_string($wp) ? $wp : $wp->number }}</option>
                            @endif
                            @endforeach
                            
                        </select>
                    </td>
                    <td><span class="user-status {{ $user->isOnline() ? 'is-online' : '' }}"></span> {{ str_replace( '_' , ' ' ,$user->name) }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <label class="badge badge-success">{{ $v }}</label>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <button data-toggle="tooltip" type="button" class="btn btn-xs btn-image load-communication-modal" data-object='user' data-id="{{ $user->id }}" title="Load messages"><img src="/images/chat.png" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" alt=""></button>
                        @if (Auth::id() == $user->id)
                            <a class="btn btn-image" href="{{ route('users.show',$user->id) }}"><img src="/images/view.png"/></a>
                        @else
                            <a class="btn btn-image" href="{{ route('users.edit',$user->id) }}"><img src="/images/edit.png"/></a>
                        @endif

                        {{ html()->form('DELETE', route('users.destroy', [$user->id]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}
                        <a href="{{ action([\App\Http\Controllers\UserActionsController::class, 'show'], $user->id) }}">Info</a>
                        <a title="Payments" class="btn btn-image" href="/users/{{$user->id}}/payments"><span class="glyphicon glyphicon-usd"></span></a>
                    </td>
                </tr>
@endforeach
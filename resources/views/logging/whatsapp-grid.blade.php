            @foreach($array as $row)
                @php
                    $row_array = explode(",",$row['error_message1']);
                    foreach ($row_array as $key => $value) {
                        if(strpos($value,'message"')){
                            unset($row_array[$key]);
                        }
                    }

                    $message = implode(',',$row_array);
                    // $message = strpos($row['error_message1'],'"message');
                    // $message_str = strtok(substr($row['error_message1'],$message), ',');
                   
                    if(isset($row['file']) && $row['file'] == 'chatapi')
                    {
                        $message1 = strpos($row['error_message2'],'whatsapp_number');
                        $number = strpos($row['error_message2'],'"number":');
                        $receiver_number = substr($row['error_message2'],$number+10,12);
                        $sender_number = substr($row['error_message2'],$message1+18,12);
                        $null = substr($row['error_message2'],$message1+17,4);
                    }else{
                        $message1 = strpos($row['error_message1'],'whatsapp_number');
                        $number = strpos($row['error_message1'],'"number":');
                        $receiver_number = substr($row['error_message1'],$number+10,12);
                        $sender_number = substr($row['error_message1'],$message1+18,12);
                        $null = substr($row['error_message1'],$message1+17,4);
                    }

                    $sent_message = strpos($row['error_message1'],'"sent":true');

                    if($sent_message)
                        $sent_message_status = 1;
                    else
                        $sent_message_status = 0;

                @endphp
                    <tr>
                        <td>{{ $sr++ }}</td>
                        <td>{{ $row['date'] }}</td>
                       
                        @if($sent_message_status == 1)
                            <td>Yes</td>     
                        @else
                            <td>No</td>
                        @endif

                        @if ($message1 == '' || $null == "null")
                            <td></td>
                        @else
                            <td>{{ $sender_number }}</td>
                        @endif
                        @if ($number == '' )
                        <td></td>
                    @else
                        <td>{{ $receiver_number }}</td>
                    @endif
                        <td class="errorLog">
                            <div class="log-text-style">
                                @if ($isAdmin)
                                Message1 : {{$row['error_message1']}} <br>
                            @else
                                Message1 : {{ $message }} <br>

                                
                            @endif
                            @php
                                $str_msg = string_convert($row['error_message2'])
                            @endphp
                                    <br/>
                            Message2 : 
                                @foreach($str_msg as $key => $val)
                                    {{$val}}<br/>
                                @endforeach
                            </div>
                        </td>
                        <td>
                        @if((isset($row['error_message1']) && getStr($row['error_message1'])) || (isset($row['error_message2']) && getStr($row['error_message2'])))
                            @if ($isAdmin)
                                <button class="btn btn-success sentMessage text-center" >
                                    Resend
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
                
            @endforeach
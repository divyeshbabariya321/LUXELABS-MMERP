     @php $i = $count + 1; @endphp
                @foreach($api_response as $res)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ isset($res->storeWebsite->title) ? $res->storeWebsite->title : '' }}</td>
                        <td>{{ $res->key }}</td>
                        <td>{{ $res->value }}</td>
                        <td>
                            <a class="btn btn-secondary" onclick="editModal({{ $res->id}});" href="javascript:void(0);">Edit</a>
                            <a class="btn btn-secondary" href="{{ route('api-response-message.responseDelete',['id' => $res->id]) }}">Delete</a>
                        </td>
                    </tr>
                    @php $i = $i+1; @endphp
                @endforeach
         
  @foreach ($documents as $document)
                <tr>
                    <td class="p-2">{{ $document->updated_at->format('d-m-Y') }}</td>
                    <td class="p-2">{{ !empty($document->user->name) ? $document->user->name : ''}}</td>
                    <td class="p-2">{{ !empty($document->user->agent_role) ? $document->user->agent_role : ''}}</td>
                    
                  
                        @if (strlen($document->name) > 40)
                        <td  style="word-break: break-word;" data-modal-title="Document type" data-log_message="{{ $document->name }}" class="log-message-popup p-2">{{ substr($document->name,0,40) }}...</td>    
                    @else
                        <td class="p-2" style="word-break: break-word;">{{ $document->name }}</td>
                    @endif      

                    @if(isset($document->documentCategory->name))
                   
                            @if (strlen($document->documentCategory->name) > 40)
                            <td  style="word-break: break-word;" data-modal-title="Category" data-log_message="{{ $document->documentCategory->name }}" class="log-message-popup p-2">{{ substr($document->documentCategory->name,0,40) }}...</td>    
                             @else
                            <td class="p-2" style="word-break: break-word;">{{ $document->documentCategory->name }}</td>
                             @endif  

                    @else
                    <td class="p-2">
                    </td>
                    @endif

                        @if (strlen($document->filename) > 40)
                        <td  style="word-break: break-word;" data-modal-title="Filename" data-log_message="{{ $document->filename }}" class="log-message-popup p-2">{{ substr($document->filename,0,40) }}...</td>
                    @else
                        <td class="p-2" style="word-break: break-word;">{{ $document->filename }}</td>
                    @endif     

                      <td class="p-2">
                        <a href="{{ route('document.download', $document->id) }}" title="Download" class="btn btn-image text-dark btn-xs p-0"><i class="fa fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-image sendWhatsapp btn-xs p-0" title="Send" data-id="{{ $document->id }}"><img src="{{asset('/images/send.png')}}" /></button>
                        <button type="button" class="btn btn-image sendEmail btn-xs p-0" title="Email" data-id="{{ $document->id }}"><img src="{{asset('/images/customer-email.png')}}"/></button>

                        {{ html()->form('DELETE', route('document.destroy', [$document->id]))->style('display:inline')->open() }}

                        <button type="submit" class="btn btn-image btn-xs p-0" title="Delete"><img src="{{asset('/images/delete.png')}}" /></button>
                        <button type="button" class="btn btn-image make-remark d-inline btn-xs p-0" title="Remark" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $document->id }}"><img src="{{asset('/images/remark.png')}}" /></button>

                        {{ html()->form()->close() }}
                        <button type="button" class="btn btn-image uploadDocument btn-xs p-0" title="Upload Document" data-id="{{ $document->id }}"><img src="{{asset('/images/upload.png')}}" /></button>
                        
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='document' data-id="{{ $document->id }}" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                      
                     <p class="text-dark btn font-weight-bold btn-xs p-0">   V: {{ $document->version }}</p>
                    </td>
                </tr>
            @endforeach
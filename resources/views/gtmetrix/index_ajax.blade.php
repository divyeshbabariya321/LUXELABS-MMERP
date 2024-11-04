
                        @foreach ($list as $key)
                        
                        <tr>
                                <td>
                                    <input type="checkbox" name ="multi-run-test-type" class= "multi-run-test" value ="{{ $key->id }}">
                                </td>
                                <td>
                                    <a class="text-dark" href="{{ $key->website_url }}" target="_blank" title="Goto website"> {{ !empty($key->website_url) ? $key->website_url : $key->store_view_id }} </a>
                                </td>
                                <td>{{ $key->test_id }}</td>
                                <td>{{ $key->status }}</td>
                                <td class="expand-row-msg" data-name="error" data-id="{{$key->id}}">
                                    <span class="show-short-error-{{$key->id}}">{{ Str::limit($key->error, 35, '...')}}</span>
                                    <span style="word-break:break-all;" class="show-full-error-{{$key->id}} hidden">{{$key->error}}</span>
                                </td>
                                <td>
                                    <a class="text-dark" href="{{$key->report_url}}" target="_blank" title="Show report"> Report </a>
                                </td>
                                <td>{{ $key->html_load_time }}</td>
                                <td>{{ $key->html_bytes }}</td>
                                <td>{{ $key->page_load_time }}</td>
                                <td>{{ $key->page_bytes }}</td>
                                <td>{{ $key->page_elements }}</td>
                                <td>{{ $key->pagespeed_score }}</td>
                                <td>{{ $key->yslow_score }}</td>
                                <td>
                                    @if (!empty($key->resources) && is_array($key->resources))
                                        <ul style="display: inline-block;">
                                            @foreach ($key->resources as $item => $value)
                                                    <li> <a href="{{ $value }}" target="_blank" rel="noopener noreferrer"> {{ $item }} </a> </li>
                                            @endforeach
                                        </ul>
                                    @else
                                     --
                                    @endif
                                    
                                <td>{{ $key->created_at }}</td>
                                <td>
                                    <a class="text-dark" target="__blank" href="{{url('/')}}{{ $key->pdf_file }}"> {{ !empty($key->pdf_file) ? 'Open' : 'N/A' }} </a>
                                </td>
                                <td>  
                                    <button class="btn show-history btn-xs text-dark" title="Show old history" data-url="{{ route('gtmetrix.web-hitstory',[ 'id'=>$key->website_url ])}}">
                                        <i class="fa fa-history" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn run-test btn-xs text-dark" title="Run Test" data-id="{{ $key->id }}">
                                        <i class="fa fa-play" aria-hidden="true"></i>
                                    </button>
                                    @if ($key->status == "completed")
                                        <button class="btn show-pagespeed btn-xs text-dark" title="Show Pagespeed Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>'pagespeed','id'=>$key->test_id])}}" data-type="pagespeed">
                                            <i class="fa fa-tachometer" aria-hidden="true"></i>
                                        </button>
                                        <button class="btn show-pagespeed btn-xs text-dark" title="Show Yslow Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>'yslow','id'=>$key->test_id])}}">
                                            <i class="fa fa-compass" aria-hidden="true"></i>
                                        </button>
                                        <button class="btn show-comparison btn-xs text-dark" title="Show comparison" data-url="{{ route('gtmetrix.getstatsCmp',['id'=>$key->test_id])}}">
                                        <i class="fa fa-balance-scale" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                   
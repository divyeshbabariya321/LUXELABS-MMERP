

                    
                        @foreach ($magentoSettings as $magentoSetting) 
                            <tr>
                                <td><input type="checkbox" name="settings_check" class="settings_check" value="{{ $magentoSetting->id }}" data-file="{{ $magentoSetting->id }}" data-id="{{ $magentoSetting->id }}"></td>
                                <td>{{ $magentoSetting->id }}</td>

                                                                @if($magentoSetting->scope === 'default')
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo !empty($magentoSetting->website->title) ?? $magentoSetting->fromStoreId->title; ?>')" >
                                            @if(!empty($magentoSetting->website->title))
                                                {{  substr($magentoSetting->fromStoreId->title, 0,10)  }} 
                                                @if(strlen($magentoSetting->website->title ?? $magentoSetting->fromStoreId->title) > 10) ... @endif
                                            @endif  
                                        </td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal(' ')" >-</td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal(' ')" >-</td>

                                @elseif($magentoSetting->scope === 'websites')
                                        
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->title : $magentoSetting->fromStoreId->title ; ?>')" >
                                            {{ $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->title : $magentoSetting->fromStoreId->title }} ...
                                        </td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->store->website->name ?? $magentoSetting->fromStoreId->title; ?>')" >{{ substr($magentoSetting->store->website->name ?? $magentoSetting->fromStoreId->title, 0,10) }} @if(strlen($magentoSetting->store->website->name ?? $magentoSetting->fromStoreId->website) > 10) ... @endif</td>
                                        <td>-</td>
                                        
                                @else 
                                        <td>{{ $magentoSetting->storeview && $magentoSetting->storeview->websiteStore && $magentoSetting->storeview->websiteStore->website && $magentoSetting->storeview->websiteStore->website->storeWebsite ? $magentoSetting->storeview->websiteStore->website->storeWebsite->website : $magentoSetting->fromStoreId->website }}</td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('{{$magentoSetting->storeview && $magentoSetting->storeview->websiteStore ? $magentoSetting->storeview->websiteStore->name : $magentoSetting->fromStoreId->title}}')" >  {{   substr($magentoSetting->storeview && $magentoSetting->storeview->websiteStore ? $magentoSetting->storeview->websiteStore->name : $magentoSetting->fromStoreId->title, 0,10) }}</td>
                                        <td>{{ $magentoSetting->storeview->code ?? ''}}</td>
                                @endif

                                <td>{{ $magentoSetting->scope }}</td>
                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->name ; ?>')" >{{ substr($magentoSetting->name,0,12) }} @if(strlen($magentoSetting->name) > 12) ... @endif</td>

                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->path ; ?>')" >{{ substr($magentoSetting->path,0,12) }} @if(strlen($magentoSetting->path) > 12) ... @endif</td>

                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->value ; ?>')" >{{ substr($magentoSetting->value,0,12) }} @if(strlen($magentoSetting->value) > 12) ... @endif</td>
								<td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php if(isset($magentoSetting->value_on_magento)) {echo  $magentoSetting->value_on_magento;   } ?>')">
                                    @if(isset($magentoSetting->value_on_magento)) {{ substr( $magentoSetting->value_on_magento, 0,10) }} @if(strlen($magentoSetting->value_on_magento) > 10) ... @endif @endif
                                </td>
                                <td>{{ $magentoSetting->created_at }}</td>
                                <td>{{ $magentoSetting->status }}</td>
                                <td>{{ $magentoSetting->uname }}</td>
                                <td>
                                    <button type="button" value="{{ $magentoSetting->scope }}" class="btn btn-image edit-setting p-0" data-setting="{{ json_encode($magentoSetting) }}" ><img src="/images/edit.png"></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image delete-setting p-0" ><img src="/images/delete.png"></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image push_logs p-0" ><i class="fa fa-eye"></i></button>
                                </td>
                            </tr>
                        @endforeach
                  
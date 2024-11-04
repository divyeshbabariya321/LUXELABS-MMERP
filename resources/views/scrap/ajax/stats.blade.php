@php
    $status = request()->get('status', '');
    $excelOnly = request()->get('excelOnly', '');
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $hod = $user->hasRole('HOD of CRM');
    $totalCountedUrl=0;
@endphp

<div class="row mt-3">
    <div class="col-md-12" id="plannerColumn">
        <div class="">
            <table class="table table-bordered table-striped sort-priority-scrapper">
                <thead>
                <tr>
                    @if(!empty($dynamicColumnsToShows))
                        @if (!in_array('Checkbox', $dynamicColumnsToShows))
                            <th width="1%"></th>
                        @endif
                        @if (!in_array('#', $dynamicColumnsToShows))
                            <th>#</th>
                        @endif
                        @if (!in_array('Supplier', $dynamicColumnsToShows))
                            <th>Supplier</th>
                        @endif
                            <!-- <th>Server</th> -->
                        @if (!in_array('Server ID', $dynamicColumnsToShows))
                            <th>Server ID</th>
                        @endif
                        @if (!in_array('Auto Restart', $dynamicColumnsToShows))
                            <th>Auto Restart</th>
                        @endif
                        @if (!in_array('Run Time', $dynamicColumnsToShows))
                            <th>Run Time</th>
                        @endif
                        @if (!in_array('Start Scrap', $dynamicColumnsToShows))
                            <th>Start Scrap</th>
                        @endif
                        @if (!in_array('Stock', $dynamicColumnsToShows))
                            <th>Stock</th>
                        @endif
                        @if (!in_array('URL Count', $dynamicColumnsToShows))
                            <th>URL Count</th>
                        @endif
                        @if (!in_array('YDay New', $dynamicColumnsToShows))
                            <th>YDay New</th>
                        @endif
                            <!-- <th>Errors</th>
                            <th>Warnings</th> -->
                        @if (!in_array('URL Count Scrap', $dynamicColumnsToShows))
                            <th>URL Count Scrap</th>
                        @endif
                        @if (!in_array('URLs', $dynamicColumnsToShows))
                            <th>URLs</th>
                        @endif
                            <!-- <th>Existing URLs</th>
                            <th>New URLs</th> -->
                            <!-- <th>Made By</th>
                            <th>Type</th>
                            <th>Parent Scrapper</th>
                            <th>Next Step</th> -->
                        @if (!in_array('Status', $dynamicColumnsToShows))
                            <th>Status</th>
                        @endif
                        <!-- @if (!in_array('Remarks', $dynamicColumnsToShows))
                            <th>Remarks</th>
                        @endif -->
                            <?php /*
                            <th>Devtask</th>
                            <th>Logs</th>
                            */ ?>
                        @if (!in_array('Full scrap', $dynamicColumnsToShows))
                            <th>Full scrap</th>
                        @endif
                        @if (!in_array('Scraper Duration', $dynamicColumnsToShows))
                            <th>Scraper Duration</th> 
                        @endif
                        @if (!in_array('Suppiier Inventory', $dynamicColumnsToShows))
                            <th>Suppiier Inventory</th>
                        @endif
                        @if (!in_array('Date Last Product Added', $dynamicColumnsToShows))
                            <th>Date Last Product Added</th>
                        @endif
                        @if (!in_array('Functions', $dynamicColumnsToShows))
                            <th>Functions</th>
                        @endif
                    @else
                        <th width="1%"></th>
                        <th>#</th>
                        <th>Supplier</th>
                        <!-- <th>Server</th> -->
                        <th>Server ID</th>
                        <th>Auto Restart</th>
                        <th>Run Time</th>
                        <th>Start Scrap</th>
                        <th>Stock</th>
                        <th>URL Count</th>
                        <th>YDay New</th>
                        <!-- <th>Errors</th>
                        <th>Warnings</th> -->
                        <th>URL Count Scrap</th>
                        <th>URLs</th>
                        <!-- <th>Existing URLs</th>
                        <th>New URLs</th> -->
                        <!-- <th>Made By</th>
                        <th>Type</th>
                        <th>Parent Scrapper</th>
                        <th>Next Step</th> -->
                        <th>Status</th>
                        <!-- <th>Remarks</th> -->
                        <?php /*
                        <th>Devtask</th>
                        <th>Logs</th>
                        */ ?>
                        <th>Full scrap</th>
                        <th>Scraper Duration</th> 
                        <th>Suppiier Inventory</th>
                        <th>Date Last Product Added</th>
                        <th>Functions</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @php $arMatchedScrapers = []; $ii=1; @endphp
                @foreach ($activeSuppliers as $supplier)
                    @if ( (stristr($supplier->scraper_name, '_excel') && (int) $excelOnly > -1 ) || (!stristr($supplier->scraper_name, '_excel') && (int) $excelOnly < 1 ) )
                        @php $data = null;  @endphp
                        @foreach($scrapeData as $tmpData)
                            @if ( !empty($tmpData->website) && strtolower($tmpData->website) == strtolower($supplier->scraper_name) )
                                @php $data = $tmpData; $arMatchedScrapers[] = $supplier->scraper_name @endphp
                            @endif
                        @endforeach

                        @if(!empty($dynamicColumnsToShows))
                            @php
                                // Set percentage
                                if ( isset($data->errors) && isset($data->total) ) {
                                    $percentage = ($data->errors * 100) / $data->total;
                                } else {
                                    $percentage = 0;
                                }

                                // Show correct background color
                                $hasError =  false;
                                $hasWarning = false;
                                if ( (!empty($data) && $data->running == 0) || $data == null ) {
                                    $hasError =  true;
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'" data-eleid="'.$supplier->id.'">';
                                } elseif ( $percentage > 25 ) {
                                    $hasWarning = true;
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'" data-eleid="'.$supplier->id.'">';
                                } else {
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'" data-eleid="'.$supplier->id.'">';
                                }

                                if($status == 1 && !$hasError) {
                                    continue;
                                }

                                $remark = /*\App\ScrapRemark::select('remark')->where('scraper_name',$supplier->scraper_name)->whereNull("scrap_field")->where('user_name','!=','')->orderBy('created_at','desc')->first();*/

                                $remark = $supplier->scrpRemark;

                                $chatMessage = count($supplier->latestMessageNew) > 0 ? $supplier->latestMessageNew[0] : null;


                                $lastError = $supplier->lastErrorFromScrapLogNew;
                                
                            @endphp

                            @if (!in_array('Checkbox', $dynamicColumnsToShows))
                            <td>
                                <input type="checkbox" name="scrap_check" class="scrap_check" value="{{ $supplier->id }}" data-id="{{ $supplier->id }}">
                            </td>
                            @endif

                            @if (!in_array('#', $dynamicColumnsToShows))
                                <td width="1%">{{ $ii++ }}&nbsp; @if($supplier->children_scraper_count != 0) <button onclick="showHidden('{{ $supplier->scraper_name }}')" class="btn btn-link"><i class="fa fa-caret-down" style="font-size:24px"></i>  </button> @endif</td>
                            @endif

                            @if (!in_array('Supplier', $dynamicColumnsToShows))
                            <td width="8%"><a href="/supplier/{{$supplier->id}}">{{ ucwords(strtolower($supplier->mainSupplier ? $supplier->mainSupplier->supplier : '')) }}&nbsp; {{ \App\Helpers\ProductHelper::getScraperIcon($supplier->scraper_name) }}</a>
                                @if(substr(strtolower($supplier->mainSupplier ? $supplier->mainSupplier->supplier : ''), 0, 6)  == 'excel_')
                                    &nbsp;<i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                @endif
                                <?php if($hasError){ ?>
                                <i style="color: red;" class="fa fa-exclamation-triangle"></i>
                                <?php } ?>
                                <?php if($hasWarning){ ?>
                                <i style="color: orange;" class="fa fa-exclamation-triangle"></i>
                                <?php } ?>
                            </td>
                            @endif

                            <!-- <td width="10%">{{ !empty($data) ? $data->ip_address : '' }}</td> -->
                            @if (!in_array('Server ID', $dynamicColumnsToShows))
                            <td width="5%">
                                <div class="form-group d-flex">
                                        <select style="width:100% !important;" name="server_id" class="form-control select22 scraper_field_change" data-id="{{$supplier->id}}" data-field="server_id">
                                            <option value="">Select</option>
                                            @foreach($serverIds as $serverId)
                                            <option value="{{$serverId}}" {{$supplier->server_id == $serverId ? 'selected' : ''}}>{{$serverId}}</option>
                                            @endforeach
                                        </select><br>
                                        <button style="padding-right:0px;" type="button" class="btn btn-xs show-history" title="Show History" data-field="server_id" data-id="{{$supplier->id}}"><i class="fa fa-info-circle"></i></button>
                                        @if(isset($getLatestOptimization[$supplier->server_id])) {{ $getLatestOptimization[$supplier->server_id] }} @endif
                                </div>
                            </td>
                            @endif

                            @if (!in_array('Auto Restart', $dynamicColumnsToShows))
                            <td width="4%">
                                <div class="form-group">
                                    {{ html()->select("auto_restart", [0 => "Off", 1 => "On"], $supplier->auto_restart)->class("form-control auto_restart select22")->style("width:100%;") }}
                                </div>
                            </td>
                            @endif

                            @if (!in_array('Run Time', $dynamicColumnsToShows))
                                <td width="5%" style="text-right">
                                    <div class="form-group d-flex">
                                            <select style="width:100% !important;display:inline;" name="scraper_start_time" class="form-control scraper_field_change select22" data-id="{{$supplier->id}}" data-field="scraper_start_time">
                                            <option value="">Select</option>
                                            @for($i=1; $i<=24;$i++)
                                            <option value="{{$i}}" {{$supplier->scraper_start_time == $i ? 'selected' : ''}}>{{$i}} h</option>
                                            @endfor
                                            </select><br>
                                            <button style="padding-right:0px;width:10%;display:inline-block;" type="button" class="btn btn-xs show-history" title="Show History" data-field="scraper_start_time" data-id="{{$supplier->id}}"><i class="fa fa-info-circle"></i></button>
                                    </div>
                                </td>
                            @endif

                            @if (!in_array('Start Scrap', $dynamicColumnsToShows))
                                <td class="expand-row-msgg" data-name="scraper_name" data-id="{{$supplier->id}}" width="5%" data-start-time="@if($supplier->last_started_at){{$supplier->last_started_at }}@endif" data-end-time="@if($supplier->last_completed_at){{$supplier->last_completed_at }}@endif" class="show-scraper-detail">
                                    @if(isset($supplier->scraper_name) && !empty($supplier->scraper_name) &&  isset($lastRunAt[$supplier->scraper_name]))
                                        <span class="show-short-scraper_name-{{$supplier->id}}">{{ Str::limit(str_replace(' ', '<br/>', date('d-M-y H:i', strtotime($lastRunAt[$supplier->scraper_name]))), 8, '..')}}</span>
                                        <span style="word-break:break-all;" class="show-full-scraper_name-{{$supplier->id}} hidden">{!! str_replace(' ', '<br/>', date('d-M-y H:i', strtotime($lastRunAt[$supplier->scraper_name]))) !!}</span>
                                    @endif
                                </td>
                            @endif

                            @if (!in_array('Stock', $dynamicColumnsToShows))
                                <td width="3%">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                            @endif

                            @if (!in_array('URL Count', $dynamicColumnsToShows))
                                <?php $totalCountedUrl += !empty($data) ? $data->total : 0; ?>
                                <td width="3%">{{ !empty($data) ? $data->total : '' }}</td>
                            @endif

                            {{-- <!-- <td width="3%">{{ !empty($data) ? $data->errors : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->warnings : '' }}</td> --> --}}

                            @if (!in_array('YDay New', $dynamicColumnsToShows))
                                <td width="3%">{{ !empty($data) ? $data->total_new_product : '' }}</td>
                            @endif

                            @if (!in_array('URL Count Scrap', $dynamicColumnsToShows))
                                <td width="2%">{{ !empty($data) ? $data->scraper_total_urls : '' }}</td>
                            @endif

                            @if (!in_array('URLs', $dynamicColumnsToShows))
                                <td width="3%">
                                    {{ !empty($data) ? 'Exist : '.$data->scraper_existing_urls : '' }}
                                    {{ !empty($data) ? 'New : '.$data->scraper_new_urls : '' }}
                                </td>
                            @endif
                            {{-- <!-- <td width="3%">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td> -->
                            <!-- <td width="3%">{{ !empty($data) ? $data->scraper_existing_urls : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td> -->
                            <!-- <td width="10%">
                                {{ ($supplier->scraperMadeBy) ? $supplier->scraperMadeBy->name : "N/A" }}
                            </td>
                            <td width="10%">
                                {{ \App\Helpers\DevelopmentHelper::scrapTypeById($supplier->scraper_type) }}
                            </td>
                            <td width="10%">
                                {{ ($supplier->scraperParent) ? $supplier->scraperParent->scraper_name : "N/A" }}
                            </td>
                            <td width="10%">
                                {{ isset(\App\Helpers\StatusHelper::getStatus()[$supplier->next_step_in_product_flow]) ? \App\Helpers\StatusHelper::getStatus()[$supplier->next_step_in_product_flow] : "N/A" }}
                            </td> --> --}}

                            @if (!in_array('Status', $dynamicColumnsToShows))
                                <td width="6%">
                                    <div class="form-group status mb-1" style="display: flex" >
                                        {{ html()->select("status", \App\Scraper::scrapersStatus(), $supplier->status)->class("form-control scrapers_status select22")->style("width:80px;") }}
                                        <button style="padding-right:0px;" type="button" class="btn btn-xs show-history" title="Show History" data-field="status" data-id="{{$supplier->id}}"><i class="fa fa-info-circle"></i></button>
                                    </div>
                                    @php
                                        $hasTask = $supplier->developerTaskNew;
                                    @endphp
                                    {{ ($hasTask) ? "Task-Available" : "No-Task" }}
                                </td>
                            @endif

                            <!-- @if (!in_array('Remarks', $dynamicColumnsToShows))
                                <td width="10%" class="" style="font-size: 12px">
                                    <span class="" data-small-title="<?php echo ($remark) ? substr($remark->remark, 0, 19) : '' ?>" data-full-title="<?php echo ($remark) ? $remark->remark : '' ?>">
                                        <?php
                                            if($remark) {
                                                echo (strlen($remark->remark) > 35) ? substr($remark->remark, 0, 19).".." : $remark->remark;
                                            }
                                         ?>
                                    </span>
                                    <hr style="margin-top: 0px;margin-bottom: 0px;background-color: #808080;height: 1px;">
                                    <span class="" data-small-title="<?php echo ($chatMessage) ? substr($chatMessage->message, 0, 19) : '' ?>" data-full-title="<?php echo ($chatMessage) ? $chatMessage->message : '' ?>">
                                        <?php
                                            if($chatMessage) {
                                                echo (strlen($chatMessage->message) > 35) ? substr($chatMessage->message, 0, 19).".." : $chatMessage->message;
                                            }
                                         ?>
                                     </span>
                                     <?php 
                                        if($chatMessage) {
                                            echo '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="'.$isAdmin.'" data-is_hod_crm="'.$hod.'" data-object="developer_task" data-id="'.$chatMessage->developer_task_id.'" data-load-type="text" data-all="1" title="Load messages"><img src="'.asset('/images/chat.png').'" alt=""></button>';
                                            echo '<hr style="margin-top: 0px;margin-bottom: 0px;background-color: #808080;height: 1px;">';
                                        }
                                     ?>
                                     <hr style="margin-top: 0px;margin-bottom: 0px;background-color: #808080;height: 1px;">
                                        <?php
                                            $logString = null;
                                            $logbtn = null;
                                            if($lastError) {
                                                $allMessage = explode("\n",$lastError->log_messages);
                                                $items = array_slice($allMessage, -5);
                                                $logString =  "SCRAP LOG :".implode("\n", $items);
                                                $logbtn = '<button style="padding:3px;" type="button" class="btn btn-image scraper-log-details" data-scrapper-id="'.$supplier->id.'"><img width="2px;" src="'.asset('/images/remark.png').'"/></button>';
                                            }
                                         ?>
                                     <span class="" data-small-title="<?php echo ($logString) ? substr($logString, 0,10) : '' ?>" data-full-title="<?php echo ($logString) ? $logString : '' ?>">
                                        <?php
                                            echo (strlen($logString) > 3) ? substr($logString, 0,10).".." : $logString;
                                        ?>
                                     </span>
                                </td>
                            @endif -->

                            <?php /*
                            <td width="8%">
                                
                            </td>
                            <td width="8%">
                                <?php
                                    $log = $supplier->latestLog();
                                    if(!empty($latestLog)) {

                                    }
                                ?>
                                <span class="toggle-title-box has-small" data-small-title="<?php echo ($log) ? substr($log->remark, 0, 40) : '' ?>" data-full-title="<?php echo ($log) ? $chatMessage->remark : '' ?>">
                                    <?php
                                        if($log) {
                                            echo (strlen($log->remark) > 35) ? substr($log->remark, 0, 40).".." : $log->remark;
                                        }
                                     ?>
                                 </span>
                            </td>
                            */ ?>

                            @if (!in_array('Full scrap', $dynamicColumnsToShows))
                                <td width="5%">
                                    <div class="form-group">
                                        {{ html()->select("full_scrape", [0 => "No", 1 => "Yes"], $supplier->full_scrape)->class("form-control full_scrape select22")->style("width:100%;") }}
                                    </div>
                                </td>
                            @endif

                            @if (!in_array('Scraper Duration', $dynamicColumnsToShows))
                                <td width="5%">
                                    @php
                                        if(count($supplier->scraperDuration)){
                                            echo $supplier->scraperDuration[0]->duration;
                                            if(isset($supplier->scraperDuration[1])){
                                                echo '<br>' . $supplier->scraperDuration[1]->duration;
                                                if(isset($supplier->scraperDuration[2])){
                                                    echo '<br>' . $supplier->scraperDuration[2]->duration;
                                                }
                                            }
                                        }else{
                                            echo '-';
                                        } 
                                    @endphp    
                                </td>
                            @endif

                            @if (!in_array('Suppiier Inventory', $dynamicColumnsToShows))
                                <td width="5%"> {{$supplier->inventory }} </td>
                            @endif

                            @if (!in_array('Date Last Product Added', $dynamicColumnsToShows))
                            <td width="5%" class="expand-row-msgg" data-name="last_date" data-id="{{$supplier->id}}">
                                <span class="show-short-last_date-{{$supplier->id}}">{{ Str::limit($supplier->last_date !== null ? date('d-M-y H:i',strtotime($supplier->last_date)) : '-', 8, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-last_date-{{$supplier->id}} hidden">{{$supplier->last_date !== null ? date('d-M-y H:i',strtotime($supplier->last_date)) : '-' }}</span>
                            </td>
                            @endif

                            @if (!in_array('Functions', $dynamicColumnsToShows))
                                <td width="4%">
                                     <div style="float:left;">       
                                    <button style="padding:1px;" type="button" class="btn btn-image d-inline toggle-class" data-id="{{ $supplier->id }}" title="Expand more data"><img width="2px;" src="{{asset('/images/forward.png')}}"/></button>

                                    </div>
                                    <p class="d-none duration_display"></p>
                                    
                                </td>
                            @endif
                        </tr>

                            @if (!in_array('Functions', $dynamicColumnsToShows))
                            <tr class="hidden_row_{{ $supplier->id  }} dis-none" data-eleid="{{ $supplier->id }}">
                                <td colspan="4">
                                    <label>Action:</label>
                                    <div class="input-group">
                                        <a style="padding:1px;" class="btn  d-inline btn-image" href="{{ get_server_last_log_file($supplier->scraper_name,$supplier->server_id) }}" id="link" target="-blank" title="View log"><img src="{{asset('/images/view.png')}}" /></a>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="restartScript('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}' )" title="Restart script"><img width="2px;" src="{{asset('/images/resend2.png')}}"/></button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="getRunningStatus('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}' )" title="Check running status"><img width="2px;" src="{{asset('/images/resend.png')}}"/></button>
                                        <a href="<?php echo route("scraper.get.log.list"); ?>?name=<?php echo $supplier->scraper_name ?>&server_id=<?php echo $supplier->server_id ?>" target="__blank">
                                            <button style="padding:1px;" type="button" class="btn btn-image d-inline" title="API call">
                                                <i class="fa fa-bars"></i>
                                            </button>
                                        </a>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-screenshot" title="Get screenshot">
                                            <i class="fa fa-desktop"></i>
                                        </button>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-tasks-remote" title="Task list">
                                            <i class="fa fa-tasks"></i>
                                        </button>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-tasks-killed" title="Scraper killed histories">
                                            <i class="fa fa-history"></i>
                                        </button>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-position-history" title="Histories">
                                            <i class="fa fa-address-card"></i>
                                        </button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline show-history" data-field="update-restart-time" data-id="{{ $supplier->id }}" title="Remark history" ><i class="fa fa-clock-o"></i></button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline get-scraper-server-timing" data-name="{{ $supplier->scraper_name }}" title="Get scraper server timing"><i class="fa fa-info-circle"></i></button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline get-last-errors" data-id="{{ $supplier->id }}" data-name="{{ $supplier->scraper_name }}" title="Last errors">
                                            <i class="fa fa-list-ol"></i>
                                        </button>
                                    <!-- <button style="padding:1px;" type="button" class="btn btn-image d-inline" title="update process" onclick="updateScript('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}', {{$supplier->id}} )"><i class="fa fa-send"></i></button> -->
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline" title="kill process" onclick="killScript('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}')"><i class="fa fa-close"></i></button>
                                        @if($isAdmin)
                                            <div class="flag-scraper-div" style="float:none;display:contents;">
                                                @if ($supplier->flag == 1)
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper" data-flag="0" data-id="{{ $supplier->id }}"><img src="{{asset('/images/flagged.png')}}" /></button>
                                                @else
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper" data-flag="1" data-id="{{ $supplier->id }}"><img src="{{asset('/images/unflagged.png')}}" /></button>
                                                @endif
                                            </div>
                                            <div class="flag-scraper-developer-div" style="float:none;display:contents;">
                                                @if ($supplier->developer_flag == 1)
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper-developer" data-flag="0" data-id="{{ $supplier->id }}"><img src="{{asset('/images/flagged-green.png')}}" /></button>
                                                @else
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper-developer" data-flag="1" data-id="{{ $supplier->id }}"><img src="{{asset('/images/flagged-yellow.png')}}" /></button>
                                                @endif
                                            </div>
                                        @endif

                                        <button style="padding:3px;" type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $supplier->scraper_name }}"><img width="2px;" src="{{asset('/images/remark.png')}}"/ title="Remark History"></button>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Logic:</label>
                                    <div class="input-group d-flex">
                                        <textarea class="form-control scraper_logic" style="width : 80px;" name="scraper_logic"><?php echo $supplier->scraper_logic; ?></textarea>
                                        <button class="btn btn-sm btn-image submit-logic" data-vendorid="1"><img src="{{asset('/images/filled-sent.png')}}"></button>
                                    </div>
                                </td>
                                <td colspan="1">
                                    <label>Start Time:</label>
                                    <div class="input-group">
                                        {{ html()->select("start_time", ['' => "--Time--"] + $timeDropDown, $supplier->scraper_start_time)->class("form-control start_time select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Made By:</label>
                                    <div class="form-group">
                                        @php
                                            
                                            $selectedMadeBy = $supplier->scraperMadeBy;

                                            $madeByArray = ["" => "N/A"];

                                            if($selectedMadeBy){
                                                $madeByArray[$selectedMadeBy->id] = $selectedMadeBy->name;
                                            }

                                        @endphp
                
                                        {{ html()->select("scraper_made_by", $madeByArray, $supplier->scraper_made_by)->class("form-control scraper_made_by globalSelect2")->style("width:100%;")->data('ajax', route('select2.user'))->data('placeholder', 'Made by') }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Type:</label>
                                    <div class="form-group">
                                        {{ html()->select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), $supplier->scraper_type)->class("form-control scraper_type select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Parent Scrapper:</label>
                                    <div class="form-group">
                                        {{ html()->select("parent_supplier_id", [0 => "N/A"] + $allScrapperName, $supplier->parent_supplier_id)->class("form-control parent_supplier_id select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Next Step:</label>
                                    <div class="form-group">
                                        {{ html()->select("next_step_in_product_flow", [0 => "N/A"] + \App\Helpers\StatusHelper::getStatus(), $supplier->next_step_in_product_flow)->class("form-control next_step_in_product_flow select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Server Id:</label>
                                    <div class="form-group d-flex">
                                        {{ html()->text("server_id", $supplier->server_id)->class("form-control server-id-update") }}
                                        <button class="btn btn-sm btn-image server-id-update-btn" data-vendorid="<?php echo $supplier->id; ?>"><img src="{{asset('/images/filled-sent.png')}}" style="cursor: default;"></button>
                                    </div>
                                </td>
                               
                            </tr>
                            @endif
                        @else
                            @php
                                // Set percentage
                                if ( isset($data->errors) && isset($data->total) ) {
                                    $percentage = ($data->errors * 100) / $data->total;
                                } else {
                                    $percentage = 0;
                                }

                                // Show correct background color
                                $hasError =  false;
                                $hasWarning = false;
                                if ( (!empty($data) && $data->running == 0) || $data == null ) {
                                    $hasError =  true;
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'" data-eleid="'.$supplier->id.'">';
                                } elseif ( $percentage > 25 ) {
                                    $hasWarning = true;
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'" data-eleid="'.$supplier->id.'">';
                                } else {
                                    echo '<tr class="history-item-scrap" data-priority = "'.$supplier->scraper_priority.'" data-id="'.$supplier->id.'" data-eleid="'.$supplier->id.'">';
                                }

                                if($status == 1 && !$hasError) {
                                    continue;
                                }

                                $remark = /*\App\ScrapRemark::select('remark')->where('scraper_name',$supplier->scraper_name)->whereNull("scrap_field")->where('user_name','!=','')->orderBy('created_at','desc')->first();*/

                                $remark = $supplier->scrpRemark;

                                $chatMessage = count($supplier->latestMessageNew) > 0 ? $supplier->latestMessageNew[0] : null;


                                $lastError = $supplier->lastErrorFromScrapLogNew;
                                
                            @endphp

                            <td>
                                <input type="checkbox" name="scrap_check" class="scrap_check" value="{{ $supplier->id }}" data-id="{{ $supplier->id }}">
                            </td>

                            <td width="1%">{{ $ii++ }}&nbsp; @if($supplier->children_scraper_count != 0) <button onclick="showHidden('{{ $supplier->scraper_name }}')" class="btn btn-link"><i class="fa fa-caret-down" style="font-size:24px"></i>  </button> @endif</td>

                            <td width="8%"><a href="/supplier/{{$supplier->id}}">{{ ucwords(strtolower($supplier->mainSupplier ? $supplier->mainSupplier->supplier : '')) }}&nbsp; {{ \App\Helpers\ProductHelper::getScraperIcon($supplier->scraper_name) }}</a>
                                @if(substr(strtolower($supplier->mainSupplier ? $supplier->mainSupplier->supplier : ''), 0, 6)  == 'excel_')
                                    &nbsp;<i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                @endif
                                <?php if($hasError){ ?>
                                <i style="color: red;" class="fa fa-exclamation-triangle"></i>
                                <?php } ?>
                                <?php if($hasWarning){ ?>
                                <i style="color: orange;" class="fa fa-exclamation-triangle"></i>
                                <?php } ?>
                            </td>
                            <!-- <td width="10%">{{ !empty($data) ? $data->ip_address : '' }}</td> -->
                            <td width="5%">
                                <div class="form-group d-flex">
                                        <select style="width:100% !important;" name="server_id" class="form-control select22 scraper_field_change" data-id="{{$supplier->id}}" data-field="server_id">
                                            <option value="">Select</option>
                                            @foreach($serverIds as $serverId)
                                            <option value="{{$serverId}}" {{$supplier->server_id == $serverId ? 'selected' : ''}}>{{$serverId}}</option>
                                            @endforeach
                                        </select><br>
                                        <button style="padding-right:0px;" type="button" class="btn btn-xs show-history" title="Show History" data-field="server_id" data-id="{{$supplier->id}}"><i class="fa fa-info-circle"></i></button>
                                        @if(isset($getLatestOptimization[$supplier->server_id])) {{ $getLatestOptimization[$supplier->server_id] }} @endif
                                </div>
                            </td>
                            <td width="4%">
                                <div class="form-group">
                                    {{ html()->select("auto_restart", [0 => "Off", 1 => "On"], $supplier->auto_restart)->class("form-control auto_restart select22")->style("width:100%;") }}
                                </div>
                            </td>

                            <td width="5%" style="text-right">
                                <div class="form-group d-flex">
                                        <select style="width:100% !important;display:inline;" name="scraper_start_time" class="form-control scraper_field_change select22" data-id="{{$supplier->id}}" data-field="scraper_start_time">
                                        <option value="">Select</option>
                                        @for($i=1; $i<=24;$i++)
                                        <option value="{{$i}}" {{$supplier->scraper_start_time == $i ? 'selected' : ''}}>{{$i}} h</option>
                                        @endfor
                                        </select><br>
                                        <button style="padding-right:0px;width:10%;display:inline-block;" type="button" class="btn btn-xs show-history" title="Show History" data-field="scraper_start_time" data-id="{{$supplier->id}}"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </td>
                           <td class="expand-row-msgg" data-name="scraper_name" data-id="{{$supplier->id}}" width="5%" data-start-time="@if($supplier->last_started_at){{$supplier->last_started_at }}@endif" data-end-time="@if($supplier->last_completed_at){{$supplier->last_completed_at }}@endif" class="show-scraper-detail">
                                @if(isset($supplier->scraper_name) && !empty($supplier->scraper_name) &&  isset($lastRunAt[$supplier->scraper_name]))
                                    <span class="show-short-scraper_name-{{$supplier->id}}">{{ Str::limit(str_replace(' ', '<br/>', date('d-M-y H:i', strtotime($lastRunAt[$supplier->scraper_name]))), 8, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-scraper_name-{{$supplier->id}} hidden">{!! str_replace(' ', '<br/>', date('d-M-y H:i', strtotime($lastRunAt[$supplier->scraper_name]))) !!}</span>
                                @endif
                            </td>
                            <td width="3%">{{ !empty($data) ? $data->total - $data->errors : '' }}</td>
                            <?php $totalCountedUrl += !empty($data) ? $data->total : 0; ?>
                            <td width="3%">{{ !empty($data) ? $data->total : '' }}</td>
                            {{-- <!-- <td width="3%">{{ !empty($data) ? $data->errors : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->warnings : '' }}</td> --> --}}
                            <td width="3%">{{ !empty($data) ? $data->total_new_product : '' }}</td>
                            <td width="2%">{{ !empty($data) ? $data->scraper_total_urls : '' }}</td>
                            <td width="3%">
                                {{ !empty($data) ? 'Exist : '.$data->scraper_existing_urls : '' }}
                                {{ !empty($data) ? 'New : '.$data->scraper_new_urls : '' }}
                            </td>
                            {{-- <!-- <td width="3%">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td> -->
                            <!-- <td width="3%">{{ !empty($data) ? $data->scraper_existing_urls : '' }}</td>
                            <td width="3%">{{ !empty($data) ? $data->scraper_new_urls : '' }}</td> -->
                            <!-- <td width="10%">
                                {{ ($supplier->scraperMadeBy) ? $supplier->scraperMadeBy->name : "N/A" }}
                            </td>
                            <td width="10%">
                                {{ \App\Helpers\DevelopmentHelper::scrapTypeById($supplier->scraper_type) }}
                            </td>
                            <td width="10%">
                                {{ ($supplier->scraperParent) ? $supplier->scraperParent->scraper_name : "N/A" }}
                            </td>
                            <td width="10%">
                                {{ isset(\App\Helpers\StatusHelper::getStatus()[$supplier->next_step_in_product_flow]) ? \App\Helpers\StatusHelper::getStatus()[$supplier->next_step_in_product_flow] : "N/A" }}
                            </td> --> --}}
                            <td width="6%">
                                <div class="form-group status mb-1" style="display: flex" >
                                    {{ html()->select("status", \App\Scraper::scrapersStatus(), $supplier->status)->class("form-control scrapers_status select22")->style("width:80px;") }}
                                    <button style="padding-right:0px;" type="button" class="btn btn-xs show-history" title="Show History" data-field="status" data-id="{{$supplier->id}}"><i class="fa fa-info-circle"></i></button>
                                </div>
                                @php
                                    $hasTask = $supplier->developerTaskNew;
                                @endphp
                                {{ ($hasTask) ? "Task-Available" : "No-Task" }}
                            </td>
                            <!-- <td width="10%" class="" style="font-size: 12px">
                                <span class="" data-small-title="<?php echo ($remark) ? substr($remark->remark, 0, 19) : '' ?>" data-full-title="<?php echo ($remark) ? $remark->remark : '' ?>">
                                    <?php
                                        if($remark) {
                                            echo (strlen($remark->remark) > 35) ? substr($remark->remark, 0, 19).".." : $remark->remark;
                                        }
                                     ?>
                                </span>
                                <hr style="margin-top: 0px;margin-bottom: 0px;background-color: #808080;height: 1px;">
                                <span class="" data-small-title="<?php echo ($chatMessage) ? substr($chatMessage->message, 0, 19) : '' ?>" data-full-title="<?php echo ($chatMessage) ? $chatMessage->message : '' ?>">
                                    <?php
                                        if($chatMessage) {
                                            echo (strlen($chatMessage->message) > 35) ? substr($chatMessage->message, 0, 19).".." : $chatMessage->message;
                                        }
                                     ?>
                                 </span>
                                 <?php 
                                    if($chatMessage) {
                                        echo '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="'.$isAdmin.'" data-is_hod_crm="'.$hod.'" data-object="developer_task" data-id="'.$chatMessage->developer_task_id.'" data-load-type="text" data-all="1" title="Load messages"><img src="'.asset('/images/chat.png').'" alt=""></button>';
                                        echo '<hr style="margin-top: 0px;margin-bottom: 0px;background-color: #808080;height: 1px;">';
                                    }
                                 ?>
                                 <hr style="margin-top: 0px;margin-bottom: 0px;background-color: #808080;height: 1px;">
                                    <?php
                                        $logString = null;
                                        $logbtn = null;
                                        if($lastError) {
                                            $allMessage = explode("\n",$lastError->log_messages);
                                            $items = array_slice($allMessage, -5);
                                            $logString =  "SCRAP LOG :".implode("\n", $items);
                                            $logbtn = '<button style="padding:3px;" type="button" class="btn btn-image scraper-log-details" data-scrapper-id="'.$supplier->id.'"><img width="2px;" src="'.asset('/images/remark.png').'"/></button>';
                                        }
                                     ?>
                                 <span class="" data-small-title="<?php echo ($logString) ? substr($logString, 0,10) : '' ?>" data-full-title="<?php echo ($logString) ? $logString : '' ?>">
                                    <?php
                                        echo (strlen($logString) > 3) ? substr($logString, 0,10).".." : $logString;
                                    ?>
                                 </span>
                            </td> -->
                            <?php /*
                            <td width="8%">
                                
                            </td>
                            <td width="8%">
                                <?php
                                    $log = $supplier->latestLog();
                                    if(!empty($latestLog)) {

                                    }
                                ?>
                                <span class="toggle-title-box has-small" data-small-title="<?php echo ($log) ? substr($log->remark, 0, 40) : '' ?>" data-full-title="<?php echo ($log) ? $chatMessage->remark : '' ?>">
                                    <?php
                                        if($log) {
                                            echo (strlen($log->remark) > 35) ? substr($log->remark, 0, 40).".." : $log->remark;
                                        }
                                     ?>
                                 </span>
                            </td>
                            */ ?>
                            <td width="5%">
                                <div class="form-group">
                                    {{ html()->select("full_scrape", [0 => "No", 1 => "Yes"], $supplier->full_scrape)->class("form-control full_scrape select22")->style("width:100%;") }}
                                </div>
                            </td>
                            <td width="5%">
                                @php
                                    if(count($supplier->scraperDuration)){
                                        echo $supplier->scraperDuration[0]->duration;
                                        if(isset($supplier->scraperDuration[1])){
                                            echo '<br>' . $supplier->scraperDuration[1]->duration;
                                            if(isset($supplier->scraperDuration[2])){
                                                echo '<br>' . $supplier->scraperDuration[2]->duration;
                                            }
                                        }
                                    }else{
                                        echo '-';
                                    } 
                                @endphp    
                            </td>
                            <td width="5%"> {{$supplier->inventory }} </td>
                            <td width="5%" class="expand-row-msgg" data-name="last_date" data-id="{{$supplier->id}}">
                                <span class="show-short-last_date-{{$supplier->id}}">{{ Str::limit($supplier->last_date !== null ? date('d-M-y H:i',strtotime($supplier->last_date)) : '-', 8, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-last_date-{{$supplier->id}} hidden">{{$supplier->last_date !== null ? date('d-M-y H:i',strtotime($supplier->last_date)) : '-' }}</span>
                            </td>
                            <td width="4%">
                                 <div style="float:left;">       
                                <button style="padding:1px;" type="button" class="btn btn-image d-inline toggle-class" data-id="{{ $supplier->id }}" title="Expand more data"><img width="2px;" src="{{asset('/images/forward.png')}}"/></button>

                                </div>
                                <p class="d-none duration_display"></p>
                                
                            </td>
                            </tr>
                            <tr class="hidden_row_{{ $supplier->id  }} dis-none" data-eleid="{{ $supplier->id }}">
                                <td colspan="4">
                                    <label>Action:</label>
                                    <div class="input-group">
                                        <a style="padding:1px;" class="btn  d-inline btn-image" href="{{ get_server_last_log_file($supplier->scraper_name,$supplier->server_id) }}" id="link" target="-blank" title="View log"><img src="{{asset('/images/view.png')}}" /></a>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="restartScript('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}' )" title="Restart script"><img width="2px;" src="{{asset('/images/resend2.png')}}"/></button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="getRunningStatus('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}' )" title="Check running status"><img width="2px;" src="{{asset('/images/resend.png')}}"/></button>
                                        <a href="<?php echo route("scraper.get.log.list"); ?>?name=<?php echo $supplier->scraper_name ?>&server_id=<?php echo $supplier->server_id ?>" target="__blank">
                                            <button style="padding:1px;" type="button" class="btn btn-image d-inline" title="API call">
                                                <i class="fa fa-bars"></i>
                                            </button>
                                        </a>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-screenshot" title="Get screenshot">
                                            <i class="fa fa-desktop"></i>
                                        </button>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-tasks-remote" title="Task list">
                                            <i class="fa fa-tasks"></i>
                                        </button>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-tasks-killed" title="Scraper killed histories">
                                            <i class="fa fa-history"></i>
                                        </button>
                                        <button style="padding: 1px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-position-history" title="Histories">
                                            <i class="fa fa-address-card"></i>
                                        </button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline show-history" data-field="update-restart-time" data-id="{{ $supplier->id }}" title="Remark history" ><i class="fa fa-clock-o"></i></button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline get-scraper-server-timing" data-name="{{ $supplier->scraper_name }}" title="Get scraper server timing"><i class="fa fa-info-circle"></i></button>
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline get-last-errors" data-id="{{ $supplier->id }}" data-name="{{ $supplier->scraper_name }}" title="Last errors">
                                            <i class="fa fa-list-ol"></i>
                                        </button>
                                    <!-- <button style="padding:1px;" type="button" class="btn btn-image d-inline" title="update process" onclick="updateScript('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}', {{$supplier->id}} )"><i class="fa fa-send"></i></button> -->
                                        <button style="padding:1px;" type="button" class="btn btn-image d-inline" title="kill process" onclick="killScript('{{ $supplier->scraper_name }}' , '{{ $supplier->server_id }}')"><i class="fa fa-close"></i></button>
                                        @if($isAdmin)
                                            <div class="flag-scraper-div" style="float:none;display:contents;">
                                                @if ($supplier->flag == 1)
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper" data-flag="0" data-id="{{ $supplier->id }}"><img src="{{asset('/images/flagged.png')}}" /></button>
                                                @else
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper" data-flag="1" data-id="{{ $supplier->id }}"><img src="{{asset('/images/unflagged.png')}}" /></button>
                                                @endif
                                            </div>
                                            <div class="flag-scraper-developer-div" style="float:none;display:contents;">
                                                @if ($supplier->developer_flag == 1)
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper-developer" data-flag="0" data-id="{{ $supplier->id }}"><img src="{{asset('/images/flagged-green.png')}}" /></button>
                                                @else
                                                    <button type="button" style="padding:1px;" class="btn btn-image flag-scraper-developer" data-flag="1" data-id="{{ $supplier->id }}"><img src="{{asset('/images/flagged-yellow.png')}}" /></button>
                                                @endif
                                            </div>
                                        @endif

                                        <button style="padding:3px;" type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="{{ $supplier->scraper_name }}"><img width="2px;" src="{{asset('/images/remark.png')}}"/ title="Remark History"></button>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Logic:</label>
                                    <div class="input-group d-flex">
                                        <textarea class="form-control scraper_logic" style="width : 80px;" name="scraper_logic"><?php echo $supplier->scraper_logic; ?></textarea>
                                        <button class="btn btn-sm btn-image submit-logic" data-vendorid="1"><img src="{{asset('/images/filled-sent.png')}}"></button>
                                    </div>
                                </td>
                                <td colspan="1">
                                    <label>Start Time:</label>
                                    <div class="input-group">
                                        {{ html()->select("start_time", ['' => "--Time--"] + $timeDropDown, $supplier->scraper_start_time)->class("form-control start_time select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Made By:</label>
                                    <div class="form-group">
                                        @php
                                            
                                            $selectedMadeBy = $supplier->scraperMadeBy;

                                            $madeByArray = ["" => "N/A"];

                                            if($selectedMadeBy){
                                                $madeByArray[$selectedMadeBy->id] = $selectedMadeBy->name;
                                            }

                                        @endphp
                
                                        {{ html()->select("scraper_made_by", $madeByArray, $supplier->scraper_made_by)->class("form-control scraper_made_by globalSelect2")->style("width:100%;")->data('ajax', route('select2.user'))->data('placeholder', 'Made by') }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Type:</label>
                                    <div class="form-group">
                                        {{ html()->select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), $supplier->scraper_type)->class("form-control scraper_type select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Parent Scrapper:</label>
                                    <div class="form-group">
                                        {{ html()->select("parent_supplier_id", [0 => "N/A"] + $allScrapperName, $supplier->parent_supplier_id)->class("form-control parent_supplier_id select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Next Step:</label>
                                    <div class="form-group">
                                        {{ html()->select("next_step_in_product_flow", [0 => "N/A"] + \App\Helpers\StatusHelper::getStatus(), $supplier->next_step_in_product_flow)->class("form-control next_step_in_product_flow select22")->style("width:100%;") }}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <label>Server Id:</label>
                                    <div class="form-group d-flex">
                                        {{ html()->text("server_id", $supplier->server_id)->class("form-control server-id-update") }}
                                        <button class="btn btn-sm btn-image server-id-update-btn" data-vendorid="<?php echo $supplier->id; ?>"><img src="{{asset('/images/filled-sent.png')}}" style="cursor: default;"></button>
                                    </div>
                                </td>
                               
                            </tr>
                        @endif        
                @endif
                @endforeach
                </tbody>
            </table>

            {{ $activeSuppliers->links() }}
            
        </div>
    </div>
</div>

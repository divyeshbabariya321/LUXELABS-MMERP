<?php
$task_color = $issue->task_color;
?>
@if (Auth::user()->isAdmin())
    @if(!empty($dynamicColumnsToShowDs))
        <tr style="color:grey; background-color:{{$task_color}}">
            @if (!in_array('ID', $dynamicColumnsToShowDs))
                <td style="display:table-cell;vertical-align: baseline;">
                    <div class="d-flex align-items-center">
                        <a style="color: #555;" href="{{ url("development/task-detail/$issue->id") }}">
                            @if($issue->is_resolved==0)
                                <input type="checkbox" name="selected_issue[]"
                                       value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
                            @endif
                        </a>
                        <span class="ml-2"> {{ $issue->id }}</span>
                    </div>


                    <div>
                        <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn">
                            <img width="15px"
                                 src="{{asset('/images/attach.png')}}"
                                 alt=""
                                 style="cursor: default;"></a>
                        <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn">
                            <img width="15px"
                                 src="{{asset('/images/archive.png')}}"
                                 alt=""
                                 style="cursor: default;"></a>
                    </div>
                </td>
            @endif

            @if (!in_array('MODULE', $dynamicColumnsToShowDs))
                <td>
                    <select name="module" class="form-control task-module" data-id="{{$issue->id}}">
                        <option value=''>Select Module..</option>
                        @foreach($modules as $module)

                            @if( isset($issue->module_id) && (int) $issue->module_id == $module->id )
                                <option value="{{$module->id}}" selected>{{$module->name}}</option>
                            @else
                                <option value="{{$module->id}}">{{$module->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
            @endif
            <!-- <td class="p-2">{{ Carbon\Carbon::parse($issue->created_at)->format('d-m H:i') }}</td> -->

            @if (!in_array('Assigned To', $dynamicColumnsToShowDs))
                <td>
                    <select class="form-control assign-user select2" data-id="{{$issue->id}}" name="assigned_to"
                            id="user_{{$issue->id}}">
                        <option value="">Select...</option>
                            <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                        @foreach($users as $id => $name)
                            @if( $assignedId == $id )
                                <option value="{{$id}}" selected>{{ $name }}</option>
                            @else
                                <option value="{{$id}}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
            @endif

            @if (!in_array('Lead', $dynamicColumnsToShowDs))
                <td>
                    <select class="form-control assign-master-user select2" data-id="{{$issue->id}}"
                            name="master_user_id" id="user_{{$issue->id}}">
                        <option value="">Select...</option>
                            <?php $masterUser = isset($issue->masterUser->id) ? $issue->masterUser->id : 0; ?>
                        @foreach($users as $id=>$name)
                            @if( $masterUser == $id )
                                <option value="{{$id}}" selected>{{ $name }}</option>
                            @else
                                <option value="{{$id}}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
            @endif

            @if (!in_array('Communication', $dynamicColumnsToShowDs))
                <td class="communication-td">
                    <!-- class="expand-row" -->
                    <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}"
                           id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" />
                    </br>
                    <button style="display: inline-block;" class="btn btn-sm btn-image send-message-open" type="submit"
                            id="submit_message" data-id="{{$issue->id}}"><img
                                src="{{asset('/images/filled-sent.png')}}" /></button>
                    <button type="button" class="btn btn-xs btn-image load-communication-modal"
                            data-object='developer_task' data-id="{{ $issue->id }}"
                            style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img
                                src="{{asset('/images/chat.png')}}" alt=""></button>
                    <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? '' : '' }} justify-content-between expand-row-msg"
                          style="word-break: break-all;margin-top:6px;" data-id="{{$issue->id}}">
                    <span class="td-mini-container-{{$issue->id}}" style="margin:0px;">
                                    {{  \Illuminate\Support\Str::limit($issue->message, 90, $end='...') }}
                    </span>
                </span>
                    <div class="expand-row-msg" data-id="{{$issue->id}}">
                    <span class="td-full-container-{{$issue->id}} hidden">
                        <span style="word-break:break-word;">{{ $issue->message }}</span>
                        <br>
                        <div class="td-full-container">
                            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }})">Send Attachment</button>
                            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}})">Send Images</button>
                            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;"
                                   multiple />
                         </div>
                    </span>
                    </div>
                </td>
            @endif

            @if (!in_array('Send To', $dynamicColumnsToShowDs))
                <td class="send-to-str">
                        {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:100% !important;display: inline;") }}

                </td>
            @endif

            @if (!in_array('Status', $dynamicColumnsToShowDs))
                <td>
                    @if($issue->is_resolved)
                        <strong>Done</strong>
                    @else
                            {{ html()->select("task_status", $statusList, $issue->status)->class("form-control resolve-issue")->attribute('onchange', "resolveIssue(this," . $issue->id . ")") }}
                    @endif
                </td>
            @endif

            @if (!in_array('Estimated Time', $dynamicColumnsToShowDs))
                <td class="p-2">
                    <div style="margin-bottom:10px;width: 100%;">
                        <div class="form-group">
                            <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                                   value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                            <div style="max-width: 30px;">
                                <button class="btn btn-sm btn-image send-approximate-lead 33333"
                                        title="Send approximate"
                                        onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                        data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                                </button>
                            </div>
                        </div>
                    </div>

                    @php
                        $time_history = $issue->dthWithMinuteEstimate;
                    @endphp

                    @if($time_history)
                        @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                            <button data-task="{{$time_history->developer_task_id}}" data-id="{{$time_history->id}}"
                                    title="approve" data-type="DEVTASK"
                                    class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </button>
                        @endif

                        @if($issue->task_start==1)
                            <input type="hidden" value="{{$issue->m_start_date}}" class="m_start_date_"
                                   id="m_start_date_{{$issue->id}}" data-id="{{$issue->id}}"
                                   data-value="{{$issue->m_start_date}}">
                            <div id="time-counter_{{$issue->id}}"></div>
                        @endif
                    @endif
                </td>
            @endif

            @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowDs))
                <td class="p-2">
                    <div class="form-group">
                        <div class='input-group date cls-start-due-date'>
                            <input type="text" class="form-control" name="start_dates{{$issue->id}}"
                                   value="{{$issue->start_date}}" autocomplete="off" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                    onclick="funDevTaskInformationUpdatesTime('start_date',{{$issue->id}})"
                                    data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>
                    @if(!empty($issue->start_date) && $issue->start_date!='0000-00-00 00:00:00')
                        {{$issue->start_date}}
                    @endif

                    <div class="form-group">
                        <div class='input-group date cls-start-due-date'>
                            <input type="text" class="form-control" name="estimate_date{{$issue->id}}"
                                   value="{{$issue->estimate_date}}" autocomplete="off" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                    onclick="funDevTaskInformationUpdatesTime('estimate_date',{{$issue->id}})"
                                    data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>
                    @if(!empty($issue->estimate_date) && $issue->estimate_date!='0000-00-00 00:00:00')
                        {{$issue->estimate_date}}
                    @endif
                </td>
            @endif

            @if (!in_array('Shortcuts', $dynamicColumnsToShowDs))
                <td id="shortcutsIds">
                    @include('development.partials.shortcutsds')
                </td>
            @endif

            @if (!in_array('Actions', $dynamicColumnsToShowDs))
                <td>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="Showactionbtn('{{$issue->id}}')"><i
                                class="fa fa-arrow-down"></i></button>
                </td>
            @endif
        </tr>
        @if (!in_array('Actions', $dynamicColumnsToShowDs))
            <tr class="action-btn-tr-{{$issue->id}} d-none">
                <td class="font-weight-bold">Action</td>
                <td colspan="15">
                    <button class="btn btn-image set-remark p-1" data-task_id="{{ $issue->id }}"
                            data-task_type="Quick-dev-task"><i class="fa fa-comment" aria-hidden="true"></i></button>
                    @if ($issue->is_flagged == 1)
                        <button type="button" class="btn btn-image flag-task p-1" data-id="{{ $issue->id }}"><img
                                    src="{{asset('images/flagged.png')}}" /></button>
                    @else
                        <button type="button" class="btn btn-image flag-task p-1" data-id="{{ $issue->id }}"><img
                                    src="{{asset('images/unflagged.png')}}" /></button>
                    @endif
                    <button type="button" class="btn btn-xs show-status-history p-1" title="Show Status History"
                            data-id="{{$issue->id}}">
                        <i class="fa fa-info-circle"></i>
                    </button>
                    <button class="btn btn-xs mt-2 add-document-permission" data-task_id="{{$issue->id}}"
                            data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                        <i class="fa fa-key" aria-hidden="true"></i>
                    </button>

                    <button class="btn btn-sm btn-image add-scrapper" data-task_id="{{$issue->id}}"
                            data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>

                    @include('development.partials.show-scrapper-logs-btn')

                    <button style="padding-left: 0;padding-left:3px;" type="button"
                            class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $issue->id }}"
                            title="Show scrapper" data-id="{{ $issue->id }}" data-category="{{ $issue->id }}"><i
                                class="fa fa-list"></i></button>
                </td>
            </tr>
        @endif
    @else
        <tr style="color:grey; background-color:{{$task_color}}">
            <td style="display:table-cell;vertical-align: baseline;">

                <div class="d-flex align-items-center">
                    <a style="color: #555;" href="{{ url("development/task-detail/$issue->id") }}">
                        @if($issue->is_resolved==0)
                            <input type="checkbox" name="selected_issue[]"
                                   value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
                        @endif
                    </a>
                    <span class="ml-2"> {{ $issue->id }}</span>
                </div>


                <div>
                    <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn">
                        <img width="15px"
                             src="{{asset('/images/attach.png')}}"
                             alt=""
                             style="cursor: default;"></a>
                    <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn">
                        <img width="15px"
                             src="{{asset('/images/archive.png')}}"
                             alt=""
                             style="cursor: default;"></a>
                </div>
            </td>
            <td>
                <select name="module" class="form-control task-module" data-id="{{$issue->id}}">
                    <option value=''>Select Module..</option>
                    @foreach($modules as $module)

                        @if( isset($issue->module_id) && (int) $issue->module_id == $module->id )
                            <option value="{{$module->id}}" selected>{{$module->name}}</option>
                        @else
                            <option value="{{$module->id}}">{{$module->name}}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td>
                <select class="form-control assign-user select2" data-id="{{$issue->id}}" name="assigned_to"
                        id="user_{{$issue->id}}">
                    <option value="">Select...</option>
                        <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                    @foreach($users as $id => $name)
                        @if( $assignedId == $id )
                            <option value="{{$id}}" selected>{{ $name }}</option>
                        @else
                            <option value="{{$id}}">{{ $name }}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td>
                <select class="form-control assign-master-user select2" data-id="{{$issue->id}}" name="master_user_id"
                        id="user_{{$issue->id}}">
                    <option value="">Select...</option>
                    @php($masterUser = isset($issue->masterUser->id) ? $issue->masterUser->id : 0)
                    @foreach($users as $id=>$name)
                        @if( $masterUser == $id )
                            <option value="{{$id}}" selected>{{ $name }}</option>
                        @else
                            <option value="{{$id}}">{{ $name }}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td class="communication-td">
                <!-- class="expand-row" -->
                <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}"
                       id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" />
                </br>
                <button style="display: inline-block;" class="btn btn-sm btn-image send-message-open" type="submit"
                        id="submit_message" data-id="{{$issue->id}}"><img src="{{asset('/images/filled-sent.png')}}" />
                </button>
                <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task'
                        data-id="{{ $issue->id }}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img
                            src="{{asset('/images/chat.png')}}" alt=""></button>
                <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? '' : '' }} justify-content-between expand-row-msg"
                      style="word-break: break-all;margin-top:6px;" data-id="{{$issue->id}}">
                    <span class="td-mini-container-{{$issue->id}}" style="margin:0px;">
                                    {{  \Illuminate\Support\Str::limit($issue->message, 90, $end='...') }}
                    </span>
                </span>
                <div class="expand-row-msg" data-id="{{$issue->id}}">
                    <span class="td-full-container-{{$issue->id}} hidden">
                        <span style="word-break:break-word;">{{ $issue->message }}</span>
                        <br>
                        <div class="td-full-container">
                            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }})">Send Attachment</button>
                            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}})">Send Images</button>
                            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;"
                                   multiple />
                         </div>
                    </span>
                </div>
            </td>
            <td class="send-to-str">
                    {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:100% !important;display: inline;") }}

            </td>


            <td>
                @if($issue->is_resolved)
                    <strong>Done</strong>
                @else
                        {{ html()->select("task_status", $statusList, $issue->status)->class("form-control resolve-issue")->attribute('onchange', "resolveIssue(this," . $issue->id . ")") }}
                @endif
            </td>
            <td class="p-2">
                <div style="margin-bottom:10px;width: 100%;">
                    <div class="form-group">
                        <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                               value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-approximate-lead 11111" title="Send approximate"
                                    onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                    data-taskid="{{ $issue->id }}">
                                <img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>
                </div>

                @if(!$issue->dthWithMinuteEstimate)
                    @if (isset($issue->dthWithMinuteEstimate->is_approved) && $issue->dthWithMinuteEstimate->is_approved != 1)
                        <button data-task="{{$issue->dthWithMinuteEstimate->developer_task_id}}"
                                data-id="{{$issue->dthWithMinuteEstimate->id}}"
                                title="approve" data-type="DEVTASK"
                                class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if($issue->task_start==1)
                        <input type="hidden" value="{{$issue->m_start_date}}" class="m_start_date_"
                               id="m_start_date_{{$issue->id}}" data-id="{{$issue->id}}"
                               data-value="{{$issue->m_start_date}}">
                        <div id="time-counter_{{$issue->id}}"></div>
                    @endif
                @endif
            </td>
            <td class="p-2">
                <div class="form-group">
                    <div class='input-group date cls-start-due-date'>
                        <input type="text" class="form-control" name="start_dates{{$issue->id}}"
                               value="{{$issue->start_date}}" autocomplete="off" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                    <div style="max-width: 30px;">
                        <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                onclick="funDevTaskInformationUpdatesTime('start_date',{{$issue->id}})"
                                data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                        </button>
                    </div>
                </div>
                @if(!empty($issue->start_date) && $issue->start_date!='0000-00-00 00:00:00')
                    {{$issue->start_date}}
                @endif

                <div class="form-group">
                    <div class='input-group date cls-start-due-date'>
                        <input type="text" class="form-control" name="estimate_date{{$issue->id}}"
                               value="{{$issue->estimate_date}}" autocomplete="off" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                    <div style="max-width: 30px;">
                        <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                                onclick="funDevTaskInformationUpdatesTime('estimate_date',{{$issue->id}})"
                                data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                        </button>
                    </div>
                </div>
                @if(!empty($issue->estimate_date) && $issue->estimate_date!='0000-00-00 00:00:00')
                    {{$issue->estimate_date}}
                @endif
            </td>
            <td id="shortcutsIds">
                @include('development.partials.shortcutsds')
            </td>
            <td>
                <button type="button" class="btn btn-secondary btn-sm" onclick="Showactionbtn('{{$issue->id}}')"><i
                            class="fa fa-arrow-down"></i></button>
            </td>
        </tr>
        <tr class="action-btn-tr-{{$issue->id}} d-none">
            <td class="font-weight-bold">Action</td>
            <td colspan="15">
                <button class="btn btn-image set-remark p-1" data-task_id="{{ $issue->id }}"
                        data-task_type="Quick-dev-task"><i class="fa fa-comment" aria-hidden="true"></i></button>
                @if ($issue->is_flagged == 1)
                    <button type="button" class="btn btn-image flag-task p-1" data-id="{{ $issue->id }}"><img
                                src="{{asset('images/flagged.png')}}" /></button>
                @else
                    <button type="button" class="btn btn-image flag-task p-1" data-id="{{ $issue->id }}"><img
                                src="{{asset('images/unflagged.png')}}" /></button>
                @endif
                <button type="button" class="btn btn-xs show-status-history p-1" title="Show Status History"
                        data-id="{{$issue->id}}">
                    <i class="fa fa-info-circle"></i>
                </button>
                <button class="btn btn-xs mt-2 add-document-permission" data-task_id="{{$issue->id}}"
                        data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                    <i class="fa fa-key" aria-hidden="true"></i>
                </button>

                <button class="btn btn-sm btn-image add-scrapper" data-task_id="{{$issue->id}}" data-task_type="DEVTASK"
                        data-assigned_to="{{$issue->assigned_to}}">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>

                @include('development.partials.show-scrapper-logs-btn')

                <button style="padding-left: 0;padding-left:3px;" type="button"
                        class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $issue->id }}"
                        title="Show scrapper" data-id="{{ $issue->id }}" data-category="{{ $issue->id }}"><i
                            class="fa fa-list"></i></button>
            </td>
        </tr>
    @endif
@else
    <tr style="color:grey; background-color:{{$task_color}}">
        <td style="display:table-cell;vertical-align: baseline;">

            <div class="d-flex align-items-center">
                <a style="color: #555;" href="{{ url("development/task-detail/$issue->id") }}">
                    @if($issue->is_resolved==0)
                        <input type="checkbox" name="selected_issue[]"
                               value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
                    @endif
                </a>
                <span class="ml-2"> {{ $issue->id }}</span>
            </div>


            <div>
                <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn">
                    <img width="15px"
                         src="{{asset('/images/attach.png')}}"
                         alt=""
                         style="cursor: default;"></a>
                <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn">
                    <img width="15px"
                         src="{{asset('/images/archive.png')}}"
                         alt=""
                         style="cursor: default;"></a>
            </div>
        </td>
        <td>
            <select name="module" class="form-control task-module" data-id="{{$issue->id}}">
                <option value=''>Select Module..</option>
                @foreach($modules as $module)
                    @if( isset($issue->module_id) && (int) $issue->module_id == $module->id )
                        <option value="{{$module->id}}" selected>{{$module->name}}</option>
                    @else
                        <option value="{{$module->id}}">{{$module->name}}</option>
                    @endif
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control assign-user select2" data-id="{{$issue->id}}" name="assigned_to"
                    id="user_{{$issue->id}}">
                <option value="">Select...</option>
                    <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                @foreach($users as $id => $name)
                    @if( $assignedId == $id )
                        <option value="{{$id}}" selected>{{ $name }}</option>
                    @else
                        <option value="{{$id}}">{{ $name }}</option>
                    @endif
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control assign-master-user select2" data-id="{{$issue->id}}" name="master_user_id"
                    id="user_{{$issue->id}}">
                <option value="">Select...</option>
                    <?php $masterUser = isset($issue->masterUser->id) ? $issue->masterUser->id : 0; ?>
                @foreach($users as $id=>$name)
                    @if( $masterUser == $id )
                        <option value="{{$id}}" selected>{{ $name }}</option>
                    @else
                        <option value="{{$id}}">{{ $name }}</option>
                    @endif
                @endforeach
            </select>
        </td>
        <td class="communication-td">
            <!-- class="expand-row" -->
            <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}"
                   id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" />
            </br>
            <button style="display: inline-block;" class="btn btn-sm btn-image send-message-open" type="submit"
                    id="submit_message" data-id="{{$issue->id}}"><img src="{{asset('/images/filled-sent.png')}}" />
            </button>
            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task'
                    data-id="{{ $issue->id }}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img
                        src="{{asset('/images/chat.png')}}" alt=""></button>
            <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? '' : '' }} justify-content-between expand-row-msg"
                  style="word-break: break-all;margin-top:6px;" data-id="{{$issue->id}}">
                <span class="td-mini-container-{{$issue->id}}" style="margin:0px;">
                                {{  \Illuminate\Support\Str::limit($issue->message, 90, $end='...') }}
                </span>
            </span>
            <div class="expand-row-msg" data-id="{{$issue->id}}">
                <span class="td-full-container-{{$issue->id}} hidden">
                    <span style="word-break:break-word;">{{ $issue->message }}</span>
                    <br>
                    <div class="td-full-container">
                        <button class="btn btn-secondary btn-xs"
                                onclick="sendImage({{ $issue->id }})">Send Attachment</button>
                        <button class="btn btn-secondary btn-xs"
                                onclick="sendUploadImage({{$issue->id}})">Send Images</button>
                        <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;"
                               multiple />
                     </div>
                </span>
            </div>
        </td>
        <td class="send-to-str">
                {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:100% !important;display: inline;") }}

        </td>


        <td>
            @if($issue->is_resolved)
                <strong>Done</strong>
            @else
                    {{ html()->select("task_status", $statusList, $issue->status)->class("form-control resolve-issue")->attribute('onchange', "resolveIssue(this," . $issue->id . ")") }}
            @endif
        </td>
        <td class="p-2">
            <div style="margin-bottom:10px;width: 100%;">
                <div class="form-group">
                    <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                           value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                    <div style="max-width: 30px;">
                        <button class="btn btn-sm btn-image send-approximate-lead 22222" title="Send approximate"
                                onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                        </button>
                    </div>
                </div>
            </div>
        </td>
        <td class="p-2">
            <div class="form-group">
                <div class='input-group date cls-start-due-date'>
                    <input type="text" class="form-control" name="start_dates{{$issue->id}}"
                           value="{{$issue->start_date}}" autocomplete="off" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div style="max-width: 30px;">
                    <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                            onclick="funDevTaskInformationUpdatesTime('start_date',{{$issue->id}})"
                            data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                </div>
            </div>
            @if(!empty($issue->start_date) && $issue->start_date!='0000-00-00 00:00:00')
                {{$issue->start_date}}
            @endif

            <div class="form-group">
                <div class='input-group date cls-start-due-date'>
                    <input type="text" class="form-control" name="estimate_date{{$issue->id}}"
                           value="{{$issue->estimate_date}}" autocomplete="off" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div style="max-width: 30px;">
                    <button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate"
                            onclick="funDevTaskInformationUpdatesTime('estimate_date',{{$issue->id}})"
                            data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                </div>
            </div>
            @if(!empty($issue->estimate_date) && $issue->estimate_date!='0000-00-00 00:00:00')
                {{$issue->estimate_date}}
            @endif
        </td>
        <td>
            <button type="button" class="btn btn-secondary btn-sm" onclick="Showactionbtn('{{$issue->id}}')"><i
                        class="fa fa-arrow-down"></i></button>
        </td>
    </tr>
    <tr class="action-btn-tr-{{$issue->id}} d-none">
        <td class="font-weight-bold">Action</td>
        <td colspan="15">
            <button class="btn btn-image set-remark p-1" data-task_id="{{ $issue->id }}"
                    data-task_type="Quick-dev-task"><i class="fa fa-comment" aria-hidden="true"></i></button>
            @if ($issue->is_flagged == 1)
                <button type="button" class="btn btn-image flag-task p-1" data-id="{{ $issue->id }}"><img
                            src="{{asset('images/flagged.png')}}" /></button>
            @else
                <button type="button" class="btn btn-image flag-task p-1" data-id="{{ $issue->id }}"><img
                            src="{{asset('images/unflagged.png')}}" /></button>
            @endif
            <button type="button" class="btn btn-xs show-status-history p-1" title="Show Status History"
                    data-id="{{$issue->id}}">
                <i class="fa fa-info-circle"></i>
            </button>
            <button class="btn btn-xs mt-2 add-document-permission" data-task_id="{{$issue->id}}"
                    data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                <i class="fa fa-key" aria-hidden="true"></i>
            </button>

            <button class="btn btn-sm btn-image add-scrapper" data-task_id="{{$issue->id}}" data-task_type="DEVTASK"
                    data-assigned_to="{{$issue->assigned_to}}">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>

            @include('development.partials.show-scrapper-logs-btn')

            <button style="padding-left: 0;padding-left:3px;" type="button"
                    class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $issue->id }}"
                    title="Show scrapper" data-id="{{ $issue->id }}" data-category="{{ $issue->id }}"><i
                        class="fa fa-list"></i></button>
        </td>
    </tr>
@endif
<script>

  $(document).on("click", ".flag-task", function() {
    var task_id = $(this).data("id");
    var thiss = $(this);

    $.ajax({
      type: "POST",
      url: "{{ route('task.flag') }}",
      data: {
        _token: "{{ csrf_token() }}",
        task_id: task_id,
        task_type: "DEVTASK"
      },
      beforeSend: function() {
        $(thiss).text("Flagging...");
      }
    }).done(function(response) {
      if (response.is_flagged == 1) {
        //
        $(thiss).html("<img src=\"/images/flagged.png\" />");
      } else {
        $(thiss).html("<img src=\"/images/unflagged.png\" />");
      }

      // $(thiss).remove();
    }).fail(function(response) {
      $(thiss).html("<img src=\"/images/unflagged.png\" />");

      alert("Could not flag task!");

      console.log(response);
    });
  });

  function Showactionbtn(id) {
    $(".action-btn-tr-" + id).toggleClass("d-none");
  }
</script>

@if (Auth::user()->isAdmin())
    @if(!empty($dynamicColumnsToShowDl))
        <tr style="color:grey;">
            @if (!in_array('ID', $dynamicColumnsToShowDl))
                <td>
                    {{ $issue->id }}</br>
                    @if($issue->is_resolved==0)
                        <input type="checkbox" name="selected_issue[]"
                               value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
                    @endif
                    <input type="checkbox" title="Select task" class="select_task_checkbox" name="task"
                           data-id="{{ $issue->id }}" value="">
                </td>
            @endif

            @if (!in_array('Date', $dynamicColumnsToShowDl))
                <td class="p-2">{{ Carbon\Carbon::parse($issue->created_at)->format('d-m H:i') }}</td>
            @endif

            @if (!in_array('Subject', $dynamicColumnsToShowDl))
                <td class="task-subject-container">
                    <p class="hidden">{{ $issue->subject ?? 'N/A' }}</p>
                    <p>{{ $issue->subject ? \Illuminate\Support\Str::limit($issue->subject, 20, $end='...') : 'N/A' }}</p>
                </td>
            @endif

            @if (!in_array('Communication', $dynamicColumnsToShowDl))
                <td class="expand-row">
                    <textarea class="form-control send-message-textbox addToAutoComplete" data-id="{{$issue->id}}"
                              id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}"
                              style="margin-top:5px;margin-bottom:5px" rows="3" cols="20"></textarea>

                    <div class="col-12 d-flex justify-content-between align-items-center m-0 p-0 mt-2 mb-2">
                        <input class="" name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                            {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:30% !important;display: inline;") }}
                    </div>

                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open"
                                type="submit" id="submit_message_{{$issue->id}}" data-id="{{$issue->id}}"><img
                                    src="/images/filled-sent.png" /></button>

                        <button type="button" class="btn btn-xs btn-image load-communication-modal"
                                data-object='developer_task' data-id="{{ $issue->id }}"
                                style="margin-top:-0%;margin-left: -3%;" title="Load messages"
                                data-is_admin="{{ Auth::user()->hasRole('Admin') }}"><img src="/images/chat.png" alt="">
                        </button>

                        <button class="btn btn-image upload-task-files-button btn-xs" type="button" title="Uploaded Files" data-id="{{ $issue->id }}">
                            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                        </button>

                        <input type="hidden" name="is_audio" id="is_audio_{{$issue->id}}" class="is_audio" value="0">
                        <button type="button" class="btn btn-xs btn-image btn-trigger-rvn-modal" data-id="{{$issue->id}}"
                                data-tid="{{$issue->id}}" title="Record & Send Voice Message" style="margin-top: 2%;"><img
                                    src="{{asset('images/record-voice-message.png')}}" alt=""></button>

                        <a class="btn btn-xs btn-image" title="View Drive Files"
                           onclick="fetchGoogleDriveFileData('{{$issue->id}}')" style="margin-top:-0%;margin-left: -3%;">
                            <img width="2px;" src="/images/google-drive.png" />
                        </a>
                    </div>
                    <div class="td-full-container hidden">
                        <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send
                            Attachment
                        </button>
                        <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send
                            Images
                        </button>
                        <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;"
                               multiple />
                    </div>

                    <div class="col-12 d-flex pb-5 pt-2 justify-content-start pl-1 task-container-row">
                        @if($issue->is_audio)
                            <audio controls="" src="{{\App\Helpers::getAudioUrl($issue->message)}}"></audio>
                        @else
                            <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}"
                                  style="word-break: break-all; text-align:left; ">{{ \Illuminate\Support\Str::limit($issue->task, 50, $end='...') }}</span>

                            <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }} hidden"
                                  style="word-break: break-all; text-align:left; ">#{{ $issue->id }}. {{ $issue->subject }}. {{ $issue->task }}</span>
                        @endif
                    </div>
                </td>
            @endif

            @if (!in_array('Est Completion Time', $dynamicColumnsToShowDl))
                <td data-id="{{ $issue->id }}">
                    <div class="form-group">
                        @if ($issue->status == 'Approved')
                            <span>{{ $issue->status }}</span>
                            : {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                        @elseif ($issue->estimate_minutes)
                            <span style="color:#337ab7"><strong>Unapproved</strong></span>
                            : {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                        @else
                            <span style="color:#337ab7"><strong>Unapproved</strong> </span>
                        @endif
                    </div>

                    @if(auth()->user()->id == $issue->assigned_to)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{$issue->id}}" data-type="developer">Meeting time
                        </button>
                    @elseif(auth()->user()->id == $issue->master_user_id)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{$issue->id}}" data-type="lead">Meeting time
                        </button>
                    @elseif(auth()->user()->id == $issue->tester_id)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{$issue->id}}" data-type="tester">Meeting time
                        </button>
                    @elseif(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{$issue->id}}" data-type="admin">Meeting time
                        </button>
                    @endif

                </td>
            @endif

            @if (!in_array('Est Completion Date', $dynamicColumnsToShowDl))
                <td data-id="{{ $issue->id }}">
                    <div class="form-group">
                        <div class='input-group'>
                            <span>{{ $issue->developerTaskHistory?->new_value ?: "--" }}</span>
                        </div>
                    </div>
                </td>
            @endif

            @if (!in_array('Tracked Time', $dynamicColumnsToShowDl))
                <td>
                    @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
                        Developer : {{ formatDuration($issue->timeSpent->tracked) }}

                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-tracked-history" title="Show tracked time History"
                                data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i>
                        </button>
                    @endif

                    @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
                        Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-tracked-history" title="Show tracked time History"
                                data-id="{{$issue->id}}" data-type="lead"><i class="fa fa-info-circle"></i></button>
                    @endif

                    @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
                        Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-tracked-history" title="Show tracked time History"
                                data-id="{{$issue->id}}" data-type="tester"><i class="fa fa-info-circle"></i></button>
                    @endif


                    @if(!$issue->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->assigned_to))
                        <button type="button" class="btn btn-xs create-hubstaff-task"
                                title="Create Hubstaff task for User" data-id="{{$issue->id}}" data-type="developer">
                            Create D Task
                        </button>
                    @endif
                    @if(!$issue->lead_hubstaff_task_id && $issue->master_user_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->master_user_id))
                        <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                                title="Create Hubstaff task for Master user" data-id="{{$issue->id}}" data-type="lead">
                            Create L Task
                        </button>
                    @endif

                    @if(!$issue->tester_hubstaff_task_id && $issue->tester_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->tester_id))
                        <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                                title="Create Hubstaff task for Tester" data-id="{{$issue->id}}" data-type="tester">
                            Create T Task
                        </button>
                    @endif
                </td>
            @endif

            @if (!in_array('Developers', $dynamicColumnsToShowDl))
                <td>
                    <div class="form-group">
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
                    </div>

                    <div class="expand-column-lead hidden" data-id="{{ $issue->id }}">
                        <div class="form-group mt-5">
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
                        </div>
                        <div class="form-group mt-5">
                            <select class="form-control assign-team-lead select2" data-id="{{$issue->id}}"
                                    name="team_lead_id" id="user_{{$issue->id}}">
                                <option value="">Select...</option>
                                @foreach($users as $id=>$name)
                                    <option value="{{$id}}" {{$issue->team_lead_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-5">
                            <select class="form-control assign-tester select2" data-id="{{$issue->id}}" name="tester_id"
                                    id="user_{{$issue->id}}">
                                <option value="">Select...</option>
                                @foreach($users as $id=>$name)
                                    <option value="{{$id}}" {{$issue->tester_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history"
                                title="Show History" data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
                        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs pull-request-history"
                                title="Pull Request History" data-id="{{$issue->id}}"><i class="fa fa-history"></i></button>
                    </div>
                </td>
            @endif

            @if (!in_array('Status', $dynamicColumnsToShowDl))
                <td>
                    <div>
                        @if($issue->is_resolved)
                            <strong>Done</strong>
                        @else
                                {{ html()->select("task_status", $statusList, $issue->status)->class("form-control resolve-issue")->attribute('onchange', "resolveIssue(this," . $issue->id . ")") }}
                        @endif
                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-status-history" title="Show Status History"
                                data-id="{{$issue->id}}">
                            <i class="fa fa-info-circle"></i>
                        </button>
                    </div>
                </td>
            @endif

            @if (!in_array('Cost', $dynamicColumnsToShowDl))
                <td>
                    {{ $issue->cost ?: 0 }}
                </td>
            @endif

            @if (!in_array('Estimated Time', $dynamicColumnsToShowDl))
                <td class="p-2">
                    <div style="margin-bottom:10px;width: 100%;">
                        <div class="d-flex align-items-center">
                            <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                                   value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                            <div style="max-width: 30px;">
                                <button class="btn btn-sm btn-image send-approximate-lead 88888"
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
                    @if(!empty($time_history))
                        @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                            <button data-task="{{$time_history->developer_task_id}}" data-id="{{$time_history->id}}"
                                    title="approve" data-type="DEVTASK"
                                    class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </button>
                        @endif

                        @if($issue->task_start!=1)
                            <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                                    class="btn btn-sm startDirectTask" data-task-type="1">
                                <i class="fa fa-play" aria-hidden="true"></i>
                            </button>
                        @else
                            <input type="hidden" value="{{$issue->m_start_date}}" class="m_start_date_"
                                   id="m_start_date_{{$issue->id}}" data-id="{{$issue->id}}"
                                   data-value="{{$issue->m_start_date}}">
                            <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                                    class="btn btn-sm startDirectTask" data-task-type="2">
                                <i class="fa fa-stop" aria-hidden="true"></i>
                            </button>
                            <div id="time-counter_{{$issue->id}}"></div>
                        @endif

                        <button type="button" class="btn btn-xs show-timer-history" title="Show timer History"
                                data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
                    @endif
                </td>
            @endif

            @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowDl))
                <td class="p-2">
                    <div class="d-flex align-items-center mb-3">
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

                    <div class="d-flex align-items-center">
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

            @if (!in_array('Shortcuts', $dynamicColumnsToShowDl))
                <td id="shortcutsIds">@include('development.partials.shortcutsdl')</td>
            @endif

            @if (!in_array('Actions', $dynamicColumnsToShowDl))
                <td>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="Showactionbtn('{{$issue->id}}')"><i
                                class="fa fa-arrow-down"></i></button>
                </td>
            @endif
        </tr>

        @if (!in_array('Actions', $dynamicColumnsToShowDl))
            <tr class="action-btn-tr-{{$issue->id}} d-none">
                <td class="font-weight-bold">Action</td>
                <td colspan="15">
                    <button class="btn btn-image set-remark" data-task_id="{{ $issue->id }}" data-task_type="Dev-task">
                        <i class="fa fa-comment" aria-hidden="true"></i></button>

                    <a title="Task Information: Update" class="m-0 btn btn-sm btn-image" href="javascript:void(0);"
                       onclick="funTaskInformationModal(this, '{{ $issue->id }}')"><i class="fa fa-info-circle"
                                                                                      aria-hidden="true"></i></a>

                    <button class="btn btn-sm btn-image create-task-document m-0" title="Create document"
                            data-id="{{$issue->id}}">
                        <i class="fa fa-file-text" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm btn-image show-created-task-document m-0" title="Show created document"
                            data-id="{{$issue->id}}">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm btn-image add-document-permission m-0" data-task_id="{{$issue->id}}"
                            data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                        <i class="fa fa-key" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm btn-image add-scrapper m-0" data-task_id="{{$issue->id}}"
                            data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>

                    @include('development.partials.show-scrapper-logs-btn')

                    <button style="padding-left: 0;padding-left:3px;" type="button"
                            class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $issue->id }} m-0"
                            title="Show scrapper" data-id="{{ $issue->id }}" data-category="{{ $issue->id }}"><i
                                class="fa fa-list"></i></button>

                    <button class="btn btn-image expand-row-btn-lead m-0" data-id="{{ $issue->id }}"><img src="/images/forward.png"></button>
                </td>
            </tr>
        @endif
    @else
        <tr style="color:grey;">
            <td>
                {{ $issue->id }}</br>
                @if($issue->is_resolved==0)
                    <input type="checkbox" name="selected_issue[]"
                           value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
                @endif
                <input type="checkbox" title="Select task" class="select_task_checkbox" name="task"
                       data-id="{{ $issue->id }}" value="">
            </td>
            <td class="p-2">{{ Carbon\Carbon::parse($issue->created_at)->format('d-m H:i') }}</td>
            <td class="task-subject-container">
                <p class="hidden">{{ $issue->subject ?? 'N/A' }}</p>
                <p>{{ $issue->subject ? \Illuminate\Support\Str::limit($issue->subject, 20, $end='...') : 'N/A' }}</p>
            </td>
            <td class="expand-row">
                <textarea class="form-control send-message-textbox addToAutoComplete" data-id="{{$issue->id}}"
                          id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}"
                          style="" rows="2"></textarea>

                <div class="col-12 d-flex justify-content-between align-items-center m-0 p-0 mt-2 mb-2">
                    <input class="form-checkbox-input mr-1" name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                        {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number") }}
                </div>

                <div class="col-12 d-flex align-items-center justify-content-between">
                    <button style="" class="btn btn-sm btn-image send-message-open m-0 p-0"
                            type="submit" id="submit_message_{{$issue->id}}" data-id="{{$issue->id}}"><img
                                src="/images/filled-sent.png" /></button>

                    <button type="button" class="btn btn-xs btn-image load-communication-modal"
                        data-object='developer_task' data-id="{{ $issue->id }}"
                        style="margin-top:-0%;margin-left: -3%;" title="Load messages"
                        data-is_admin="{{ Auth::user()->hasRole('Admin') }}"><img src="/images/chat.png" alt="">
                    </button>
                    
                    @include('development.partials.upload-document-modal')

                    <button class="btn btn-image upload-task-files-button btn-xs m-0" type="button" title="Uploaded Files" data-id="{{ $issue->id }}">
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </button>

                    <input type="hidden" name="is_audio" id="is_audio_{{$issue->id}}" class="is_audio" value="0">
                    <button type="button" class="btn btn-xs btn-image btn-trigger-rvn-modal m-0" data-id="{{$issue->id}}"
                            data-tid="{{$issue->id}}" title="Record & Send Voice Message" style="margin-top: 2%;"><img
                                src="{{asset('images/record-voice-message.png')}}" alt=""></button>

                    <a class="btn btn-xs btn-image" title="View Drive Files"
                       onclick="fetchGoogleDriveFileData('{{$issue->id}}')" style="margin-top:-0%;margin-left: -3%;">
                        <img width="2px;" src="/images/google-drive.png" />
                    </a>

                    <a class="btn btn-xs btn-image" id="global_files_and_attachments_id" title="File video and images"
                       onclick="GlobalFilesAndAttachments('{{$issue->id}}')" style="margin-top:-0%;margin-left: -3%;">
                        <img width="2px;" src="/images/attach.png" />
                    </a>
                </div>

                <div class="td-full-container d-flex align-items-center hidden">
                    <button class="btn btn-secondary btn-xs mr-2" onclick="sendImage({{ $issue->id }} )">Send Attachment
                    </button>
                    <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images
                    </button>
                    <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple />
                </div>

                <div class="col-12 d-flex pb-5 pt-2 justify-content-start pl-1 task-container-row">
                    @if($issue->is_audio)
                        <audio controls="" src="{{\App\Helpers::getAudioUrl($issue->message)}}"></audio>
                    @else
                        <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}"
                              style="word-break: break-all; text-align:left; ">{{ \Illuminate\Support\Str::limit($issue->task, 50, $end='...') }}</span>

                        <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }} hidden"
                              style="word-break: break-all; text-align:left; ">#{{ $issue->id }}. {{ $issue->subject }}. {{ $issue->task }}</span>
                    @endif
                </div>
            </td>
            <td data-id="{{ $issue->id }}">
                <div class="form-group">
                    @if ($issue->status == 'Approved')
                        <span>{{ $issue->status }}</span>: {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                    @elseif ($issue->estimate_minutes)
                        <span style="color:#337ab7"><strong>Unapproved</strong></span>
                        : {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                    @else
                        <span style="color:#337ab7"><strong>Unapproved</strong> </span>
                    @endif
                </div>

                @if(auth()->user()->id == $issue->assigned_to)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{$issue->id}}" data-type="developer">Meeting time
                    </button>
                @elseif(auth()->user()->id == $issue->master_user_id)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{$issue->id}}" data-type="lead">Meeting time
                    </button>
                @elseif(auth()->user()->id == $issue->tester_id)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{$issue->id}}" data-type="tester">Meeting time
                    </button>
                @elseif(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{$issue->id}}" data-type="admin">Meeting time
                    </button>
                @endif

            </td>
            <td data-id="{{ $issue->id }}">
                <div class="form-group">
                    <div class='input-group'>
                        <span>{{ $issue->developerTaskHistory?->new_value ?: "--" }}</span>
                    </div>
                </div>
            </td>
            <td>
                @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
                    Developer : {{ formatDuration($issue->timeSpent->tracked) }}

                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                            title="Show tracked time History" data-id="{{$issue->id}}" data-type="developer"><i
                                class="fa fa-info-circle"></i></button>
                @endif

                @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
                    Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                            title="Show tracked time History" data-id="{{$issue->id}}" data-type="lead"><i
                                class="fa fa-info-circle"></i></button>
                @endif

                @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
                    Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                            title="Show tracked time History" data-id="{{$issue->id}}" data-type="tester"><i
                                class="fa fa-info-circle"></i></button>
                @endif


                @if(!$issue->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->assigned_to))
                    <button type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for User"
                            data-id="{{$issue->id}}" data-type="developer">Create D Task
                    </button>
                @endif
                @if(!$issue->lead_hubstaff_task_id && $issue->master_user_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->master_user_id))
                    <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                            title="Create Hubstaff task for Master user" data-id="{{$issue->id}}" data-type="lead">
                        Create L Task
                    </button>
                @endif

                @if(!$issue->tester_hubstaff_task_id && $issue->tester_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->tester_id))
                    <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                            title="Create Hubstaff task for Tester" data-id="{{$issue->id}}" data-type="tester">Create T
                        Task
                    </button>
                @endif
            </td>
            <td>
                <div class="form-group">
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
                </div>

                <div class="expand-column-lead hidden" data-id="{{ $issue->id }}">
                    <div class="form-group mt-5">
                        <label class="fs-2 text-dark" for="user_{{$issue->id}}">Master User:</label>
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
                    </div>
                    <div class="form-group mt-5">
                        <label class="fs-2 text-dark" for="user_{{$issue->id}}">Team Lead:</label>
                        <select class="form-control assign-team-lead select2" data-id="{{$issue->id}}" name="team_lead_id"
                                id="user_{{$issue->id}}">
                            <option value="">Select...</option>
                            @foreach($users as $id=>$name)
                                <option value="{{$id}}" {{$issue->team_lead_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-5">
                        <label class="fs-2 text-dark" for="user_{{$issue->id}}">Tester:</label>
                        <select class="form-control assign-tester select2" data-id="{{$issue->id}}" name="tester_id"
                                id="user_{{$issue->id}}">
                            <option value="">Select...</option>
                            @foreach($users as $id=>$name)
                                <option value="{{$id}}" {{$issue->tester_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history"
                            title="Show History" data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs pull-request-history"
                            title="Pull Request History" data-id="{{$issue->id}}"><i class="fa fa-history"></i></button>
                </div>
            </td>
            <td>
                <div>
                    @if($issue->is_resolved)
                        <strong>Done</strong>
                    @else
                        {{ html()->select("task_status", $statusList, $issue->status)->class("form-control resolve-issue")->attribute('onchange', "resolveIssue(this," . $issue->id . ")") }}
                    @endif
                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history"
                            title="Show Status History" data-id="{{$issue->id}}">
                        <i class="fa fa-info-circle"></i>
                    </button>
                </div>
            </td>
            <td>
                {{ $issue->cost ?: 0 }}
            </td>
            <td class="p-2">
                <div style="margin-bottom:10px;width: 100%;">
                    <div class="d-flex align-items-center">
                        <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                               value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-approximate-lead 99999" title="Send approximate"
                                    onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                    data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>

                    @php
                        $time_history = $issue->dthWithMinuteEstimate;
                    @endphp

                    @if(!empty($time_history))
                        @if($issue->task_start!=1)
                            <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                                    class="btn btn-sm startDirectTask" data-task-type="1">
                                <i class="fa fa-play" aria-hidden="true"></i>
                            </button>
                        @else
                            <input type="hidden" value="{{$issue->m_start_date}}" class="m_start_date_"
                                   id="m_start_date_{{$issue->id}}" data-id="{{$issue->id}}"
                                   data-value="{{$issue->m_start_date}}">
                            <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                                    class="btn btn-sm startDirectTask" data-task-type="2">
                                <i class="fa fa-stop" aria-hidden="true"></i>
                            </button>
                            <div id="time-counter_{{$issue->id}}"></div>
                        @endif

                        <button type="button" class="btn btn-xs show-timer-history" title="Show timer History"
                                data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
                    @endif
                </div>
                @php
                    $time_history = $issue->dthWithMinuteEstimate;
                @endphp

                @if(!empty($time_history))
                    @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                        <button data-task="{{$time_history->developer_task_id}}" data-id="{{$time_history->id}}"
                                title="approve" data-type="DEVTASK"
                                class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                    @endif

                    @if($issue->task_start!=1)
                        <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                                class="btn btn-sm startDirectTask" data-task-type="1">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </button>
                    @else
                        <input type="hidden" value="{{$issue->m_start_date}}" class="m_start_date_"
                               id="m_start_date_{{$issue->id}}" data-id="{{$issue->id}}" data-value="{{$issue->id}}">
                        <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                                class="btn btn-sm startDirectTask" data-task-type="2">
                            <i class="fa fa-stop" aria-hidden="true"></i>
                        </button>
                        <div id="time-counter_{{$issue->id}}"></div>
                    @endif

                    <button type="button" class="btn btn-xs show-timer-history" title="Show timer History"
                            data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
                @endif
            </td>
            <td class="p-2">
                <div class="d-flex align-items-center mb-3">
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

                <div class="d-flex align-items-center">
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

            <td id="shortcutsIds">@include('development.partials.shortcutsdl')</td>
            <td>
                <button type="button" class="btn btn-secondary btn-sm" onclick="Showactionbtn('{{$issue->id}}')"><i
                            class="fa fa-arrow-down"></i></button>
            </td>
        </tr>
        <tr class="action-btn-tr-{{$issue->id}} d-none">
            <td class="font-weight-bold">Action</td>
            <td colspan="15">
                <button class="btn btn-image set-remark m-0" data-task_id="{{ $issue->id }}" data-task_type="Dev-task"><i
                            class="fa fa-comment" aria-hidden="true"></i></button>

                <a title="Task Information: Update" class="btn btn-sm btn-image m-0" href="javascript:void(0);"
                   onclick="funTaskInformationModal(this, '{{ $issue->id }}')"><i class="fa fa-info-circle"
                                                                                  aria-hidden="true"></i></a>

                <button class="btn btn-sm btn-image create-task-document m-0" title="Create document"
                        data-id="{{$issue->id}}">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                </button>
                <button class="btn btn-sm btn-image show-created-task-document m-0" title="Show created document"
                        data-id="{{$issue->id}}">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>
                <button class="btn btn-sm btn-image add-document-permission m-0" data-task_id="{{$issue->id}}"
                        data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                    <i class="fa fa-key" aria-hidden="true"></i>
                </button>
                <button class="btn btn-sm btn-image add-scrapper m-0" data-task_id="{{$issue->id}}" data-task_type="DEVTASK"
                        data-assigned_to="{{$issue->assigned_to}}">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>

                @include('development.partials.show-scrapper-logs-btn')

                <button style="padding-left: 0;padding-left:3px;" type="button"
                        class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $issue->id }} m-0"
                        title="Show scrapper" data-id="{{ $issue->id }}" data-category="{{ $issue->id }}"><i
                            class="fa fa-list"></i></button>

                <button class="btn btn-image expand-row-btn-lead m-0" data-id="{{ $issue->id }}"><img src="/images/forward.png"></button>
            </td>
        </tr>
    @endif
@else
    <tr style="color:grey;">
        <td>
            {{ $issue->id }}</br>
            @if($issue->is_resolved==0)
                <input type="checkbox" name="selected_issue[]"
                       value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
            @endif
            <input type="checkbox" title="Select task" class="select_task_checkbox" name="task"
                   data-id="{{ $issue->id }}" value="">
        </td>
        <td class="p-2">{{ Carbon\Carbon::parse($issue->created_at)->format('d-m H:i') }}</td>
        <td class="task-subject-container">
            <p class="hidden">{{ $issue->subject ?? 'N/A' }}</p>
            <p>{{ $issue->subject ? \Illuminate\Support\Str::limit($issue->subject, 20, $end='...') : 'N/A' }}</p>
        </td>
        <td class="expand-row">
            <!-- class="expand-row" -->
            <textarea class="form-control send-message-textbox addToAutoComplete" data-id="{{$issue->id}}"
                      id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}"
                      style="margin-top:5px;margin-bottom:5px" rows="3" cols="20"></textarea>

            <div class="col-12 d-flex justify-content-between align-items-center m-0 p-0 mt-2 mb-2">
                <input class="" name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                    {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:30% !important;display: inline;") }}
            </div>

            <div class="col-12 d-flex align-items-center justify-content-between">
                <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open"
                        type="submit" id="submit_message_{{$issue->id}}" data-id="{{$issue->id}}"><img
                            src="/images/filled-sent.png" /></button>

                <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task'
                        data-id="{{ $issue->id }}" style="margin-top:-0%;margin-left: -3%;" title="Load messages"
                        data-is_admin="{{ Auth::user()->hasRole('Admin') }}"><img src="/images/chat.png" alt=""></button>

                <button class="btn btn-image upload-task-files-button btn-xs" type="button" title="Uploaded Files" data-id="{{ $issue->id }}">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                </button>

                <input type="hidden" name="is_audio" id="is_audio_{{$issue->id}}" class="is_audio" value="0">
                <button type="button" class="btn btn-xs btn-image btn-trigger-rvn-modal" data-id="{{$issue->id}}"
                        data-tid="{{$issue->id}}" title="Record & Send Voice Message" style="margin-top: 2%;"><img
                            src="{{asset('images/record-voice-message.png')}}" alt=""></button>

                <a class="btn btn-xs btn-image" title="View Drive Files"
                   onclick="fetchGoogleDriveFileData('{{$issue->id}}')" style="margin-top:-0%;margin-left: -3%;">
                    <img width="2px;" src="/images/google-drive.png" />
                </a>

                <div class="d-flex align-items-centert d-full-container hidden">
                    <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
                    <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
                    <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple />
                </div>
            </div>


            <div class="col-12 d-flex pb-5 pt-2 justify-content-start pl-1 task-container-row">
                @if($issue->is_audio)
                    <audio controls="" src="{{\App\Helpers::getAudioUrl($issue->message)}}"></audio>
                @else
                    <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}"
                          style="word-break: break-all;">{{ \Illuminate\Support\Str::limit($issue->message, 90, $end='...') }}</span>

                    <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }} hidden"
                          style="word-break: break-all; text-align:left; ">#{{ $issue->id }}. {{ $issue->subject }}. {{ $issue->task }}</span>
                @endif
            </div>
        </td>
        <td data-id="{{ $issue->id }}">
            <div class="form-group">
                @if ($issue->status == 'Approved')
                    <span>{{ $issue->status }}</span>: {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                @elseif ($issue->estimate_minutes)
                    <span style="color:#337ab7"><strong>Unapproved</strong></span>
                    : {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                @else
                    <span style="color:#337ab7"><strong>Unapproved</strong> </span>
                @endif
            </div>

            @if(auth()->user()->id == $issue->assigned_to)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{$issue->id}}" data-type="developer">Meeting time
                </button>
            @elseif(auth()->user()->id == $issue->master_user_id)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{$issue->id}}" data-type="lead">Meeting time
                </button>
            @elseif(auth()->user()->id == $issue->tester_id)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{$issue->id}}" data-type="tester">Meeting time
                </button>
            @elseif(auth()->user()->isAdmin())
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{$issue->id}}" data-type="admin">Meeting time
                </button>
            @endif

        </td>
        <td data-id="{{ $issue->id }}">
            <div class="form-group">
                <div class='input-group'>
                    <span>{{ $issue->developerTaskHistory?->new_value ?: "--" }}</span>
                </div>
            </div>
        </td>
        <td>
            @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
                Developer : {{ formatDuration($issue->timeSpent->tracked) }}

                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                        title="Show tracked time History" data-id="{{$issue->id}}" data-type="developer"><i
                            class="fa fa-info-circle"></i></button>
            @endif

            @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
                Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                        title="Show tracked time History" data-id="{{$issue->id}}" data-type="lead"><i
                            class="fa fa-info-circle"></i></button>
            @endif

            @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
                Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                        title="Show tracked time History" data-id="{{$issue->id}}" data-type="tester"><i
                            class="fa fa-info-circle"></i></button>
            @endif


            @if(!$issue->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->assigned_to))
                <button type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for User"
                        data-id="{{$issue->id}}" data-type="developer">Create D Task
                </button>
            @endif
            @if(!$issue->lead_hubstaff_task_id && $issue->master_user_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->master_user_id))
                <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                        title="Create Hubstaff task for Master user" data-id="{{$issue->id}}" data-type="lead">Create L
                    Task
                </button>
            @endif

            @if(!$issue->tester_hubstaff_task_id && $issue->tester_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->tester_id))
                <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                        title="Create Hubstaff task for Tester" data-id="{{$issue->id}}" data-type="tester">Create T
                    Task
                </button>
            @endif
        </td>
        <td>
            <div class="form-group">
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
            </div>
            
            <div class="expand-column-lead hidden" data-id="{{ $issue->id }}">
                <div class="form-group mt-5">
                    <label class="fs-2 text-dark" for="user_{{$issue->id}}">Master User:</label>
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
                </div>
                <div class="form-group mt-5">
                    <label class="fs-2 text-dark" for="user_{{$issue->id}}">Team Lead:</label>
                    <select class="form-control assign-team-lead select2" data-id="{{$issue->id}}" name="team_lead_id"
                            id="user_{{$issue->id}}">
                        <option value="">Select...</option>
                        @foreach($users as $id=>$name)
                            <option value="{{$id}}" {{$issue->team_lead_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mt-5">
                    <label class="fs-2 text-dark" for="user_{{$issue->id}}">Tester:</label>
                    <select class="form-control assign-tester select2" data-id="{{$issue->id}}" name="tester_id"
                            id="user_{{$issue->id}}">
                        <option value="">Select...</option>
                        @foreach($users as $id=>$name)
                            <option value="{{$id}}" {{$issue->tester_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history"
                        title="Show History" data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs pull-request-history"
                        title="Pull Request History" data-id="{{$issue->id}}"><i class="fa fa-history"></i></button>
            </div>
        </td>
        <td>
            <div>
                @if($issue->is_resolved)
                    <strong>Done</strong>
                @else
                        {{ html()->select("task_status", $statusList, $issue->status)->class("form-control resolve-issue")->attribute('onchange', "resolveIssue(this," . $issue->id . ")") }}
                @endif
                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history"
                        title="Show Status History" data-id="{{$issue->id}}">
                    <i class="fa fa-info-circle"></i>
                </button>
            </div>
        </td>
        <td>
            {{ $issue->cost ?: 0 }}
        </td>
        <td class="p-2">
            <div style="d-flex align-items-center">
                <div class="form-group">
                    <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                           value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                    <div style="max-width: 30px;">
                        <button class="btn btn-sm btn-image send-approximate-lead 10101" title="Send approximate"
                                onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                        </button>
                    </div>
                </div>
            </div>

            @php
                $time_history = $issue->dthWithMinuteEstimate;
            @endphp

            @if(!empty($time_history))
                @if($issue->task_start!=1)
                    <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                            class="btn btn-sm startDirectTask" data-task-type="1">
                        <i class="fa fa-play" aria-hidden="true"></i>
                    </button>
                @else
                    <input type="hidden" value="{{$issue->m_start_date}}" class="m_start_date_"
                           id="m_start_date_{{$issue->id}}" data-id="{{$issue->id}}"
                           data-value="{{$issue->m_start_date}}">
                    <button data-task="{{$issue->id}}" title="Start Task" data-type="DEVTASK"
                            class="btn btn-sm startDirectTask" data-task-type="2">
                        <i class="fa fa-stop" aria-hidden="true"></i>
                    </button>
                    <div id="time-counter_{{$issue->id}}"></div>
                @endif

                <button type="button" class="btn btn-xs show-timer-history" title="Show timer History"
                        data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
            @endif
        </td>
        <td class="p-2">
            <div class="d-flex align-items-center mb-3">
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

            <div class="d-flex align-items-center">
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
            <button class="btn btn-image set-remark m-0" data-task_id="{{ $issue->id }}" data-task_type="Dev-task"><i
                        class="fa fa-comment" aria-hidden="true"></i></button>

            <a title="Task Information: Update" class="btn btn-sm btn-image m-0" href="javascript:void(0);"
               onclick="funTaskInformationModal(this, '{{ $issue->id }}')"><i class="fa fa-info-circle"
                                                                              aria-hidden="true"></i></a>

            <button class="btn btn-sm btn-image create-task-document m-0" title="Create document" data-id="{{$issue->id}}">
                <i class="fa fa-file-text" aria-hidden="true"></i>
            </button>
            <button class="btn btn-sm btn-image show-created-task-document m-0" title="Show created document"
                    data-id="{{$issue->id}}">
                <i class="fa fa-list" aria-hidden="true"></i>
            </button>
            <button class="btn btn-sm btn-image add-document-permission m-0" data-task_id="{{$issue->id}}"
                    data-task_type="DEVTASK" data-assigned_to="{{$issue->assigned_to}}">
                <i class="fa fa-key" aria-hidden="true"></i>
            </button>

            <button class="btn btn-sm btn-image add-scrapper m-0" data-task_id="{{$issue->id}}" data-task_type="DEVTASK"
                    data-assigned_to="{{$issue->assigned_to}}">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>

            @include('development.partials.show-scrapper-logs-btn')

            <button style="padding-left: 0;padding-left:3px;" type="button"
                    class="btn btn-image d-inline count-dev-scrapper count-dev-scrapper_{{ $issue->id }} m-0"
                    title="Show scrapper" data-id="{{ $issue->id }}" data-category="{{ $issue->id }}"><i
                        class="fa fa-list"></i></button>

            <button class="btn btn-image expand-row-btn-lead m-0" data-id="{{ $issue->id }}"><img src="/images/forward.png"></button>
        </td>
    </tr>
@endif

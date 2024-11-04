@if (Auth::user()->isAdmin())
    @if(!empty($dynamicColumnsToShowDs))
        <tr style="color:grey;">
            @if (!in_array('ID', $dynamicColumnsToShowDs))
                <td>
                    {{ $issue->id }}
                    @if ($issue->is_resolved == 0)
                        <input type="checkbox" name="selected_issue[]"
                               value="{{ $issue->id }}" {{ in_array($issue->id, $priority) ? 'checked' : '' }}>
                    @endif

                </td>
            @endif

            @if (!in_array('Date', $dynamicColumnsToShowDs))
                <td class="p-2">{{ Carbon\Carbon::parse($issue->created_at)->format('d-m H:i') }}</td>
            @endif

            @if (!in_array('Subject', $dynamicColumnsToShowDs))
                <td class="task-subject-container">
                    <p class="hidden">{{ $issue->subject ?? 'N/A' }}</p>
                    <p>{{ $issue->subject ? \Illuminate\Support\Str::limit($issue->subject, 20, $end='...') : 'N/A' }}</p>
                </td>
            @endif

            @if (!in_array('Communication', $dynamicColumnsToShowDs))
                <td class="expand-row">
                    <!-- class="expand-row" -->
                    <textarea class="form-control send-message-textbox" data-id="{{ $issue->id }}"
                              id="send_message_{{ $issue->id }}" name="send_message_{{ $issue->id }}"
                              style="margin-top:5px;margin-bottom:5px;" rows="3" cols="20"></textarea>

                    <div class="col-12 d-flex justify-content-between align-items-center m-0 p-0 mt-2 mb-2">
                            {{ html()->select('send_message_' . $issue->id, ['to_master' => 'Send To Master Developer', 'to_developer' => 'Send To Developer', 'to_team_lead' => 'Send To Team Lead', 'to_tester' => 'Send To Tester'])->class('form-control send-message-number') }}
                    </div>

                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <button style="" class="btn btn-sm btn-image send-message-open"
                                type="submit" id="submit_message_{{ $issue->id }}" data-id="{{ $issue->id }}"><img
                                    src="/images/filled-sent.png" /></button>

                        <button type="button" class="btn btn-xs btn-image load-communication-modal"
                                data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top: 2%;"
                                title="Load messages"><img src="/images/chat.png" alt=""></button>

                        <button class="btn btn-image upload-task-files-button ml-2" type="button" title="Uploaded Files" data-id="{{ $issue->id }}">
                            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                        </button>

                        <input type="hidden" name="is_audio" id="is_audio_{{$issue->id}}" class="is_audio" value="0">
                        <button type="button" class="btn btn-xs btn-image btn-trigger-rvn-modal" data-id="{{$issue->id}}"
                                data-tid="{{$issue->id}}" title="Record & Send Voice Message" style="margin-top: 2%;"><img
                                    src="{{asset('images/record-voice-message.png')}}" alt=""></button>

                        <a class="btn btn-xs btn-image" title="View Drive Files"
                           onclick="fetchGoogleDriveFileData('{{$issue->id}}')" style="margin-top: 2%;">
                            <img width="2px;" src="/images/google-drive.png" />
                        </a>
                    </div>

                    <div class="d-flex align-items-center td-full-container hidden">
                        <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send
                            Attachment
                        </button>
                        <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{ $issue->id }} )">Send
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

            @if (!in_array('Est Completion Time', $dynamicColumnsToShowDs))
                <td data-id="{{ $issue->id }}">
                    <div class="form-group">
                        <div class='input-group'>
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
                    </div>
                    @if (auth()->user()->id == $issue->assigned_to)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="developer">Meeting time
                        </button>
                    @elseif(auth()->user()->id == $issue->master_user_id)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="lead">Meeting time
                        </button>
                    @elseif(auth()->user()->id == $issue->tester_id)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="tester">Meeting time
                        </button>
                    @elseif(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="admin">Meeting time
                        </button>
                    @endif

                    @if ($isTimeShow)
                        Others : {{ $developerTime }}
                    @endif
                </td>
            @endif

            @if (!in_array('Est Completion Date', $dynamicColumnsToShowDs))
                <td data-id="{{ $issue->id }}">
                    <div class="form-group">
                        <div class='input-group'>
                            <span>{{ $issue->developerTaskHistory ? $issue->developerTaskHistory->new_value : '--' }}</span>
                        </div>
                    </div>
                    @if (auth()->user()->id == $issue->assigned_to)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="developer">Meeting time
                        </button>
                    @elseif(auth()->user()->id == $issue->master_user_id)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="lead">Meeting time
                        </button>
                    @elseif(auth()->user()->id == $issue->tester_id)
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="tester">Meeting time
                        </button>
                    @elseif(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                                data-id="{{ $issue->id }}" data-type="admin">Meeting time
                        </button>
                    @endif

                    @if ($isTimeShow)
                        Others : {{ $testerTime }}
                    @endif
                </td>
            @endif

            @if (!in_array('Tracked Time', $dynamicColumnsToShowDs))
                <td>
                    @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
                        Developer : {{ formatDuration($issue->timeSpent->tracked) }}
                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-tracked-history" title="Show tracked time History"
                                data-id="{{ $issue->id }}" data-type="developer"><i class="fa fa-info-circle"></i>
                        </button>
                    @endif

                    @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
                        Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-tracked-history" title="Show tracked time History"
                                data-id="{{ $issue->id }}" data-type="lead"><i class="fa fa-info-circle"></i></button>
                    @endif
                    @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
                        Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

                        <button style="float:right;padding-right:0px;" type="button"
                                class="btn btn-xs show-tracked-history" title="Show tracked time History"
                                data-id="{{ $issue->id }}" data-type="tester"><i class="fa fa-info-circle"></i></button>
                    @endif

                    @if (!$issue->hubstaff_task_id &&
                    (auth()->user()->isAdmin() ||
                    auth()->user()->id == $issue->assigned_to))
                        <button type="button" class="btn btn-xs create-hubstaff-task"
                                title="Create Hubstaff task for User" data-id="{{ $issue->id }}" data-type="developer">
                            Create D Task
                        </button>
                    @endif
                    @if (!$issue->lead_hubstaff_task_id &&
                    $issue->master_user_id &&
                    (auth()->user()->isAdmin() ||
                    auth()->user()->id == $issue->master_user_id))
                        <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                                title="Create Hubstaff task for Master user" data-id="{{ $issue->id }}"
                                data-type="lead">Create L
                            Task
                        </button>
                    @endif



                    @if (!$issue->tester_hubstaff_task_id &&
                    $issue->tester_id &&
                    (auth()->user()->isAdmin() ||
                    auth()->user()->id == $issue->tester_id))
                        <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                                title="Create Hubstaff task for Tester" data-id="{{ $issue->id }}" data-type="tester">
                            Create T
                            Task
                        </button>
                    @endif

                </td>
            @endif

            @if (!in_array('Developers', $dynamicColumnsToShowDs))
                <td>
                    @if (isset($userID) && $issue->team_lead_id == $userID)
                        <div class="form-group">
                            <label for="" style="font-size: 12px;">Assigned To :</label>
                            <select class="form-control assign-user select2" data-id="{{ $issue->id }}"
                                    name="assigned_to" id="user_{{ $issue->id }}">
                                <option value="">Select...</option>
                                    <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                                @foreach ($users as $id => $name)
                                    @if ($assignedId == $id)
                                        <option value="{{ $id }}" selected>{{ $name }}</option>
                                    @else
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="form-group">
                            <label for="" style="font-size: 12px;">Assigned To :</label>
                            @if ($issue->assignedUser)
                                <p>{{ $issue->assignedUser->name }}</p>
                            @else
                                <p>Unassigned</p>
                            @endif
                        </div>
                    @endif

                    <div class="expand-column-lead hidden" data-id="{{ $issue->id }}">
                        <div class="d-flex flex-column align-items-start mt-5">
                            <label for="" style="font-size: 12px;">Lead :</label>
                            @if ($issue->masterUser)
                                <p>{{ $issue->masterUser->name }}</p>
                            @else
                                <p>N/A</p>
                            @endif
                        </div>

                        <div class="d-flex flex-column align-items-start mt-5">
                            @if ($issue->teamLead)
                                <label for="" style="font-size: 12px;">Team Lead :</label>
                                <p>{{ $issue->teamLead->name }}</p>
                            @endif
                        </div>

                        <div class="d-flex flex-column align-items-start mt-5">
                            @if ($issue->tester)
                                <label for="" style="font-size: 12px;">Tester :</label>
                                <p>{{ $issue->tester->name }}</p>
                            @endif
                        </div>
                    </div>
                </td>
            @endif

            @if (!in_array('Status', $dynamicColumnsToShowDs))
                <td>
                    @if ($issue->is_resolved)
                        <strong>Done</strong>
                    @else
                        <select name="task_status" id="task_status" class="form-control"
                                onchange="resolveIssue(this,{{ $issue->id }})">
                            @foreach ($statusList as $status)
                                <option value="{{ $status }}" {{ !empty($issue->status) && $issue->status == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </td>
            @endif

            @if (!in_array('Cost', $dynamicColumnsToShowDs))
                <td>
                    {{ $issue->cost ?: 0 }}
                </td>
            @endif

            @if (!in_array('Estimated Time', $dynamicColumnsToShowDs))
                <td class="p-2">
                    <div style="margin-bottom:10px;width: 100%;">
                        <div class="d-flex align-items-center">
                            <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                                   value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                            <div style="max-width: 30px;">
                                <button class="btn btn-sm btn-image send-approximate-lead 44444"
                                        title="Send approximate"
                                        onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                        data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                                </button>
                            </div>
                        </div>
                    </div>
                    @if(!empty($time_history))
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

                        <!-- <button type="button" class="btn btn-xs show-timer-history" title="Show timer History" data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button> -->
                    @endif
                </td>
            @endif

            @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowDs))
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

            @if (!in_array('Shortcuts', $dynamicColumnsToShowDs))
                <td id="shortcutsIds">@include('development.partials.shortcutsdl')</td>
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
                        <?php echo $issue->language; ?>

                    <div class="dropdown dropleft">
                        <!-- <a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" id="dropdownMenuLink{{$issue->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{$issue->id}}">
                        <a class="dropdown-item" href="javascript:void(0);" onclick="funTaskInformationModal(this, '{{ $issue->id }}')">Task Information: Update</a>
                    </div> -->

                        <a title="Task Information: Update" class="btn btn-sm btn-image" href="javascript:void(0);"
                           onclick="funTaskInformationModal(this, '{{ $issue->id }}')"><i class="fa fa-info-circle"
                                                                                          aria-hidden="true"></i></a>

                        <button class="btn btn-sm mt-2 create-task-document" title="Create document"
                                data-id="{{$issue->id}}">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm mt-2 show-created-task-document" title="Show created document"
                                data-id="{{$issue->id}}">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm mt-2 add-document-permission" data-task_id="{{$issue->id}}"
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

                        <button class="btn btn-image expand-row-btn-lead" data-id="{{ $issue->id }}"><img src="/images/forward.png"></button>
                    </div>
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="14">
                <div id="collapse_{{ $issue->id }}" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="messageList" id="message_list_{{ $issue->id }}">
                            @foreach ($issue->messages as $message)
                                <p>
                                    <strong>
                                            <?php echo ! empty($message->taskUser) ? 'To : '.$message->taskUser->name : ''; ?>
                                            <?php echo ! empty($message->user) ? 'From : '.$message->user->name : ''; ?>
                                        At {{ date('d-M-Y H:i:s', strtotime($message->created_at)) }}
                                    </strong>
                                </p>
                                {!! nl2br($message->message) !!}
                                <hr />
                            @endforeach
                        </div>
                    </div>
                    <div class="panel-footer">
                        <textarea class="form-control send-message-textbox" data-id="{{ $issue->id }}"
                                  id="send_message_{{ $issue->id }}" name="send_message_{{ $issue->id }}"></textarea>
                        <button type="submit" id="submit_message" class="btn btn-secondary ml-3 send-message"
                                data-id="{{ $issue->id }}" style="float: right;margin-top: 2%;">Submit
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    @else
        <tr style="color:grey;">
            <td>
                {{ $issue->id }}
                @if ($issue->is_resolved == 0)
                    <input type="checkbox" name="selected_issue[]"
                           value="{{ $issue->id }}" {{ in_array($issue->id, $priority) ? 'checked' : '' }}>
                @endif

            </td>
            <td class="p-2">{{ Carbon\Carbon::parse($issue->created_at)->format('d-m H:i') }}</td>
            <td class="task-subject-container">
                <p class="hidden">{{ $issue->subject ?? 'N/A' }}</p>
                <p>{{ $issue->subject ? \Illuminate\Support\Str::limit($issue->subject, 20, $end='...') : 'N/A' }}</p>
            </td>

            <td class="expand-row">
                <!--span style="word-break: break-all;">{{ \Illuminate\Support\Str::limit($issue->message, 150, $end = '...') }}</span>
                @if ($issue->getMedia(config('constants.media_tags'))->first())
        <br>
                    @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
        <a href="{{ getMediaUrl($image) }}" target="_blank" class="d-inline-block">
                            <img src="{{ getMediaUrl($image) }}" class="img-responsive" style="width: 50px" alt="">
                        </a>

                    @endforeach
                @endif
                <div>
                    <div class="panel-group">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" href="#collapse_{{ $issue->id }}">Messages({{ count($issue->messages) }})</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div-->

                <!-- class="expand-row" -->
                <textarea class="form-control send-message-textbox" data-id="{{ $issue->id }}"
                          id="send_message_{{ $issue->id }}" name="send_message_{{ $issue->id }}"
                          style="margin-top:5px;margin-bottom:5px;" rows="3" cols="20"></textarea>

                <div class="col-12 d-flex align-items-center justify-content-between">
                        {{ html()->select('send_message_' . $issue->id, ['to_master' => 'Send To Master Developer', 'to_developer' => 'Send To Developer', 'to_team_lead' => 'Send To Team Lead', 'to_tester' => 'Send To Tester'])->class('form-control send-message-number')->style('width:85% !important;display: inline;') }}
                </div>

                <div class="col-12 d-flex align-items-center justify-content-between">
                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open"
                            type="submit" id="submit_message_{{ $issue->id }}" data-id="{{ $issue->id }}"><img
                                src="/images/filled-sent.png" /></button>


                    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task'
                            data-id="{{ $issue->id }}" style="margin-top: 2%;" title="Load messages"><img
                                src="/images/chat.png" alt=""></button>

                    <button class="btn btn-image upload-task-files-button ml-2" type="button" title="Uploaded Files" data-id="{{ $issue->id }}">
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </button>

                    <input type="hidden" name="is_audio" id="is_audio_{{$issue->id}}" class="is_audio" value="0">
                    <button type="button" class="btn btn-xs btn-image btn-trigger-rvn-modal" data-id="{{$issue->id}}"
                            data-tid="{{$issue->id}}" title="Record & Send Voice Message" style="margin-top: 2%;"><img
                                src="{{asset('images/record-voice-message.png')}}" alt=""></button>

                    <a class="btn btn-xs btn-image" title="View Drive Files"
                       onclick="fetchGoogleDriveFileData('{{$issue->id}}')" style="margin-top: 2%;">
                        <img width="2px;" src="/images/google-drive.png" />
                    </a>
                </div>

                <div class="d-flex align-items-center td-full-container hidden">
                    <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment
                    </button>
                    <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{ $issue->id }} )">Send
                        Images
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
                    <div class='input-group'>
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
                </div>
                @if (auth()->user()->id == $issue->assigned_to)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="developer">Meeting time
                    </button>
                @elseif(auth()->user()->id == $issue->master_user_id)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="lead">Meeting time
                    </button>
                @elseif(auth()->user()->id == $issue->tester_id)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="tester">Meeting time
                    </button>
                @elseif(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="admin">Meeting time
                    </button>
                @endif
                
                @if ($isTimeShow)
                    Others : {{ $developerTime }}
                @endif
            </td>
            <td data-id="{{ $issue->id }}">
                <div class="form-group">
                    <div class='input-group'>
                        <span>{{ $issue->developerTaskHistory ? $issue->developerTaskHistory->new_value : '--' }}</span>
                    </div>
                </div>
                @if (auth()->user()->id == $issue->assigned_to)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="developer">Meeting time
                    </button>
                @elseif(auth()->user()->id == $issue->master_user_id)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="lead">Meeting time
                    </button>
                @elseif(auth()->user()->id == $issue->tester_id)
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="tester">Meeting time
                    </button>
                @elseif(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                            data-id="{{ $issue->id }}" data-type="admin">Meeting time
                    </button>
                @endif

                @if($isTimeShow)
                    Others : {{ $developerTime }}
                @endif
            </td>
            <td>
                @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
                    Developer : {{ formatDuration($issue->timeSpent->tracked) }}
                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                            title="Show tracked time History" data-id="{{ $issue->id }}" data-type="developer"><i
                                class="fa fa-info-circle"></i></button>
                @endif

                @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
                    Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                            title="Show tracked time History" data-id="{{ $issue->id }}" data-type="lead"><i
                                class="fa fa-info-circle"></i></button>
                @endif
                @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
                    Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                            title="Show tracked time History" data-id="{{ $issue->id }}" data-type="tester"><i
                                class="fa fa-info-circle"></i></button>
                @endif

                @if (!$issue->hubstaff_task_id &&
                (auth()->user()->isAdmin() ||
                auth()->user()->id == $issue->assigned_to))
                    <button type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for User"
                            data-id="{{ $issue->id }}" data-type="developer">Create D Task
                    </button>
                @endif
                @if (!$issue->lead_hubstaff_task_id &&
                $issue->master_user_id &&
                (auth()->user()->isAdmin() ||
                auth()->user()->id == $issue->master_user_id))
                    <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                            title="Create Hubstaff task for Master user" data-id="{{ $issue->id }}" data-type="lead">
                        Create L
                        Task
                    </button>
                @endif



                @if (!$issue->tester_hubstaff_task_id &&
                $issue->tester_id &&
                (auth()->user()->isAdmin() ||
                auth()->user()->id == $issue->tester_id))
                    <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                            title="Create Hubstaff task for Tester" data-id="{{ $issue->id }}" data-type="tester">Create
                        T
                        Task
                    </button>
                @endif

            </td>
            <td>
                <label for="" style="font-size: 12px;">Assigned To :</label>
                @if (isset($userID) && $issue->team_lead_id == $userID)
                    <div class="form-group">
                        <select class="form-control assign-user select2" data-id="{{ $issue->id }}" name="assigned_to"
                                id="user_{{ $issue->id }}">
                            <option value="">Select...</option>
                                <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                            @foreach ($users as $id => $name)
                                @if ($assignedId == $id)
                                    <option value="{{ $id }}" selected>{{ $name }}</option>
                                @else
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="form-group">
                        @if ($issue->assignedUser)
                            <p>{{ $issue->assignedUser->name }}</p>
                        @else
                            <p>Unassigned</p>
                        @endif
                    </div>
                @endif

                <div class="expand-column-lead hidden" data-id="{{ $issue->id }}">
                    <div class="form-group mt-5">
                        <label for="" style="font-size: 12px;">Lead :</label>
                        @if ($issue->masterUser)
                            <p>{{ $issue->masterUser->name }}</p>
                        @else
                            <p>N/A</p>
                        @endif
                    </div>

                    <div class="form-group mt-5">
                        @if ($issue->teamLead)
                            <label for="" style="font-size: 12px;">Team Lead :</label>
                            <p>{{ $issue->teamLead->name }}</p>
                        @endif
                    </div>

                    <div class="form-group mt-5">
                        @if ($issue->tester)
                            <label for="" style="font-size: 12px;">Tester :</label>
                            <p>{{ $issue->tester->name }}</p>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                @if ($issue->is_resolved)
                    <strong>Done</strong>
                @else
                    <select name="task_status" id="task_status" class="form-control"
                            onchange="resolveIssue(this,{{ $issue->id }})">
                        @foreach ($statusList as $status)
                            <option value="{{ $status }}" {{ !empty($issue->status) && $issue->status == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </td>
            <td>
                {{ $issue->cost ?: 0 }}
            </td>
            <td class="p-2">
                <div style="margin-bottom:10px;width: 100%;">
                    <div class="form-group">
                        <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}"
                               value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                        <div style="max-width: 30px;">
                            <button class="btn btn-sm btn-image send-approximate-lead 55555" title="Send approximate"
                                    onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                    data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                            </button>
                        </div>
                    </div>
                </div>
                @if(!empty($time_history))
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

                @if(!empty($issue->estimate_date) && $issue->start_date!='0000-00-00 00:00:00')
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
                    <?php echo $issue->language; ?>

                <div class="dropdown dropleft">
                    <!-- <a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" id="dropdownMenuLink{{$issue->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{$issue->id}}">
                        <a class="dropdown-item" href="javascript:void(0);" onclick="funTaskInformationModal(this, '{{ $issue->id }}')">Task Information: Update</a>
                    </div> -->

                    <a title="Task Information: Update" class="btn btn-sm btn-image" href="javascript:void(0);"
                       onclick="funTaskInformationModal(this, '{{ $issue->id }}')"><i class="fa fa-info-circle"
                                                                                      aria-hidden="true"></i></a>

                    <button class="btn btn-sm mt-2 create-task-document" title="Create document"
                            data-id="{{$issue->id}}">
                        <i class="fa fa-file-text" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm mt-2 show-created-task-document" title="Show created document"
                            data-id="{{$issue->id}}">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm mt-2 add-document-permission" data-task_id="{{$issue->id}}"
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

                    <button class="btn btn-image expand-row-btn-lead" data-id="{{ $issue->id }}"><img src="/images/forward.png"></button>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="14">
                <div id="collapse_{{ $issue->id }}" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="messageList" id="message_list_{{ $issue->id }}">
                            @foreach ($issue->messages as $message)
                                <p>
                                    <strong>
                                            <?php echo ! empty($message->taskUser) ? 'To : '.$message->taskUser->name : ''; ?>
                                            <?php echo ! empty($message->user) ? 'From : '.$message->user->name : ''; ?>
                                        At {{ date('d-M-Y H:i:s', strtotime($message->created_at)) }}
                                    </strong>
                                </p>
                                {!! nl2br($message->message) !!}
                                <hr />
                            @endforeach
                        </div>
                    </div>
                    <div class="panel-footer">
                        <textarea class="form-control send-message-textbox" data-id="{{ $issue->id }}"
                                  id="send_message_{{ $issue->id }}" name="send_message_{{ $issue->id }}"></textarea>
                        <button type="submit" id="submit_message" class="btn btn-secondary ml-3 send-message"
                                data-id="{{ $issue->id }}" style="float: right;margin-top: 2%;">Submit
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    @endif
@else
    <tr style="color:grey;">
        <td>
            {{ $issue->id }}
            @if ($issue->is_resolved == 0)
                <input type="checkbox" name="selected_issue[]"
                       value="{{ $issue->id }}" {{ in_array($issue->id, $priority) ? 'checked' : '' }}>
            @endif

        </td>
        <td class="p-2">{{ Carbon\Carbon::parse($issue->created_at)->format('d-m H:i') }}</td>
        <td class="task-subject-container">
            <p class="hidden">{{ $issue->subject ?? 'N/A' }}</p>
            <p>{{ $issue->subject ? \Illuminate\Support\Str::limit($issue->subject, 20, $end='...') : 'N/A' }}</p>
        </td>

        <td class="expand-row">
            <!--span style="word-break: break-all;">{{ \Illuminate\Support\Str::limit($issue->message, 150, $end = '...') }}</span>
            @if ($issue->getMedia(config('constants.media_tags'))->first())
    <br>
                @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
    <a href="{{ getMediaUrl($image) }}" target="_blank" class="d-inline-block">
                        <img src="{{ getMediaUrl($image) }}" class="img-responsive" style="width: 50px" alt="">
                    </a>

                @endforeach
            @endif
            <div>
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse_{{ $issue->id }}">Messages({{ count($issue->messages) }})</a>
                            </h4>
                        </div>
                    </div>
                </div>
            </div-->

            <!-- class="expand-row" -->
            <textarea class="form-control send-message-textbox" data-id="{{ $issue->id }}"
                      id="send_message_{{ $issue->id }}" name="send_message_{{ $issue->id }}"
                      style="margin-top:5px;margin-bottom:5px;" rows="3" cols="20"></textarea>

            <div class="col-12 d-flex justify-content-between align-items-center m-0 p-0 mt-2 mb-2">
                    {{ html()->select('send_message_' . $issue->id, ['to_master' => 'Send To Master Developer', 'to_developer' => 'Send To Developer', 'to_team_lead' => 'Send To Team Lead', 'to_tester' => 'Send To Tester'])->class('form-control send-message-number')->style('width:85% !important;display: inline;') }}
            </div>

            <div class="col-12 d-flex align-items-center justify-content-between">
                <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open"
                        type="submit" id="submit_message_{{ $issue->id }}" data-id="{{ $issue->id }}"><img
                            src="/images/filled-sent.png" /></button>


                <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task'
                        data-id="{{ $issue->id }}" style="margin-top: 2%;" title="Load messages"><img src="/images/chat.png"
                                                                                                      alt=""></button>

                <button class="btn btn-image upload-task-files-button ml-2" type="button" title="Uploaded Files" data-id="{{ $issue->id }}">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                </button>

                <input type="hidden" name="is_audio" id="is_audio_{{$issue->id}}" class="is_audio" value="0">
                <button type="button" class="btn btn-xs btn-image btn-trigger-rvn-modal" data-id="{{$issue->id}}"
                        data-tid="{{$issue->id}}" title="Record & Send Voice Message" style="margin-top: 2%;"><img
                            src="{{asset('images/record-voice-message.png')}}" alt=""></button>

                <a class="btn btn-xs btn-image" title="View Drive Files"
                   onclick="fetchGoogleDriveFileData('{{$issue->id}}')" style="margin-top: 2%;">
                    <img width="2px;" src="/images/google-drive.png" />
                </a>
            </div>

            <div class="d-flex align-items-center td-full-container hidden">
                <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
                <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{ $issue->id }} )">Send
                    Images
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
                <div class='input-group'>
                    @if ($issue->status == 'Approved')
                        <span>{{ $issue->status }}</span>: {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                    @elseif ($issue->estimate_minutes)
                        <span style="color:#337ab7"><strong>Unapproved</strong></span>
                        : {{ $issue->estimate_minutes ? $issue->estimate_minutes : 0 }}
                    @else
                        <span style="color:#337ab7"><strong>Unapproved</strong> </span>
                    @endif
                </div>
            </div>
            @if (auth()->user()->id == $issue->assigned_to)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="developer">Meeting time
                </button>
            @elseif(auth()->user()->id == $issue->master_user_id)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="lead">Meeting time
                </button>
            @elseif(auth()->user()->id == $issue->tester_id)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="tester">Meeting time
                </button>
            @elseif(auth()->user()->isAdmin())
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="admin">Meeting time
                </button>
            @endif
            
            @if ($isTimeShow)
                Others : {{ $developerTime }}
            @endif
        </td>
        <td data-id="{{ $issue->id }}">
            <div class="form-group">
                <div class='input-group'>
                    <span>{{ $issue->developerTaskHistory ? $issue->developerTaskHistory->new_value : '--' }}</span>
                </div>
            </div>
            @if (auth()->user()->id == $issue->assigned_to)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="developer">Meeting time
                </button>
            @elseif(auth()->user()->id == $issue->master_user_id)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="lead">Meeting time
                </button>
            @elseif(auth()->user()->id == $issue->tester_id)
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="tester">Meeting time
                </button>
            @elseif(auth()->user()->isAdmin())
                <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings"
                        data-id="{{ $issue->id }}" data-type="admin">Meeting time
                </button>
            @endif

            @if ($isTimeShow)
                Others : {{ $developerTime }}
            @endif
        </td>
        <td>
            @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
                Developer : {{ formatDuration($issue->timeSpent->tracked) }}
                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                        title="Show tracked time History" data-id="{{ $issue->id }}" data-type="developer"><i
                            class="fa fa-info-circle"></i></button>
            @endif

            @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
                Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                        title="Show tracked time History" data-id="{{ $issue->id }}" data-type="lead"><i
                            class="fa fa-info-circle"></i></button>
            @endif
            @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
                Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history"
                        title="Show tracked time History" data-id="{{ $issue->id }}" data-type="tester"><i
                            class="fa fa-info-circle"></i></button>
            @endif

            @if (!$issue->hubstaff_task_id &&
            (auth()->user()->isAdmin() ||
            auth()->user()->id == $issue->assigned_to))
                <button type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for User"
                        data-id="{{ $issue->id }}" data-type="developer">Create D Task
                </button>
            @endif
            @if (!$issue->lead_hubstaff_task_id &&
            $issue->master_user_id &&
            (auth()->user()->isAdmin() ||
            auth()->user()->id == $issue->master_user_id))
                <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                        title="Create Hubstaff task for Master user" data-id="{{ $issue->id }}" data-type="lead">Create
                    L
                    Task
                </button>
            @endif



            @if (!$issue->tester_hubstaff_task_id &&
            $issue->tester_id &&
            (auth()->user()->isAdmin() ||
            auth()->user()->id == $issue->tester_id))
                <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task"
                        title="Create Hubstaff task for Tester" data-id="{{ $issue->id }}" data-type="tester">Create T
                    Task
                </button>
            @endif

        </td>
        <td>
            @if (isset($userID) && $issue->team_lead_id == $userID)
                <div class="form-group">
                    <label for="" style="font-size: 12px;">Assigned To :</label>
                    <select class="form-control assign-user select2" data-id="{{ $issue->id }}" name="assigned_to"
                            id="user_{{ $issue->id }}">
                        <option value="">Select...</option>
                            <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                        @foreach ($users as $id => $name)
                            @if ($assignedId == $id)
                                <option value="{{ $id }}" selected>{{ $name }}</option>
                            @else
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @else
                <div class="form-group">
                    @if ($issue->assignedUser)
                        <p>{{ $issue->assignedUser->name }}</p>
                    @else
                        <p>Unassigned</p>
                    @endif
                </div>
            @endif

            <div class="form-group mt-5">
                <label for="" style="font-size: 12px;">Lead :</label>
                @if ($issue->masterUser)
                    <p>{{ $issue->masterUser->name }}</p>
                @else
                    <p>N/A</p>
                @endif
            </div>

            <div class="form-group mt-5">
                @if ($issue->teamLead)
                    <label for="" style="font-size: 12px;">Team Lead :</label>
                    <p>{{ $issue->teamLead->name }}</p>
                @endif
            </div>

            <div class="form-group mt-5">
                @if ($issue->tester)
                    <label for="" style="font-size: 12px;">Tester :</label>
                    <p>{{ $issue->tester->name }}</p>
                @endif
            </div>
        </td>
        <td>
            @if ($issue->is_resolved)
                <strong>Done</strong>
            @else
                <select name="task_status" id="task_status" class="form-control"
                        onchange="resolveIssue(this,{{ $issue->id }})">
                    @foreach ($statusList as $status)
                        <option value="{{ $status }}" {{ !empty($issue->status) && $issue->status == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            @endif
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
                        <button class="btn btn-sm btn-image send-approximate-lead 66666" title="Send approximate"
                                onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})"
                                data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" />
                        </button>
                    </div>
                </div>
            </div>
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

            @if(!empty($issue->estimate_date) && $issue->start_date!='0000-00-00 00:00:00')
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
                <?php echo $issue->language; ?>

            <div class="dropdown dropleft">
                <!-- <a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" id="dropdownMenuLink{{$issue->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Actions
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{$issue->id}}">
                    <a class="dropdown-item" href="javascript:void(0);" onclick="funTaskInformationModal(this, '{{ $issue->id }}')">Task Information: Update</a>
                </div> -->

                <a title="Task Information: Update" class="btn btn-sm btn-image" href="javascript:void(0);"
                   onclick="funTaskInformationModal(this, '{{ $issue->id }}')"><i class="fa fa-info-circle"
                                                                                  aria-hidden="true"></i></a>

                <button class="btn btn-sm mt-2 create-task-document" title="Create document" data-id="{{$issue->id}}">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                </button>
                <button class="btn btn-sm mt-2 show-created-task-document" title="Show created document"
                        data-id="{{$issue->id}}">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>
                <button class="btn btn-sm mt-2 add-document-permission" data-task_id="{{$issue->id}}"
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

                <button class="btn btn-image expand-row-btn-lead" data-id="{{ $issue->id }}"><img src="/images/forward.png"></button>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <div id="collapse_{{ $issue->id }}" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="messageList" id="message_list_{{ $issue->id }}">
                        @foreach ($issue->messages as $message)
                            <p>
                                <strong>
                                        <?php echo ! empty($message->taskUser) ? 'To : '.$message->taskUser->name : ''; ?>
                                        <?php echo ! empty($message->user) ? 'From : '.$message->user->name : ''; ?>
                                    At {{ date('d-M-Y H:i:s', strtotime($message->created_at)) }}
                                </strong>
                            </p>
                            {!! nl2br($message->message) !!}
                            <hr />
                        @endforeach
                    </div>
                </div>
                <div class="panel-footer">
                    <textarea class="form-control send-message-textbox" data-id="{{ $issue->id }}"
                              id="send_message_{{ $issue->id }}" name="send_message_{{ $issue->id }}"></textarea>
                    <button type="submit" id="submit_message" class="btn btn-secondary ml-3 send-message"
                            data-id="{{ $issue->id }}" style="float: right;margin-top: 2%;">Submit
                    </button>
                </div>
            </div>
        </td>
    </tr>
@endif

<script>
  function funDevTaskInformationUpdatesTime(type, id) {
    if (type == 'start_date') {
      if (confirm('Are you sure, do you want to update?')) {
        // siteLoader(1);
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
          headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ route('development.update.start-date') }}",
          type: 'POST',
          data: {
            id: id,
            value: $('input[name="start_dates' + id + '"]').val(),
            estimatedEndDateTime: $('input[name="estimate_date' + id + '"]').val(),
          }
        }).done(function(res) {
          siteLoader(0);
          siteSuccessAlert(res);
        }).fail(function(err) {
          siteLoader(0);
          siteErrorAlert(err);
        });
      }
    } else if (type == 'estimate_date') {
      if (confirm('Are you sure, do you want to update?')) {
        // siteLoader(1);
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
          headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ route('development.update.estimate-date') }}",
          type: 'POST',
          data: {
            id: id,
            value: $('input[name="estimate_date' + id + '"]').val(),
            remark: mdl.find('input[name="remark"]').val(),
          }
        }).done(function(res) {
          siteLoader(0);
          siteSuccessAlert(res);
        }).fail(function(err) {
          siteLoader(0);
          siteErrorAlert(err);
        });
      }
    } else if (type == 'cost') {
      if (confirm('Are you sure, do you want to update?')) {
        // siteLoader(1);
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
          headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ route('development.save.cost') }}",
          type: 'POST',
          data: {
            id: currTaskInformationTaskId,
            value: mdl.find('input[name="cost"]').val(),
          }
        }).done(function(res) {
          siteLoader(0);
          siteSuccessAlert(res);
        }).fail(function(err) {
          siteLoader(0);
          siteErrorAlert(err);
        });
      }
    } else if (type == 'estimate_minutes') {
      if (confirm('Are you sure, do you want to update?')) {
        // siteLoader(1);
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
          headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ route('development.update.estimate-minutes') }}",
          type: 'POST',
          data: {
            issue_id: id,
            estimate_minutes: $('input[name="estimate_minutes' + id + '"]').val(),
            remark: mdl.find('textarea[name="remark"]').val(),
          }
        }).done(function(res) {
          // siteLoader(0);
          siteSuccessAlert(res);
        }).fail(function(err) {
          // siteLoader(0);
          siteErrorAlert(err);
        });
      }
    } else if (type == 'lead_estimate_time') {
      if (confirm('Are you sure, do you want to update?')) {
        siteLoader(1);
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
          headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ route('development.update.lead-estimate-minutes') }}",
                    type: 'POST',
                    data: {
                        issue_id: currTaskInformationTaskId,
                        lead_estimate_time: mdl.find('input[name="lead_estimate_time"]').val(),
                        remark: mdl.find('input[name="lead_remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        }
    }
</script>

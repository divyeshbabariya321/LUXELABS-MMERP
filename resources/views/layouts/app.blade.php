<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- this code is added to test Bing Webmaster -->
    <meta name="msvalidate.01" content="B588B77EEAA86F46291CA317C99B8D1C" />
    <!-- this code is added to test Bing Webmaster -->
    <!-- this code is added to test Google Webmaster -->
    <meta name="google-site-verification" content="VqTJz3T08L1QQTB0Jw2uN_3MWOHubzNK9caVEo7rBm8" />
    <!-- this code is added to test Google Webmaster -->

    @php
        if (isset($metaData->page_title) && $metaData->page_title != '') {
            $title = $metaData->page_title;
        } else {
            $title = trim($__env->yieldContent('title'));
        }
    @endphp

    @if (trim($__env->yieldContent('favicon')))
        <link rel="shortcut icon" type="image/png" href="/favicon/@yield ('favicon')" />
    @else
        <link rel="shortcut icon" type="image/png" href="/generate-favicon?title={{$title}}" />
    @endif

    <title>{!! $title !!}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaData->page_description ?? config('app.name') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/app.css') }} ">
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/richtext.min.css') }} ">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/sticky-notes.css') }} ">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/app-custom.css') }} ">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/custom.css') }} ">
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/bootstrap.min.css') }} ">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/css/bootstrap-slider.min.css">
    <link href="https://unpkg.com/tabulator-tables@4.0.5/dist/css/tabulator.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/global_custom.css') }} ">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/clockpicker@0.0.7/dist/bootstrap-clockpicker.min.css">

    @stack("component-styles")
    @yield("styles")
    @stack('link-css')
    @yield('link-css')

    <script src="{{siteJs('site.js')}}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{asset('js/readmore.js')}}" defer></script>
    <script src="{{asset('/js/generic.js')}}" defer></script>
    <script>
        let Laravel = {};

        Laravel.csrfToken = "{{csrf_token()}}";
        window.Laravel = Laravel;

        @if(Auth::user())
            window.userid = "{{Auth::user()->id}}";
            window.username = "{{Auth::user()->name}}";
            loggedinuser = "{{Auth::user()->id}}";
        @endif

        var BASE_URL = '{{ config('app.url ') }}';
        if (autoIframe == undefined || (autoIframe !== undefined && typeof autoIframe !== 'function')) {

            function autoIframe(frameId) {
                try {
                    frame = document.getElementById(frameId);
                    innerDoc = (frame.contentDocument) ?
                        frame.contentDocument : frame.contentWindow.document;
                    objToResize = (frame.style) ? frame.style : frame;
                    objToResize.height = innerDoc.body.scrollHeight + 10 + 'px';
                }
                catch (err) {
                    window.status = err.message;
                }
            }
        }
        
        window.REVERB_APP_KEY = "{{env('REVERB_APP_KEY')}}";
        window.REVERB_HOST = "{{env('REVERB_HOST')}}";
        window.REVERB_PORT = "{{env('REVERB_PORT')}}";
        window.REVERB_SCHEME = "{{env('REVERB_SCHEME')}}";
    </script>

    @stack("jquery")
    <script type="text/javascript" src="{{ mix('webpack-dist/js/app.js') }} "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript" src="https://media.twiliocdn.com/sdk/js/client/v1.14/twilio.min.js"></script>
    <script src="https://sdk.twilio.com/js/taskrouter/v1.21/taskrouter.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-notify.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/calls.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/custom.js') }} "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clockpicker@0.0.7/dist/bootstrap-clockpicker.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/echo.js') }}"></script>
    <script>
        initializeTwilio();
      const firebaseConfig = {
        apiKey: '{{config('firebase.FCM_API_KEY')}}',
        authDomain: '{{config('firebase.FCM_AUTH_DOMAIN')}}',
        projectId: '{{config('firebase.FCM_PROJECT_ID')}}',
        storageBucket: '{{config('firebase.FCM_STORAGE_BUCKET')}}',
        messagingSenderId: '{{config('firebase.FCM_MESSAGING_SENDER_ID')}}',
        appId: '{{config('firebase.FCM_APP_ID')}}',
        measurementId: '{{config('firebase.FCM_MEASUREMENT_ID')}}'
      };
      firebase.initializeApp(firebaseConfig);
      const messaging = firebase.messaging();
      messaging
        .requestPermission()
        .then(function() {
          return messaging.getToken();
        })
        .then(function(response) {
          $.ajaxSetup({
            headers: {
              "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
            }
          });
          $.ajax({
            url: '{{ route("store.token") }}',
            type: "POST",
            data: {
              token: response
            },
            dataType: "JSON",
            success: function(response) {
            },
            error: function(error) {
              console.error(error);
            }
          });
        }).catch(function(error) {
        alert(error);
      });
      messaging.onMessage(function(payload) {
        const title = payload.notification.title;
        const options = {
          body: payload.notification.body,
          icon: payload.notification.icon
        };
        new Notification(title, options);
      });

      window.Laravel = '{{!!json_encode(['csrfToken '=>csrf_token(),'user '=>['authenticated '=>auth()->check(),'id '=>auth()->check() ? auth()->user()->id : null,'name '=>auth()->check() ? auth()->user()-> name : null,]], JSON_INVALID_UTF8_IGNORE)!!}}';
      initializeTwilio();
      @auth
      const IS_ADMIN_USER = {{ $isAdmin ? 1 : 0 }};
      const LOGGED_USER_ID = {{ auth()->user()->id}};
        @endauth
    </script>

    @stack("styles")
</head>

<body>
@stack('modals')

@include('modals-partial.view-quick-email')
<div id="modal-container"></div>
<div id="modal-container-1"></div>
<div id="modal-container-2"></div>

<div class="notifications-container">
    <div class="stack-container stacked" id="leads-notification"></div>
    <div class="stack-container stacked" id="orders-notification"></div>
    <div class="stack-container stacked" id="tasks-notification"></div>
</div>

<div id="app">
    <nav class="navbar navbar-expand-lg navbar-light navbar-laravel py-1">
        <div class="container-fluid pr-md-0 pl-md-0 pl-xl-3 flex-xl-row flex-lg-column">
            <a class="navbar-brand pl-0" href="{{ url('/task') }}">
                {{ config('app.name', 'Laravel') }}
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>

            </button>


            <div class="collapse navbar-collapse pr-md-0 pl-xl-3 pl-md-0" id="navbarSupportedContent">
                <div class="menu-toogle-container">
                    <div class="primary-header">

                        @auth
                        <nav id="quick-sidebars">
                            <ul class="list-unstyled components mr-1">
                                
                                @if ($isAdmin)
                                    <li>
                                        <a href="javascript:void(0);" title="Global User Search" id="menu-user-search" type="button" class="quick-icon menu-user-search" style="padding: 0px 1px;">
                                            <span><i class="fa fa-search fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Event Alerts" id="event-alerts" type="button" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-clock-o fa-2x" aria-hidden="true"></i></span>
                                            <span class="event-alert-badge hide"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Create Event" id="create_event" type="button" data-toggle="modal" data-target="#createcalender" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-calendar fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Magento Cron Error Status Alerts" id="magento-cron-error-status-alerts" type="button" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-clock-o fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Zabbix issues" id="zabbix-issues" type="button" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Live laravel logs" id="live-laravel-logs" type="button" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                        
                        
                                        <a title="Monitor Status" type="button" class="quick-icon" id="website_Off_status" style="padding: 0px 1px;">
                                            <span>
                                                <i class="fa fa-desktop fa-2x" aria-hidden="true"></i>
                                                @if ($status)
                                                <span class="status-alert-badge"></span>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        
                                        <a title="Time-Doctor-logs" id="timer-alerts" type="button" class="quick-icon" style="padding: 0 1px;">
                                            <span>
                                                <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                                                @if($currentLogs)
                                                <span class="timer-alert-badge"></span>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="jenkins Build status" id="jenkins-build-status" type="button" class="quick-icon" style="padding: 0 1px;"><span><i class="fa fa-cog fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Search Password" type="button" class="quick-icon search-password" style="padding: 0 1px;"><span><i class="fa fa-key fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                        
                                        <a title="database backup monitoring" type="button" id="database-backup-monitoring" class="quick-icon" style="padding: 0 1px;"><span>
                                                <i class="fa fa-home fa-2x" aria-hidden="true"></i>
                                                @if(isset($backupListCount) && $backupListCount > 0)
                                                <span class="database-alert-badge"></span>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Code-Shortcuts" id="code-shortcuts" type="button" class="quick-icon" style="padding: 0 1px;"><span><i class="fa fa-cog fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Google-Drive-ScreenCast" type="button" class="quick-icon" id="google-drive-screen-cast" style="padding: 0 1px;"><span><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Upload Screencast/File" type="button" class="quick-icon upload-screencast-model" style="padding: 0 1px;"><span><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="User availability" type="button" class="quick-icon user-availability" style="padding: 0px 1px;">
                                            <span>
                                                <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Create Google Doc" type="button" class="quick-icon create-google-doc" style="padding: 0px 1px;"><span><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Search Google Doc" type="button" class="quick-icon search-google-doc" style="padding: 0px 1px;"><span><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Quick Dev Task" type="button" class="quick-icon menu-show-dev-task" style="padding: 0px 1px;"><span><i class="fa fa-tasks fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                        
                                    <li>
                                        <a title="Pr lists" type="button" id="repo_status_list" class="quick-icon" style="padding: 0px 1px;"><span><i class="fa fa-star fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                        
                                    @if (in_array($route_name, ["development.issue.index", "task.index", "development.summarylist", "chatbot.messages.list"]))
                                        <li>
                                            <a title="Time Estimations" type="button" class="quick-icon show-estimate-time" data-task="{{$route_name == "development.issue.index" ? "DEVTASK" : "TASK"}}">
                                                <span>
                                                    <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                                                </span>
                                                <span class="time-estimation-badge red-alert-badge hide"></span>
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a title="Task & Activity" type="button" class="quick-icon menu-show-task" style="padding: 0px 1px;"><span><i class="fa fa-tasks fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Create database" type="button" class="quick-icon menu-create-database" style="padding: 0px 1px;"><span><i class="fa fa-database fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Create Event" type="button" class="quick-icon create-event-shortcut" data-toggle="modal" data-target="#shortcut-user-event-model" style="padding: 0px 1px;"><span><i class="fa fa-calendar-o fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Create Resource" type="button" class="quick-icon create-resource" data-toggle="modal" data-target="#shortcut_addresource" style="padding: 0px 1px;"><span><i class="fa fa-file-image-o fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Sop Search" type="button" class="quick-icon menu-sop-search" style="padding: 0px 1px;"><span><i class="fa fa-search fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Email Search" type="button" class="quick-icon menu-email-search" style="padding: 0px 1px;"><span><i class="fa fa-envelope fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Create Documentation" type="button" id="create-documents" class="quick-icon" style="padding: 0px 1px;"><span><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="vouchers" type="button" class="quick-icon vochuers" id="add-vochuer" style="padding: 0px 1px;"><span>
                                                <i class="fa fa-barcode fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="System Request" type="button" class="system-request quick-icon" style="padding: 0px 1px;"><span>
                                                <i class="fa fa-sitemap fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Add Todo List" class="quick-icon todolist-request" href="#"><span><i class="fa fa-plus fa-2x"></i></span></a>
                                    </li>
                        
                                    <!-- <li>
                                        <a title="Todo List" class="quick-icon todolist-get" href="#"><span><i class="fa fa-list fa-2x"></i></span></a>
                                    </li> -->
                        
                                    <li>
                                        <a title="Todo List" type="button" class="quick-icon menu-todolist-get" style="padding: 0px 1px;">
                                            <span><i class="fa fa-list fa-2x"></i></span></a>
                                    </li>
                                    <li>
                        
                                        <a title="Permission Request" class="quick-icon permission-request" href="#">
                                            <span><i class="fa fa-reply fa-2x"></i>
                                                @if($permissionCount)
                                                <span class="permission-alert-badge"></span>
                                                @endif
                                            </span>
                                        </a>
                        
                                    </li>
                                    <li>
                                        <a href="{{ route('bank-statement.index') }}" title="Bank statements" class="quick-icon">
                                            <span><i class="fa fa-list fa-2x"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Quick User Event Notification" class="notification-button quick-icon" href="#"><span><i class="fa fa-bell fa-2x"></i></span></a>
                                    </li>
                                    <li>
                        
                                        <button type="button" class="btn btn-xs ParticipantsList" title="view Participants">
                                            <span><i class="fa fa-users fa-2x"></i>
                                                @if(isset($description) && $description > 0)
                                                <span class="description-alert-badge"></span>
                                                @endif
                                            </span>
                                        </button>
                                    </li>
                                    <li>
                                        <a title="Quick Instruction" class="instruction-button quick-icon" href="#"><span><i class="fa fa-question-circle fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Create Sticky Notes" class="sticky-notes quick-icon" id="sticky-notes" href="#"><span>
                                                <i class="fa fa-exclamation-circle fa-2x"></i></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Daily Planner" class="daily-planner-button quick-icon" target="__blank" href="{{ route('dailyplanner.index') }}">
                                            <span><i class="fa fa-calendar-check-o fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                        
                        
                                    <li>
                                        <a title="Chat" id="message-chat-data-box" class="quick-icon">
                                            <span class="p1 fa-stack has-badge" id="new_message" data-count="@if(isset($newMessageCount)) {{ $newMessageCount }} @else 0 @endif">
                                                <i class="fa fa-comment fa-2x xfa-inverse" data-count="4b"></i>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Create Meeting" class="create-zoom-meeting quick-icon" data-toggle="modal" data-target="#quick-zoomModal">
                                            <span><i class="fa fa-video-camera fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Create Task / Dev Task" class="create-easy-task quick-icon" data-toggle="modal" data-target="#quick-create-task">
                                            <span><i class="fa fa-tasks fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    @if ($isAdmin || (auth()->user() && Auth::user()->hasRole('HOD of CRM')))
                                        <li>
                                            <a title="Manual Payment" class="manual-payment-btn quick-icon">
                                                <span><i class="fa fa-money fa-2x" aria-hidden="true"></i></span>
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a title="Manual Request" class="manual-request-btn quick-icon">
                                            <span><i class="fa fa-credit-card-alt fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Auto Refresh" class="auto-refresh-run-btn quick-icon">
                                            <span><i class="fa fa-refresh fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Search Command" id="search-command" type="button" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-terminal fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Script Document Error Logs" id="script-document-logs" type="button" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-clock-o fa-2x" aria-hidden="true"></i></span>
                                            <span class="script-document-error-badge hide"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Assets Manager" id="assets-manager-listing" type="button" class="quick-icon" style="padding: 0px 1px;">
                                            <span><i class="fa fa-table fa-2x" aria-hidden="true"></i></span>
                                            <span class="script-document-error-badge hide"></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Create Vendor" data-toggle="modal" data-target="#vendorShortcutCreateModal" type="button" class="quick-icon create-vendor" style="padding: 0px 1px;" id="create-vendor-id">
                                            <span><i class="fa fa fa-user-plus fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                @endif
                        
                        
                                <li>
                                    <input type="text" id="searchField" placeholder="Search">
                                </li>
                                <li>
                                    <a title="Quick Appointment Request" data-toggle="modal" data-target="#quickRequestZoomModal" type="button" class="quick-icon user_search" style="padding: 0px 1px;">

                                        <span><i class="fa fa-paper-plane fa-2x" aria-hidden="true"></i></span>
                                    </a>
                                </li>
                        
                                @if($isAdmin)
                                    <li>
                                        <label class="switchAN">
                                            <input type="checkbox" id="availabilityToggle" @if(auth()->user()->is_online_flag==1)
                                            {{'checked'}}
                                            @endif>
                                            <span class="slider round"></span>
                                            <span class="text @if(auth()->user()->is_online_flag==1) {{'textLeft'}} @else {{'textRight'}} @endif" id="availabilityText">@if(auth()->user()->is_online_flag==1)
                                                {{'On'}}
                                                @else
                                                {{'Off'}}
                                                @endif</span>
                                        </label>
                                    </li>
                                    <li>
                                        <a title="Keyword Quick Replies" type="button" class="quick-icon keyword-quick-reply" style="padding: 0px 1px;">
                                            <span><i class="fa fa-font fa-2x" aria-hidden="true"></i></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Vendor Flow charts" type="button" class="quick-icon vendor-flowchart-header" style="padding: 0px 1px;"><span><i class="fa fa-line-chart fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Vendor Question Answer" type="button" class="quick-icon vendor-qa-header" style="padding: 0px 1px;"><span><i class="fa fa-question fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                    <li>
                                        <a title="Vendor Rating Question Answer" type="button" class="quick-icon vendor-rqa-header" style="padding: 0px 1px;"><span><i class="fa fa-question-circle-o fa-2x" aria-hidden="true"></i></span></a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                        @endif
                    </div>
                    <div class="secondary-header">
                        <!-- Left Side Of Navbar -->

                        <!-- Right Side Of Navbar -->

                        <ul id="navs" class="navbar-nav ml-auto pl-0"
                            style="display:flex;text-align: center;flex-grow: 1;gap:6px">

                            <!-- Authentication Links -->

                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Product <span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}

                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" data-toggle="dropdown" role="button"
                                           aria-haspopup="true" aria-expanded="false">Product Templates <span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item">
                                                <a class="dropdown-item" href="{{ route('templates') }}">Templates</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="dropdown-item"
                                                   href="{{ route('product.templates') }}">List</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="dropdown-item" href="{{ route('templates.type') }}">New
                                                    List</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="dropdown-item"
                                                   href="{{ route('product.index.image') }}">Processed
                                                    Image</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="dropdown-item" href="{{ route('product.templates.log') }}">Product
                                                    Template Log</a>
                                            </li>

                                        </ul>
                                    </li>

                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Listing<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Selection<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('productselection.index') }}">Selections Grid</a>
                                                    @if(auth()->user() && auth()->user()->checkPermission('productselection-create'))
                                                        <a class="dropdown-item"
                                                           href="{{ route('productselection.create') }}">Add New</a>
                                                    @endif
                                                    <a class="dropdown-item" href="{{ url('/excel-importer') }}">Excel
                                                        Import </a>
                                                    <a class="dropdown-item"
                                                       href="{{ url('/excel-importer/mapping') }}">Add
                                                        Mapping For Master </a>
                                                    <a class="dropdown-item"
                                                       href="{{ url('/excel-importer/tools-brand') }}">Add Mapping For
                                                        Excel</a>
                                                    <a class="dropdown-item" href="{{ url('/excel-importer/log') }}">Excel
                                                        Importer Log</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a class="dropdown-item"
                                                   href="{{ route('products.magentoConditionsCheck') }}">Mangento
                                                    condition
                                                    check</a>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a class="dropdown-item"
                                                   href="{{ route('products.magentoPushStatus') }}">Magento push
                                                    status</a>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Supervisor<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('productsupervisor.index') }}">Supervisor Grid</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Image Cropper<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('productimagecropper.index') }}">Image Cropper
                                                        Grid</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product.crop.approved') }}">Approved
                                                        Crop grid</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product.auto.cropped') }}">Crop
                                                        Approval Grid</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product.crop.issue.summary') }}">Crop
                                                        Issue Summary</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product.rejected.auto.cropped') }}">Crop-Rejected
                                                        Grid</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product.order.cropped.images') }}">Crop-Sequencer</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Images<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('google.search.product') }}">Google Image
                                                        Search</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('manual.image.upload') }}">Manual Image Upload</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Attribute<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    @if(auth()->user() && auth()->user()->checkPermission('productlister-list'))
                                                        <a class="dropdown-item"
                                                           href="{{ route('products.listing') }}?cropped=on">Attribute
                                                            edit
                                                            page</a>
                                                    @endif
                                                    <a class="dropdown-item"
                                                       href="{{ route('products.push.conditions') }}">Magento product
                                                        push
                                                        conditions</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ProductController::class, 'approvedListing']) }}?cropped=on">Approved
                                                        listing</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ProductController::class, 'approvedListing']) }}?cropped=on&status_id=2">Listings
                                                        awaiting scraping</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ProductController::class, 'approvedListing']) }}?cropped=on&status_id=13">Listings
                                                        unable to scrape</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ProductController::class, 'showRejectedListedProducts']) }}">Rejected
                                                        Listings</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\AttributeReplacementController::class, 'index']) }}">Attribute
                                                        Replacement</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\UnknownAttributeProductController::class, 'index']) }}">Incorrect
                                                        Attributes</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\CropRejectedController::class, 'index']) }}">Crop
                                                        Rejected<br>Final Approval Images</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Approver<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('productapprover.index') }}">Approver Grid</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>In Stock<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('productinventory.instock') }}">In Stock</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>In Delivered<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('productinventory.indelivered') }}">In
                                                        Delivered</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Inventory<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('productinventory.index') }}">Inventory Grid</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('productinventory.list') }}">Inventory List</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('productinventory.inventory-list-new') }}">New
                                                        Product Inventory List</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('productinventory.inventory-list') }}">Inventory
                                                        Data</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product-inventory.new') }}">New
                                                        Inventory List</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('productinventory.out-of-stock') }}">Sold Out
                                                        Products</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('listing.history.index') }}">Product Listing
                                                        history</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product.category.index.list') }}">Product
                                                        Category</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('product.color.index.list') }}">Product Color
                                                        history</a>
                                                </ul>
                                            </li>
                                            @if($isAdmin)

                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a id="navbarDropdown" class="" href="#" role="button"
                                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                       v-pre>Quick Sell<span class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="navbarDropdown">
                                                        <a class="dropdown-item" href="{{ route('quicksell.index') }}">Quick
                                                            Sell</a>

                                                    </ul>
                                                </li>
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a class="dropdown-item" href="/drafted-products">Quick Sell
                                                        List</a>
                                                </li>
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a class="dropdown-item" href="{{ route('stock.index') }}">Inward
                                                        Stock</a>
                                                </li>
                                            @endif
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Scraping<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">

                                                    <a class="dropdown-item"
                                                       href="{{ url('scrap/statistics') }}">Statistics</a>
                                                    <a class="dropdown-item"
                                                       href="{{ url('scrap/statistics/server-history') }}">Server
                                                        History</a>
                                                    <a class="dropdown-item"
                                                       href="{{ url('scrap/generic-scraper') }}">Generic Supplier
                                                        Scraper</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\CategoryController::class, 'brandMinMaxPricing']) }}">Min/Max
                                                        Pricing</a>
                                                    <a class="dropdown-item" href="{{ route('supplier.count') }}">Supplier
                                                        Category Count</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('supplier.brand.count') }}">Supplier Brand
                                                        Count</a>
                                                    <a class="dropdown-item"
                                                       href="{{ url('price-comparison-scraper') }}">Price comparison</a>
                                                    <a class="dropdown-item"
                                                       href="{{ url('price-comparison-scraper/sites') }}">Price comparison sites</a>
                                                    <a class="dropdown-item"
                                                       href="{{ url('scrap/servers/statistics') }}">Scrap server
                                                        statistics</a>

                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>SKU<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\Logging\LogScraperController::class, 'logSKU']) }}">SKU
                                                        log</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\Logging\LogScraperController::class, 'logSKUErrors']) }}">SKU
                                                        warnings/errors</a>
                                                    <a class="dropdown-item" href="{{ route('sku-format.index') }}">SKU
                                                        Format</a>
                                                    <a class="dropdown-item" href="{{ route('sku.color-codes') }}">SKU
                                                        Color
                                                        Codes</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('google.search.product') }}">Search
                                                    Products by Text</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('google.search.multiple') }}">Multiple products by
                                                    Text</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('google.search.image') }}">Search
                                                    Products by Image</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Purchase<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('purchase.index') }}">Purchase</a>
                                                <a class="dropdown-item" href="{{ route('purchase.grid') }}">Purchase
                                                    Grid</a>
                                                <a class="dropdown-item" href="{{ route('purchase.calendar') }}">Purchase
                                                    Calendar</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('purchase.grid', 'canceled-refunded') }}">Cancel/Refund
                                                    Grid</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('purchase.grid', 'ordered') }}">Ordered Grid</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('purchase.grid', 'delivered') }}">Delivered Grid</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('purchase.grid', 'non_ordered') }}">Non Ordered
                                                    Grid</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('purchaseproductorders.list') }}">Purchase Product
                                                    Orders</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('list.magento.product-push-information') }}">Product
                                                    information update </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Supplier<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('supplier.index') }}">Supplier
                                                    List</a></a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('supplier.product.history') }}">Supplier Product
                                                    History</a></a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('supplier/category/permission') }}">Supplier Category
                                                    <br> Permission</a></a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('supplier.discount.files') }}">Supplier Discount
                                                    Files</a></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Scraping<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('scrapper-category-map.index')}}">Node Category Maping</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\CodeShortcutController::class, 'index']) }}">
                                                    Node Code Shortcut</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ProductController::class, 'productStats']) }}">Product
                                                    Statistics</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ProductController::class, 'showAutoRejectedProducts']) }}">Auto
                                                    Reject Statistics</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ListingPaymentsController::class, 'index']) }}">Product
                                                    Listing
                                                    Payments</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ScrapStatisticsController::class, 'index']) }}">Scrap
                                                    Statistics</a>
                                                <a class="dropdown-item" href="{{ route('statistics.quick') }}">Quick
                                                    Scrap
                                                    Statistics</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ScrapController::class, 'scrapedUrls']) }}">Scrap
                                                    Urls</a>
                                                <a class="dropdown-item" href="{{ route('scrap.activity') }}">Scrap
                                                    activity</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('scrap.scrap_server_status') }}">Scrapper Server
                                                    Status</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ScrapController::class, 'showProductStat']) }}">Products
                                                    Scrapped</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\SalesItemController::class, 'index']) }}">Sale
                                                    Items</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\DesignerController::class, 'index']) }}">Designer
                                                    List</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\GmailDataController::class, 'index']) }}">Gmail
                                                    Inbox</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ScrapController::class, 'index']) }}">Google
                                                    Images</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\GoogleSearchImageController::class, 'searchImageList']) }}">Image
                                                    Search By Google</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\SocialTagsController::class, 'index']) }}">Social
                                                    Tags</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\DubbizleController::class, 'index']) }}">Dubzzle</a>
                                                <a class="dropdown-item" href="{{ route('log-scraper.index') }}">Scraper
                                                    log</a>
                                                <a class="dropdown-item" href="{{ route('log-scraper.api') }}">Scraper
                                                    Api
                                                    log</a>
                                                <a class="dropdown-item" href="{{ route('scrap-brand') }}">Scrap
                                                    Brand</a>
                                                <a class="dropdown-item" href="{{ url('scrap/log/list') }}">Scrapper
                                                    Task
                                                    Logs</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('price-comparison-scraper/logs') }}">Price comparison logs</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('scrap/development/monitoring') }}">Scrap dev monitoring</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('scrapper-category-map') }}">Scrapper category map</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Crop Reference<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\CroppedImageReferenceController::class, 'grid']) }}">Crop
                                                    Reference Grid</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Magento<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\Logging\LogListMagentoController::class, 'index']) }}">Log
                                                    List
                                                    Magento</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="/magento/status">Order Status Mapping</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="/languages">Language</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('logging.magento.product_push_journey') }}">Product
                                                    Push
                                                    Journey</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Logs<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ProductController::class, 'productScrapLog']) }}">Status
                                                    Logs</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ScrapLogsController::class, 'index']) }}">Scrap
                                                    Logs</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\LaravelLogController::class, 'index']) }}">Laravel
                                                    Log</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('api-log-list') }}">Laravel API
                                                    Log</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\LaravelLogController::class, 'liveLogs']) }}">Live
                                                    Laravel
                                                    Log</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\LaravelLogController::class, 'scraperLiveLogs']) }}">Live
                                                    Scraper
                                                    Log</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('social-webhook-log.index') }}">Social Webhook Log</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('website.file.list.log') }}">Magento
                                                    Logs</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('website.log.view') }}">Magento
                                                    Logs
                                                    View</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item"
                                           href="{{route('products.status-history')}}">Product Status History</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item"
                                           href="{{action([\App\Http\Controllers\ProductController::class, 'productDescription'])}}">Product
                                            Description</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('products.product-translation')}}">Product
                                            translate</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('products.product-assign')}}">Assign
                                            Products</a>
                                    </li>

                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item"
                                           href="{{route('products.listing.approved.images')}}/images">Final Apporval
                                            Images</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('customer.charity')}}">Charity
                                            Products</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('google.vision')}}">Google Vision Data</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{route('products.status-history')}}">Product Status History</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">CRM <span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}
                                    <li class="nav-item">
                                        <a class="dropdown-item" target="_blank" href="{{ route('logs.index') }}">Image
                                            Logs</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" target="_blank"
                                           href="{{ route('order.call-management') }}">Call Management</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" target="_blank" href="/web-message">Communication</a>
                                        <a class="dropdown-item" href="{{route('translation.list')}}">Translations</a>
                                        <a class="dropdown-item" href="{{route('translation.log')}}">Translations
                                            Logs</a>
                                        <a class="dropdown-item" href="{{route('pushfcmnotification.list')}}">FCM
                                            Notifications</a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" data-toggle="dropdown" role="button"
                                           aria-haspopup="true" aria-expanded="false">Scheduled Flows<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('flow.index') }}">Flows</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('flow.schedule-emails') }}">Scheduled Emails</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('flow.schedule-messages') }}">Scheduled Messages</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false">Customers<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ url('/erp-customer') }}">Customers -
                                                    NEW</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('customer.index') }}?type=unread">Customers -
                                                    unread</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('customer.index') }}?type=unapproved">Customers -
                                                    unapproved</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('customer.index') }}?type=Refund+to+be+processed">Customers
                                                    - refund</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\VisitorController::class, 'index']) }}">Livechat
                                                    Visitor Logs</a>
                                                <a class="dropdown-item" href="{{ url('livechat/setting') }}">Livechat
                                                    Setting</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ProductController::class, 'attachedImageGrid']) }}">Attach
                                                    Images</a>
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ProductController::class, 'suggestedProducts']) }}">Sent
                                                    Images</a>
                                                <a class="dropdown-item" href="{{ route('chat.dndList') }}">DND
                                                    Manage</a>
                                                <a class="dropdown-item" href="{{ url('customer/credit') }}">Customer
                                                    Credit</a>
                                                <a class="dropdown-item" href="{{ url('chatbot-message-log') }}">Chatbot
                                                    Message Log</a>
                                                <a class="dropdown-item" href="{{ url('watson-journey') }}">Watson
                                                    Journey</a>
                                                <a class="dropdown-item" href="{{ url('customers/accounts') }}">Store
                                                    website customer</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('product.customer-reviews') }}">Customer Reviews</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('customer.priority.points') }}">Customer Priority</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('customer.get.priority.range.points') }}">Customer
                                                    Range
                                                    Priority Point</a>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Cold Leads<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ColdLeadsController::class, 'index']) }}?via=hashtags">Via
                                                        Hashtags</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ColdLeadsController::class, 'showImportedColdLeads']) }}">Imported
                                                        Cold leads</a>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Instructions<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Instructions<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('instruction.index') }}">Instructions</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('instruction.list') }}">Instructions List</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\KeywordInstructionController::class, 'index']) }}">Instruction
                                                        Keyword Instructions</a>
                                                    <a class="dropdown-item" href="/instruction/quick-instruction">Quick
                                                        instructions</a>
                                                    <a class="dropdown-item"
                                                       href="/instruction/quick-instruction?type=price">Quick
                                                        instructions
                                                        (price)</a>
                                                    <a class="dropdown-item"
                                                       href="/instruction/quick-instruction?type=image">Quick
                                                        instructions
                                                        (attach)</a>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Referral System<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Referral Programs<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('referralprograms.list') }}">List Referral
                                                        Programs</a>
                                                    <a class="dropdown-item" href="{{ route('referralprograms.add') }}">Add
                                                        Referral Programs</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Friend Referral<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item" href="{{ route('referfriend.list') }}">List
                                                        Friend Referral</a>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Leads<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="{{ route('leads.index') }}">Leads</a>
                                            <a class="dropdown-item"
                                               href="{{ action([\App\Http\Controllers\LeadsController::class, 'erpLeads']) }}">Leads
                                                (new)</a>
                                            <a class="dropdown-item"
                                               href="{{ action([\App\Http\Controllers\LeadsController::class, 'erpLeadsHistory']) }}">Leads
                                                History</a>
                                            <a class="dropdown-item" href="{{ route('lead-queue.approve') }}">Leads
                                                Queue
                                                Approval</a>
                                            <a class="dropdown-item" href="{{ route('lead-queue.index') }}">Leads Queue
                                                (Approved)</a>
                                            <a class="dropdown-item" href="{{ route('leads.create') }}">Add new lead</a>
                                            <a class="dropdown-item" href="{{ route('leads.image.grid') }}">Leads Image
                                                grid</a>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Refunds<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('refund.index') }}">Refunds</a>
                                            </li>
                                        </ul>

                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('quick-replies') }}">Quick Replies</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('quick.customer.index') }}">Quick
                                            Customer</a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Orders<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Orders<span class="caret"></span></a>

                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('order.index') }}">Orders</a>
                                                    <a class="dropdown-item" href="{{ route('order.create') }}">Add
                                                        Order</a>
                                                    <a class="dropdown-item" href="{{ route('order.products') }}">Order
                                                        Product List</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('return-exchange.list') }}">Return-Exchange</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('return-exchange.status') }}">Return-Exchange
                                                        Status</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('order.status.messages') }}">Order Status
                                                        Messages</a>
                                                    <a class="dropdown-item" href="{{ route('lead-order.index') }}">Lead
                                                        order</a>
                                                    <a class="dropdown-item" href="{{ url('order/charity-order') }}">Charity
                                                        order</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('order.get.email.send.journey.logs') }}">Order
                                                        email
                                                        journey</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('order.get.order.journey') }}">Order
                                                        journey</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class=""
                                                   href="{{ action([\App\Http\Controllers\OrderController::class, 'viewAllInvoices']) }}"
                                                   role="button"
                                                   aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Invoices<span></span></a>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a class="" href="{{ route('store-website.all.status') }}" role="button"
                                                   aria-haspopup="true" aria-expanded="false">Magento order
                                                    status<span></span></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Customer<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('complaint.index') }}">Customer
                                                    Complaints</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a href="{{ route('livechat.get.chats') }}">Live Chat</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a href="{{ route('livechat.get.tickets') }}">Live Chat Tickets</a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Missed<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('order.missed-calls') }}">Missed
                                                    Calls List</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Call<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('order.calls-history') }}">Call
                                                    history</a>

                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Private<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('stock.private.viewing') }}">Private
                                                    Viewing</a>

                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Bulk Customer Replies<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item"
                                               href="{{ action([\App\Http\Controllers\BulkCustomerRepliesController::class, 'index']) }}">Bulk
                                                Messages</a>
                                            <a class="dropdown-item"
                                               href="{{ action([\App\Http\Controllers\CustomerCategoryController::class, 'index']) }}">Categories</a>
                                            <a class="dropdown-item"
                                               href="{{ action([\App\Http\Controllers\KeywordToCategoryController::class, 'index']) }}">Keywords</a>

                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Delivery<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('deliveryapproval.index') }}">Delivery Approvals</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Broadcast<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('broadcast.index') }}">Broadcast
                                                    Grid</a>
                                                <a class="dropdown-item" href="{{ route('broadcast.images') }}">Broadcast
                                                    Images</a>
                                                <a class="dropdown-item" href="{{ route('broadcast.calendar') }}">Broadcast
                                                    Calender</a>
                                                <a class="dropdown-item" href="/marketing/instagram-broadcast">Instagram
                                                    Broadcast</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Marketing<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('whatsapp.config.index') }}">WhatsApp Config</a>
                                                <a class="dropdown-item" href="/marketing/accounts/instagram">Instagram
                                                    Config</a>
                                                <a class="dropdown-item" href="/marketing/accounts/facebook">Facebook
                                                    Config</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('platforms.index') }}">Platforms</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('broadcasts.index') }}">BroadCast</a>
                                                <a class="dropdown-item" href="/marketing/services">Mailing Service</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('mailingList') }}">Mailinglist</a>
                                                <a class="dropdown-item" href="{{ route('mailingList.log') }}">Mailinglist
                                                    Log</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('mailingList.flowlog') }}">Mailinglist Flow Log</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('mailingList.customerlog') }}">Customer Mailinglist
                                                    Log</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('mailingList-template') }}">Mailinglist Templates</a>
                                                <a class="dropdown-item"
                                                   href="{{ route('mailingList-emails') }}">Mailinglist Emails</a>
                                                <a class="dropdown-item" href="/mail-templates/mailables">Mailables</a>
                                                <a class="dropdown-item" href="{{ route('emailleads') }}">Email
                                                    Leads</a>
                                                <a class="dropdown-item" href="{{ url('twillio')}}">Messages</a>
                                                <a class="dropdown-item" href="{{ url('email-data-extraction')}}">Auto
                                                    Email
                                                    Records</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Checkout<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('coupons.index') }}">Coupons</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('negative.coupon.response') }}">Negative Coupons
                                                    Response</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a id="navbarDropdown" class="" href="{{ route('keywordassign.index') }}"
                                           role="button">Keyword Assign</a>
                                    </li>
                                    {{-- START - Purpose : Add new Menu Keyword Response Logs - DEVTASK-4233 --}}
                                    <li class="nav-item">
                                        <a id="navbarDropdown" class="" href="{{ route('keywordreponse.logs') }}"
                                           role="button">Keyword Response Logs</a>
                                    </li>
                                    {{-- END - DEVTASK-4233 --}}
                                    <li class="nav-item">
                                        <a id="navbarDropdown" class="" href="{{ route('purchase-product.index') }}"
                                           role="button">Purchase</a>
                                    </li>
                                    <li class="nav-item">
                                        <a id="navbarDropdown" class="" href="{{ route('status-mapping.index') }}"
                                           role="button">Status Mappings</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Vendor <span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('vendors.index') }}">Vendor Info</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('vendor-category.index') }}">Vendor
                                            Category</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('vendors.product.index') }}">Product
                                            Info</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('developer.vendor.form') }}">Vendor
                                            Form</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('developer.supplier.form') }}">Supplier
                                            Form</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('vendor-category.permission') }}">Vendor
                                            Category Permission</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('vendor.cv.index') }}">Vendors CV</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('meetings.all.data') }}">Zoom
                                            Meetings</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('list.all-participants') }}">Zoom
                                            Participants</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Users <span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>User Management<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('users.index') }}">List
                                                    Users</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('users.create') }}">Add New</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('userlogs.index') }}">User
                                                    Logs</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('users.login.index') }}">User
                                                    Logins</a>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Roles<span class="caret"></span></a>

                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">

                                                    <a class="dropdown-item" href="{{ route('roles.index') }}">List
                                                        Roles</a>
                                                    <a class="dropdown-item" href="{{ route('roles.create') }}">Add
                                                        New</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Permissions<span class="caret"></span></a>

                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item" href="{{ route('permissions.index') }}">List
                                                        Permissions</a>
                                                    <a class="dropdown-item" href="{{ route('permissions.create') }}">Add
                                                        New</a>
                                                    <a class="dropdown-item" href="{{ route('permissions.users') }}">User
                                                        Permission List</a>
                                                    <a class="dropdown-item" href="{{ route('users.login.ips') }}">User
                                                        Login IP(s)</a>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('user-management.index') }}">New
                                                    Management</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ url('api/documentation') }}">API
                                                    Documentation</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('user.get-feedback-table-data')}}">User Feedback</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('user-management.user-schedules.index')}}">User
                                                    Schedules</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('user-management.user-delivered.index')}}">User
                                                    Delivered</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('user-management.user-access-listing')}}">User Access
                                                    Listing</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Activity<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('activity') }}">View</a>
                                            </li>


                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('graph_user') }}">User Graph</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('benchmark.create') }}">Add
                                                    Benchmark</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\ProductController::class, 'showListigByUsers']) }}">User
                                                    Product
                                                    Assignment</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/calendar">Calendar</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/event">Event</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/all/events">All Event list</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Platforms <span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item"
                                           href="{{ action([\App\Http\Controllers\PreAccountController::class, 'index']) }}">Other
                                            Email Accounts
                                        </a>
                                    </li>
                                    @if($isAdmin)
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>Instagram<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramPostsController::class, 'grid']) }}">Instagram
                                                        Posts
                                                        (Grid)</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramPostsController::class, 'index']) }}">Instagram
                                                        Posts</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\HashtagController::class, 'influencer']) }}">Influencers</a>
                                                    <a class="dropdown-item" href="/instagram/hashtag/comments/">Hashtag
                                                        Comments</a>
                                                    <a class="dropdown-item" href="/instagram/direct-message">Direct
                                                        Message</a>
                                                    <a class="dropdown-item" href="/instagram/post">Posts</a>
                                                    <a class="dropdown-item" href="/instagram/post/create">Create
                                                        Post</a>
                                                    <a class="dropdown-item" href="/instagram/direct-message">Media</a>
                                                    <a class="dropdown-item" href="/instagram/users">Get User Post</a>
                                                </li>

                                                <hr />

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramController::class, 'index']) }}">Dashboard</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramController::class, 'accounts']) }}">Accounts</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('instagram/hashtag') }}">Hashtags</a>
                                                </li>


                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\HashtagController::class, 'showGrid'], 'sololuxury') }}">Hashtag
                                                        monitoring & manual Commenting</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\HashtagController::class, 'showNotification']) }}">Recent
                                                        Comments (Notifications)</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramController::class, 'showPosts']) }}">All
                                                        Posts</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\TargetLocationController::class, 'index']) }}">Target
                                                        Location</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\KeywordsController::class, 'index']) }}">Keywords
                                                        For
                                                        comments</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\HashtagController::class, 'showProcessedComments']) }}">Processed
                                                        Comments</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\CompetitorPageController::class, 'index']) }}?via=instagram">All
                                                        Competitors On Instagram</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramAutoCommentsController::class, 'index']) }}">Quick
                                                        Reply</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ReplyController::class, 'replyList']) }}">Quick
                                                        Reply List</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\UsersAutoCommentHistoriesController::class, 'index']) }}">Bulk
                                                        Commenting</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\AutoCommentHistoryController::class, 'index']) }}">Auto
                                                        Comments
                                                        Statistics</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramProfileController::class, 'index']) }}">Customers
                                                        followers</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramProfileController::class, 'edit'], 1) }}">#tags
                                                        Used by
                                                        top customers.</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\InstagramController::class, 'accounts']) }}">Accounts</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad
                                                        Schedules</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('social.ad.create')}}">Create
                                                        New
                                                        Ad</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('social.ad.adset.create')}}">Create
                                                        New Adset</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{route('social.ad.campaign.create')}}">Create New
                                                        Campaign </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('social.get-post.page')}}">See
                                                        Posts</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('social.post.page')}}">Post
                                                        to
                                                        Page</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('social.report')}}">Ad
                                                        Reports</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{route('social.adCreative.report')}}">Ad
                                                        Creative Reports</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('complaint.index') }}">Customer
                                                        Complaints</a>
                                                </li>

                                            </ul>
                                        </li>
                                    @endif

                                    @if($isAdmin)
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>LiveChat, Inc.<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\VisitorController::class, 'index']) }}">LiveChat
                                                        Visitor Log</a>
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\LiveChatController::class, 'setting']) }}">LiveChat
                                                        Settings</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif

                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Facebook<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\InstagramController::class, 'showImagesToBePosted']) }}">Create
                                                    Post</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\InstagramController::class, 'showSchedules']) }}">Schedule
                                                    A
                                                    Post</a>
                                            </li>

                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Facebook<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\FacebookController::class, 'index']) }}">Facebook
                                                        Post</a>
                                                </ul>
                                            </li>

                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Facebook Groups<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\FacebookController::class, 'show'], 'group') }}">Facebook
                                                        Groups</a>
                                                </ul>
                                            </li>

                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Facebook Brand Fan Page<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\FacebookController::class, 'show'], 'brand') }}">Facebook
                                                        Brand Fan Page</a>
                                                </ul>
                                            </li>

                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>All Adds<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <a class="dropdown-item" href="{{route('social.get-post.page')}}">See
                                                        Posts</a>
                                                    <a class="dropdown-item" href="{{route('social.post.page')}}">Post
                                                        On
                                                        pgae</a>
                                                    <a class="dropdown-item" href="{{route('social.report')}}">Ad
                                                        report</a>
                                                    <a class="dropdown-item"
                                                       href="{{route('social.adCreative.report')}}">Ad
                                                        Creative Reports</a>
                                                    <a class="dropdown-item"
                                                       href="{{route('social.ad.campaign.create')}}">Create New
                                                        Campaign</a>
                                                    <a class="dropdown-item"
                                                       href="{{route('social.ad.adset.create')}}">Create New adset</a>
                                                    <a class="dropdown-item" href="{{route('social.ad.create')}}">Create
                                                        New
                                                        ad</a>
                                                    <a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad
                                                        Schedule</a>
                                                </ul>

                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\FacebookPostController::class, 'index']) }}">Facebook
                                                    Posts</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Sitejabber<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\SitejabberQAController::class, 'accounts']) }}">Account</a>
                                            </li>

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ action([\App\Http\Controllers\QuickReplyController::class, 'index']) }}">Quick
                                                    Reply</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Pinterest<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('pinterest.accounts') }}">Accounts</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Images<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('image.grid') }}">Lifestyle
                                                    Image
                                                    Grid</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('image.grid.new') }}">Lifestyle
                                                    Image Grid New</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{ route('image.grid.approved') }}">Final
                                                    Images</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('image.grid.final.approval') }}">Final Approval</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('review.index') }}">Reviews
                                        </a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Bloggers<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('blogger.index')}}">Bloggers</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="seoMenu" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre="">SEO<span class="caret">
                                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="seoMenu">
                                                    <li class="nav-item dropdown dropdown-submenu">
                                                        @if(auth()->user() && auth()->user()->hasRole(['Admin', 'user', 'Seo Head']))
                                                            <a class="dropdown-item"
                                                               href="{{ route('seo.content.index') }}">Content</a>
                                                        @endif
                                                        <a class="dropdown-item"
                                                           href="{{ route('seo.company.index') }}">Company</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\BackLinkController::class, 'displayBackLinkDetails']) }}">Back
                                                            Link Details</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\BrokenLinkCheckerController::class, 'displayBrokenLinkDetails']) }}">Broken
                                                            Link Details</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\AnalyticsController::class, 'showData']) }}">New Google
                                                            Analytics</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\AnalyticsController::class, 'customerBehaviourByPage']) }}">Customer
                                                            Behaviour By Page</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\SERankingController::class, 'getSites']) }}">SE
                                                            Ranking</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\ArticleController::class, 'index']) }}">Article
                                                            Approval</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\ProductController::class, 'getSupplierScrappingInfo']) }}">Supplier
                                                            Scrapping Info</a>
                                                        <a class="dropdown-item"
                                                           href="{{ action([\App\Http\Controllers\NewDevTaskController::class, 'index']) }}">New Dev Task
                                                            Planner</a>
                                                        <a class="dropdown-item" href="{{ route('seo-tool') }}">Semrush
                                                            details</a>
                                                    </li>
                                                </ul>
                                    </li>

                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Chatbot<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('chatbot.question.list')}}">Intents
                                                    /
                                                    Entities</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('chatbot.dialog.list')}}">Dialog</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('chatbot.dialog-grid.list')}}">Dialog
                                                    Grid</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('chatbot.mostUsedWords')}}">Most
                                                    used
                                                    words</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('chatbot.mostUsedPhrases')}}">Most
                                                    used phrases</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('chatbot.mostUsedPhrasesDeleted')}}">Most used phrases
                                                    Updated</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('chatbot.analytics.list')}}">Analytics</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('chatbot.messages.list')}}">Messages</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('chatbot.messages.list')}}/elastic">Messages
                                                    Elasticsearch</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('chatbot.messages.logs')}}">Logs</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('chatbot-simulator')}}">Simulator</a>
                                            </li>
                                        </ul>
                                    </li>


                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>App Store<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('appconnect.app-users')}}">Usage</a>
                                                <a class="dropdown-item"
                                                   href="{{route('appconnect.app-sales')}}">Sales</a>
                                                <a class="dropdown-item"
                                                   href="{{route('appconnect.app-sub')}}">Subscription</a>
                                                <a class="dropdown-item"
                                                   href="{{route('appconnect.app-ads')}}">Ads</a>
                                                <a class="dropdown-item"
                                                   href="{{route('appconnect.app-rate')}}">Ratings</a>
                                                <a class="dropdown-item"
                                                   href="{{route('appconnect.app-pay')}}">Payments</a>
                                            </li>
                                        </ul>
                                    </li>


                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Google<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Search<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('google.search.keyword')}}">Keywords</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('google.search.results')}}">Search Results</a>
                                                    </li>

                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Affiliate<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{route('affiliates.list')}}">Manual
                                                            Affiliates</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('google.affiliate.keyword')}}">Keywords</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('google.affiliate.results')}}">Search
                                                            Results</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Developer API<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('google.developer-api.crash')}}">Crash
                                                            Report</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('google.developer-api.anr')}}">ANR Report</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('google.developer-api.logs')}}">Logs</a>
                                                    </li>

                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="item" href="{{ route('google.billing.index') }}">Google Billing Master</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('google-drive.new') }}">Google Drive</a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Google Web Master<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('googlewebmaster.index')}}">Sites</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Bing Web Master<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('bingwebmaster.index')}}">Sites</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a class="dropdown-item" href="{{ route('googleadsaccount.index') }}">Google
                                            AdWords</a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown"
                                            style="width: fit-content !important;">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('googleadsaccount.index')}}">Google
                                                    AdWords Account</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('googlecampaigns.campaignslist')}}">Google Campaign</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('googleadsaccount.adsgroupslist')}}">Google Ads
                                                    groups</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('googlecampaigns.adslist')}}">Google
                                                    Ads</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('googlecampaigns.displayads')}}">Google
                                                    Responsive Display Ads</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('googleadsaccount.appadlist')}}">Google
                                                    App Ads</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('googleadreport.index')}}">Google
                                                    Ads Report</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item" href="{{route('googleadslogs.index')}}">Google
                                                    Ads Logs</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('digital-marketing.index') }}">Social
                                            Digital Marketing
                                        </a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Virtualmin<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('virtualmin.domains')}}">Domains</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item" href="{{ route('logging.flow.log') }}">Flow Log
                                        </a>
                                    </li>
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false" v-pre>Affiliate Marketing<span
                                                    class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <li class="nav-item dropdown">
                                                <a class="dropdown-item"
                                                   href="{{route('affiliate-marketing.providerAccounts')}}">Providers
                                                    Accounts</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Social <span class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    {{-- Sub Menu Product --}}
                                    <li class="nav-item dropdown">
                                        <a href="{{route('social.config.index')}}">Social Config</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a href="{{route('social.post.grid')}}">Social Posts Grid</a>
                                    </li>
                                    @if($isAdmin)
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>Instagram<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="/instagram/post">Posts</a>
                                                    <a class="dropdown-item" href="/instagram/post/create">Create
                                                        Post</a>
                                                    <a class="dropdown-item" href="/instagram/direct-message">Media</a>
                                                    <a class="dropdown-item" href="/instagram/direct">Direct</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>Blog<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">

                                                    <a class="dropdown-item" href="/blog/list">Blog</a>
                                                    <a class="dropdown-item" href="/blog/history/list">View History</a>
                                                </li>
                                            </ul>
                                        </li>


                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>Youtube<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="/youtube/add-chanel">Create
                                                        Channel</a>


                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a href="{{ route('social.direct-message') }}">Direct Messsage</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a href="{{route('social.config.index')}}">Social Config</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a href="{{route('social.adcreative.index')}}">Social Ad Creative</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a href="{{route('social.ad.index')}}">Social Ads</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a href="{{ route('chatgpt.index') }}">Chat GPT</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            @if($isAdmin)
                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                       data-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false" v-pre="">Multi Site<span
                                                class="caret"></span></a>

                                    <ul class="dropdown-menu multi-level">
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('products/pushproductlist') }}">
                                                Push Product List</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('magento-setting-revision-history.index') }}">Magento
                                                Setting Revision Histories
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('magento-cron-data') }}">Magento Cron
                                                Data</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('magento-productt-errors.index') }}">Magento product push
                                                errors</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('magento_module_categories.index') }}">Magento Module
                                                Category</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('magento_module_types.index') }}">Magento Module Type</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('magento_modules.index') }}">Magento
                                                Modules</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('magento_module_listing') }}">Magento
                                                Modules Listing</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('magento_frontend_listing') }}">Magento
                                                Frontend
                                                Documentation</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('magento.backend.listing') }}">Magento
                                                Backend
                                                Documentation</a>
                                        </li>
                                        
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('tickets/') }}">Price Match Tickets</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('store-website.index') }}">Store
                                                Website</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('store-website.apiToken') }}">Store
                                                Website API Token</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('store-website.builderApiKey') }}">Store
                                                Website Builder Key</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('indexer-state.index') }}">Indexer
                                                State</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('payment-responses.index') }}">Payment Responses</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('site-development-status.stats') }}">Multi Site
                                                status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('content-management.index') }}">Content Management</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.brand.list') }}">Store Brand</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.environment.matrix') }}">Store
                                                Environment</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.version-numbers') }}">Storewebsite Version
                                                Number</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.environment.index') }}">Store Environment
                                                Table</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.category.list') }}">Store Category</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.color.list') }}">Store Color</a>
                                            <a class="dropdown-item" href="{{ route('size.index') }}">Size</a>
                                            <a class="dropdown-item" href="{{ route('system.size') }}">System Size</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('landing-page.index') }}">Landing
                                                Page</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('newsletters.index') }}">Newsletters</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre="">Review Newsletters
                                                Translate<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="twilioDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate') }}">Review Arabic
                                                        Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','English') }}">Review
                                                        English Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Chinese') }}">Review
                                                        Chinese Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Japanese') }}">Review
                                                        Japanese Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Korean') }}">Review
                                                        Korean Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Urdu') }}">Review
                                                        Urdu Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Russian') }}">Review
                                                        Russian Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Italian') }}">Review
                                                        Italian Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','French') }}">Review
                                                        French Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Spanish') }}">Review
                                                        Spanish Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','Dutch') }}">Review
                                                        Dutch Newsletters Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('newsletters.review.translate','German') }}">Review
                                                        German Newsletters Translate </a>
                                                </li>

                                            </ul>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.price-override.index') }}">Price
                                                Override</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('country.duty.list') }}">Country
                                                duty list</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('country.duty.index') }}">Country
                                                duty search</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.country-group.index') }}">Country
                                                Group</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.websites.index') }}">Website</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.website-stores.index') }}">Website
                                                Store</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.website-store-views.index') }}">Website
                                                Store View</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.page.index') }}">Website Page</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.page.histories') }}">Website Page
                                                History</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre="">Website Page Review
                                                Translate<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="twilioDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Arabic') }}">Arabic
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','English') }}">English
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Chinese') }}">Chinese
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Japanese') }}">Japanese
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Korean') }}">Korean
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Urdu') }}">Urdu
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Russian') }}">Russian
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Italian') }}">Italian
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','French') }}">French
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Spanish') }}">Spanish
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','Dutch') }}">Dutch
                                                        Page Review Translate </a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('store-website.page.review.translate','German') }}">German
                                                        Page Review Translate </a>
                                                </li>

                                            </ul>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.product-attribute.index') }}">Product
                                                Attribute</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('scrapper.phyhon.index') }}">Site
                                                Scrapper Python</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('scrapper.image.urlList') }}">Scrapper Phyhon Urls</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.site-attributes.index') }}">Site
                                                Attributes</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.category-seo.index') }}">Category seo</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website.cancellation') }}">Cancellation Policy</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                               href="{{ route('logging.magento.product.api.call') }}">Magento API
                                                call</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('product.pricing') }}">Magento
                                                Product Pricing</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                               href="{{ url('/product-generic-pricing') }}">Product Generic Pricing</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                               href="{{ url('/store-website-product-prices') }}">Store website product
                                                price</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('/site-assets') }}">Site assets</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('/site-check-list') }}">Site check
                                                list</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('list.daily-push-log') }}">Magento
                                                Daily Product Push Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('google.bigdata') }}">Google Big
                                                Data</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('zabbix.index') }}">Zabbix Items</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('zabbix.problem') }}">Zabbix
                                                Problems</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('uicheck') }}">U I Check</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('updateLog.get') }}">Update Log</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('uicheck.responsive') }}">U I
                                                Responsive</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('uicheck.device-builder-datas') }}">U
                                                I Device Builder Datas</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('uicheck.translation') }}">U I
                                                Languages</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                               href="{{ route('site-development.store-website-category') }}">Store
                                                Website Category</a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                       data-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false" v-pre="">Admin<span
                                                class="caret"></span></a>

                                    <ul class="dropdown-menu multi-level">
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="twilioDropdown" href="#" class="nav-link dropdown-toggle"
                                               data-toggle="dropdown" role="button" aria-haspopup="true"
                                               aria-expanded="false">Twilio<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="twilioDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('twilio.errors') }}">Twilio
                                                        Errors</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('twilio.webhook.error.logs') }}">Twilio
                                                        Webhook Error Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio-manage-accounts') }}">Twilio Account
                                                        Management</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio.account_logs') }}">Twilio Account Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio.view_tone') }}">Twilio Message Tones</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('twilio/getChats') }}">SMS</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('twilio.erp_logs') }}">Twilio ERP Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a href="{{ route('twilio.call_journey') }}">Twilio call journey</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('get.python.log') }}">Python
                                                        Site Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('twilio.get_website_wise_key_data_options') }}">Twilio
                                                        Key Options</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('twilio.twilio_delivery_logs') }}">Twilio
                                                        Delivery Logs</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>

                                    <ul class="dropdown-menu multi-level">
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('snsAlerts.list')}}">SNS Alerts</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('csvTranslator.list')}}">Csv
                                                translator</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('reply.replyTranslateList')}}">Reply
                                                Translate List</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('store-website.listing')}}">Store
                                                Website Csv Download</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('sonarqube.list.page')}}">Sonar
                                                Cube</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('git-action-lists')}}">Git
                                                Actions</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('magento-problems-lists')}}">Magento
                                                Problems</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('redis.jobs') }}">Redis Job</a>
                                        </li>
                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('task-summary') }}" class=""
                                               role="button" aria-haspopup="true" aria-expanded="false">Task Summary</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="queueDropdown" href="#" class="dropdown-toggle"
                                               data-toggle="dropdown" role="button" aria-haspopup="true"
                                               aria-expanded="false">Queue<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="queueDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('message-queue.index') }}">Message Queue</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('message-queue.approve') }}">Message Queue
                                                        Approval</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('message-queue-history.index') }}">Queue
                                                        History</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="queueDropdown" href="#" class="dropdown-toggle"
                                               data-toggle="dropdown" role="button" aria-haspopup="true"
                                               aria-expanded="false">Zabbix<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="zabbixDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('zabbix.index') }}">Items</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('zabbix.problem') }}">Problems</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('todolist') }}" class=""
                                               role="button" aria-haspopup="true" aria-expanded="false">TodoList</a>
                                        </li>
                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('test-cases') }}" class=""
                                               role="button" aria-haspopup="true" aria-expanded="false">Test Cases</a>
                                        </li>
                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('test-case-automation') }}" class=""
                                               role="button" aria-haspopup="true" aria-expanded="false">Test Cases Automation</a>
                                        </li>

                                        <li class="nav-item dropdown ">
                                            <a id="queueDropdown" href="{{ url('test-suites') }}" class=""
                                               role="button" aria-haspopup="true" aria-expanded="false">Test Suites</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{route('messages.index')}}">Broadcast
                                                messages</a>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="bugDropdown" href="#" class="dropdown-toggle"
                                               data-toggle="dropdown" role="button" aria-haspopup="true"
                                               aria-expanded="false">Bug Track<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="bugDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{route('bug-tracking.index')}}">Bug Track</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('bug-tracking.website') }}">Bug Tracking
                                                        Summary</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('get.model.name') }}">Model Name</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{route('plan.index')}}">Plan</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a href="{{ route('custom-chat-message.index') }}">Chat Messages</a>
                                        </li>

                                        {{-- Sub Menu Product --}}
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre>Cash Flow<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('cashflow.index') }}">Cash
                                                        Flow</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('cashflow.hubstuff.log') }}">Hubstuff Command
                                                        Log</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('monetary-account') }}">Monetary Account</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('voucher.index') }}">Convience Voucher</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('cashflow.mastercashflow') }}">Master Cash
                                                        Flow</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('dailycashflow.index') }}">Daily Cash Flow</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('budget.index') }}">Budget</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{route('settings.index')}}">Settings</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{url('conversion/rates')}}">Currency
                                                        Conversion Rate</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{url('magento-admin-settings')}}">Magento Admin
                                                        Settings</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('auto.refresh.index')}}">Auto
                                                        Refresh page</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('budget.index') }}">Hubstaff</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('page-notes') }}">Page
                                                        Notes</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('page-notes-categories') }}">Page Notes
                                                        Categories</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="/totem">Cron Package</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('charity') }}">Charity</a>
                                                </li>
                                            </ul>
                                        </li>
                                        @if($isAdmin)
                                            <li class="nav-item dropdown">
                                                <a href="{{ route('slack.channel.index') }}">Slack Channel</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a href="{{ route('geminiai-accounts') }}">Gemini AI Account Management</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a href="{{ route('watson-accounts') }}">Watson Account Management</a>
                                            </li>
                                            <li class="nav-item dropdown">
                                                <a href="{{ route('google-chatbot-accounts') }}">Google Dialogflow
                                                    Account Management</a>
                                            </li>

                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Call Management<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{ route('twilio-call-management') }}"> Call
                                                            Management</a>
                                                    </li>

                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('twilio-speech-to-text-logs')}}">Twilio Speech
                                                            to
                                                            text Logs</a>
                                                    </li>

                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('chatbot.type.error.log')}}">Twilio Chat Bot
                                                            Not
                                                            Recognised</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('twilio.call.blocks')}}">Twilio Call Blocks</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('twilio.call.statistic')}}">Twilio Call
                                                            Statistic</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('twilio.conditions')}}">Twilio Conditions</a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                   v-pre>Legal<span class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="navbarDropdown">
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{route('lawyer.index')}}">
                                                            Lawyers</a>
                                                    </li>

                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('case.index')}}">Cases</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        @endif
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre>Old Issues<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/old/') }}">Old Info</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/old/?type=1') }}">Old Out
                                                        going</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/old/?type=2') }}">Old
                                                        Incoming</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre>Duty<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('simplyduty.category.index') }}">SimplyDuty
                                                        Categories</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('simplyduty.currency.index') }}">SimplyDuty
                                                        Currency</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('simplyduty.country.index') }}">SimplyDuty
                                                        Country</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('simplyduty.calculation') }}">SimplyDuty
                                                        Calculation</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('simplyduty.hscode.index') }}">HsCode</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\ProductController::class, 'hsCodeIndex']) }}">HsCode
                                                        Generator</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\HsCodeController::class, 'mostCommon']) }}">Most
                                                        Common</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ action([\App\Http\Controllers\HsCodeController::class, 'mostCommonByCategory']) }}">Most
                                                        Common Category</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('assets-manager.index') }}">Assets
                                                Manager</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('email-addresses.index') }}">Email
                                                Addresses</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('email-addresses.run-histories-listing') }}">Email
                                                Addresses Run Jobs</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('api-response-message') }}">Api
                                                Response Messages</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('services') }}">Services</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre>System<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('jobs.list')}}">Laravel
                                                        Queue</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('failedjobs.list')}}">Laravel
                                                        Failed Queue</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{route('wetransfer.list')}}">Wetransfer Queue</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{route('cron.index')}}">Cron</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <!-- Github -->
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="githubsubmenu" href="#" role="button" data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>Github<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="githubsubmenu">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/organizations') }}">Github Organizations</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/repos') }}">Repositories</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/branches') }}">Branches</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/actions') }}">Actions</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('/github/users') }}">Users</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/groups') }}">Groups</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/pullRequests') }}">Pull requests</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/new-pullRequests') }}">New Pull
                                                        requests</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/new-pr-activities') }}">New PR
                                                        Activities</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('monit-status.index') }}">Monit Status</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('/github/sync') }}">Synchronise from online</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('gitDeplodError') }}">Migration Error</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <!-- hubstaff -->
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="hubstaffsubmenu" href="#" role="button" data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>Hubstaff<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="hubstaffsubmenu">
                                                {{-- Sub Menu Product --}}

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('hubstaff/members')  }}">Members</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('hubstaff/projects') }}">Projects</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('hubstaff/tasks') }}">Tasks</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('hubstaff-payment') }}">Payments Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('hubstaff-activities/notification') }}">Activity
                                                        Notofication</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('hubstaff-activities/activities') }}">Activities</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('hubstaff-acitivties.acitivties.userTreckTime') }}">User
                                                        Track Time</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <!-- time doctor -->
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a href="#" role="button" data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre>Time Doctor<span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('time-doctor.members') }}">Time Doctor Members</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('time-doctor/projects') }}">Time Doctor Projects</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('time-doctor/tasks') }}">Time Doctor Tasks</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('time-doctor-activities/notification') }}">Time
                                                        Doctor Activity
                                                        Notification</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('time-doctor-activities/activities') }}">Time Doctor
                                                        Activities</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('time-doctor-acitivties.acitivties.userTreckTime') }}">Time
                                                        Doctor User
                                                        Track Time</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('time-doctor.task_creation_logs') }}">Time Doctor
                                                        Task Creation Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('time-doctor.list-user') }}">Time Doctor List
                                                        Account</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre>Database<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('database.index') }}">Historical Data</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('database.states') }}">Query Process List</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre>Encryption<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{route('encryption.index')}}">Encryption Key</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre>Courier<span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item"
                                                   href="{{ route('shipment.index') }}">Shipment</a>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('email.index') }}">Emails</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('email-log.list') }}">Email Logs</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('quick.email.list') }}">Quick
                                                Emails</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('activity') }}">Activity</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ url('env-manager') }}">Env Manager</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('routes.index') }}">Routes</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ url('/store-website-analytics/index') }}">Store Website
                                                Analytics</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('store-website-country-shipping.index') }}">Store Website
                                                country shipping</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('googlefiletranslator.list') }}">Google File
                                                Translator</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('google-docs.index') }}">Google
                                                Docs</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item"
                                               href="{{ route('google-drive-screencast.index') }}">Google Drive
                                                Screencast</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ url('/google-traslation-settings') }}">Google Translator
                                                Setting</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('googlewebmaster.index') }}">Google
                                                webmaster</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('gt-metrix') }}">GTMetrix
                                                analysis</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('gt-metrix-url') }}">GTMetrix
                                                Url's</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item"
                                               href="{{ route('GtMetrixAccount.index') }}">GTMetrix Account</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('gtm.cetegory.web') }}">GTMetrix
                                                Category Website</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('checklist') }}">Checklist</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('sop') }}">SOP</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('gtmetrix.error.index.list') }}">GTMetrix Error log</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('/postman') }}">Magento Request</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ url('/postman') }}">Post Man</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('email.event.journey') }}">Sendgrid
                                                Event
                                                Journey</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('magento.command') }}">Magento
                                                Command</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('bank-statement.index') }}">Bank
                                                statements</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('s3-files.index') }}">S3
                                                files</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="{{ route('email-alert-log.index') }}">Email Alert Logs</a>
                                        </li>
                                    </ul>
                                </li>
                            @endif

                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Development <span
                                            class="caret"></span></a>
                                <ul class="dropdown-menu multi-level">
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/change-user') }}">Change
                                            User</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/document/list') }}">Document
                                            Upload List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('load-testing.index') }}">Jmeter Task</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('load-testing.result') }}">Jmeter Result</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item"
                                           href="{{ action([\App\Http\Controllers\NewDevTaskController::class, 'index']) }}">Devtask
                                            Planner</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('development.overview') }}">Overview</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('scrap/development/list') }}">Scrapper
                                            Tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item"
                                           href="{{ url('development/automatic/tasks') }}">Automatic Tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/list') }}">Tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('development/summarylist') }}">Quick Dev
                                            Task</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item"
                                           href="{{url('task?daily_activity_date=&term=&selected_user=&is_statutory_query=3')}}">Discussion
                                            tasks</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('task-types.index') }}">Task Types</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('development.issue.create') }}">Submit
                                            Issue</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('deploy-node') }}">Deploy Node</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('master.dev.task') }}">Dev Master
                                            Control</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('database.index') }}">Database Size</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('database.states') }}">Database
                                            Query Process List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('admin/database-log') }}">Database Log</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('manage-modules.index') }}">Manage
                                            Module</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('manage-task-category.index') }}">Manage
                                            Task Category</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('erp-log') }}">ERP Log</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('whatsapp.log') }}">Whatsapp Log</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('horizon') }}">Jobs</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('project-file-manager') }}">Project
                                            Directory manager</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ route('sentry-log') }}">Sentry Log</a>
                                        <a class="dropdown-item" href="{{ route('development.tasksSummary') }}">Developer
                                            Task Summary</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('settings/telescope') }}">Manage
                                            Telescope </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('telescope/dashboard') }}">View Telescope
                                            Dashboard</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{ url('script-documents') }}">Script
                                            Documents</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/store-website/admin-urls">Admin URLs</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/store-website/admin-password">Admin Password</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/magento/magento_command">Magento Crons</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/virtualmin/domains">Virtualmin Domains</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/mailbox">Mailbox</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="{{route('database.tables-list')}}">Truncate
                                            Tables</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/scrap/scrap-links">Scrap Links</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/devoops">Dev Oops</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="https://erp.theluxuryunlimited.com/seo/company">Seo
                                            Company</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/appointment-request">Appointment Request</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/affiliate-marketing/provider-accounts">Affiliates
                                            Providers Sites</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="dropdown-item" href="/products/listing/scrapper/images">Scrapper
                                            Images</a>
                                    </li>

                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <div id="nav-dotes" class="nav-item dropdown dots mr-3 ml-3">
                                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-haspopup="true" aria-expanded="false" style="padding: 1rem 1rem">
                                        <svg width="16" height="18" viewBox="0 0 16 4" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                    d="M4 2C4 0.9 3.1 -1.35505e-07 2 -8.74228e-08C0.9 -3.93402e-08 -1.35505e-07 0.9 -8.74228e-08 2C-3.93402e-08 3.1 0.9 4 2 4C3.1 4 4 3.1 4 2ZM6 2C6 3.1 6.9 4 8 4C9.1 4 10 3.1 10 2C10 0.9 9.1 -3.97774e-07 8 -3.49691e-07C6.9 -3.01609e-07 6 0.9 6 2ZM12 2C12 3.1 12.9 4 14 4C15.1 4 16 3.1 16 2C16 0.899999 15.1 -6.60042e-07 14 -6.11959e-07C12.9 -5.63877e-07 12 0.9 12 2Z"
                                                    fill="#757575"></path>
                                        </svg>
                                    </a>

                                    <ul id="nav_dots" class="dropdown-menu multi-level ">


                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('redisQueue.list') }}">Larvel
                                                Queue</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false"
                                               v-pre="">{{{ isset(Auth::user()->name) ? Auth::user()->name : 'Settings' }}}
                                                <span
                                                        class="caret"></span></a>

                                            <ul class="dropdown-menu multi-level">
                                                {{-- Sub Menu Product --}}

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('mastercontrol.index') }}">Master
                                                        Control</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('dailyplanner.index') }}">Daily
                                                        Planner</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('task.list') }}">Tasks
                                                        List</a>
                                                </li>
                                                @if($isAdmin)
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{route('password.index')}}">Password
                                                            Manager</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{route('password.manage')}}">Multiple
                                                            User
                                                            Passwords Manager</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item" href="{{route('document.index')}}">Document
                                                            manager</a>
                                                    </li>
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{ route('resourceimg.index') }}">Resource
                                                            Center</a>
                                                    </li>
                                                @endif
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a id="navbarDropdown" class="" href="#" role="button"
                                                       data-toggle="dropdown"
                                                       aria-haspopup="true" aria-expanded="false" v-pre>Product<span
                                                                class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="navbarDropdown">
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item" href="{{route('products.index')}}">Product</a>
                                                        </li>

                                                        <li class="nav-item dropdown">

                                                            <a id="navbarDropdown" class="nav-link dropdown-toggle"
                                                               href="#"
                                                               role="button" data-toggle="dropdown" aria-haspopup="true"
                                                               aria-expanded="false" v-pre>
                                                                Development<span class="caret"></span>
                                                            </a>

                                                            <div class="dropdown-menu dropdown-menu-right"
                                                                 aria-labelledby="navbarDropdown">
                                                                <a class="dropdown-item"
                                                                   href="{{ route('development.index') }}">Tasks</a>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('development.flagtask') }}">Flag
                                                                    Tasks</a>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('development.issue.index') }}">Issue
                                                                    List</a>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('development.issue.create') }}">Submit
                                                                    Issue</a>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('development.overview') }}">Overview</a>
                                                            </div>
                                                        </li>

                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('category-segment.index')}}">Category
                                                                Segment</a>
                                                        </li>

                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item" href="{{route('category')}}">Category</a>
                                                        </li>

                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{action([\App\Http\Controllers\CategoryController::class, 'mapCategory'])}}">Category
                                                                Reference</a>
                                                        </li>

                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item" href="/category/new-references">New
                                                                Category
                                                                Reference</a>
                                                        </li>

                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="/category/new-references-group">New Category
                                                                Reference Group</a>
                                                        </li>

                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item" href="{{route('brand.index')}}">Brands</a>
                                                        </li>
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('missing-brands.index')}}">Missing
                                                                Brands</a>
                                                        </li>
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('brand/size/chart')}}">Brand Size
                                                                Chart</a>
                                                        </li>
                                                        @if(auth()->user() && auth()->user()->checkPermission('category-edit'))
                                                            <li class="nav-item dropdown">
                                                                <a class="dropdown-item"
                                                                   href="{{route('color-reference.index')}}">Color
                                                                    Reference</a>
                                                            </li>
                                                        @endif
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('compositions.index')}}">Composition</a>
                                                        </li>
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('compositions.groups')}}">Composition
                                                                Groups</a>
                                                        </li>
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="/descriptions">Description</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a id="navbarDropdown" class="" href="#" role="button"
                                                       data-toggle="dropdown"
                                                       aria-haspopup="true" aria-expanded="false" v-pre>Customer<span
                                                                class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="navbarDropdown">
                                                        @if($isAdmin)
                                                            <li class="nav-item dropdown">
                                                                <a class="dropdown-item"
                                                                   href="{{route('task_category.index')}}">Task
                                                                    Category</a>
                                                            </li>
                                                        @endif
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item" href="{{route('reply.index')}}">Quick
                                                                Replies</a>
                                                        </li>

                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('autoreply.index')}}">Auto
                                                                Reples</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('brand.index')}}">Brands</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('brand.logo_data')}}">Brand
                                                        Logos</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('missing-brands.index')}}">Missing
                                                        Brands</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('brand/size/chart')}}">Brand
                                                        Size
                                                        Chart</a>
                                                </li>
                                                @if(auth()->user() && auth()->user()->checkPermission('category-edit'))
                                                    <li class="nav-item dropdown">
                                                        <a class="dropdown-item"
                                                           href="{{route('color-reference.index')}}">Color
                                                            Reference</a>
                                                    </li>
                                                @endif
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{url('/kb/')}}" target="_blank">Knowledge
                                                        Base</a>
                                                </li>

                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a href="#" role="button" data-toggle="dropdown"
                                                       aria-haspopup="true" aria-expanded="false" v-pre="">Time
                                                        Doctor<span class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="navbarDropdown">
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('time-doctor-acitivties.pending-payments')}}">Time
                                                                Doctor Approved Timings</a>
                                                        </li>
                                                    </ul>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        {{ __('Logout') }}</a>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                          style="display: none;">
                                                        @csrf
                                                    </form>
                                                </li>
                                            </ul>
                                        </li>

                                        <!------    System Menu     !-------->
                                        <li class="nav-item dropdown dropdown-submenu">

                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre="">System <span
                                                        class="caret"></span></a>

                                            <ul class="dropdown-menu multi-level">
                                                {{-- Sub Menu Product --}}

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{route('jobs.list')}}">Queue</a>
                                                </li>
                                            </ul>
                                        </li>


                                        <li class="nav-item dropdown dropdown-submenu">

                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false" v-pre="">Admin Menu <span
                                                        class="caret"></span></a>

                                            <ul class="dropdown-menu multi-level">
                                                {{-- Sub Menu Admin Menu --}}
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a id="navbarDropdown" class="" href="#" role="button"
                                                       data-toggle="dropdown"
                                                       aria-haspopup="true" aria-expanded="false" v-pre>Database
                                                        Menu<span
                                                                class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="navbarDropdown">
                                                        <li class="nav-item dropdown">
                                                            <a class="dropdown-item"
                                                               href="{{route('admin.databse.menu.direct.dbquery')}}">Direct
                                                                DB
                                                                Query</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a class="dropdown-item" href="{{ url('learning') }}">Learning
                                                        Menu</a>
                                                </li>
                                                <li class="nav-item dropdown dropdown-submenu">
                                                    <a class="dropdown-item"
                                                       href="{{ url('order/invoices/saveLaterList') }}">Save Later
                                                        Invoices</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('monitor-jenkins-build.index') }}">Monitor Jenkins
                                                        Build</a>
                                                    <a class="dropdown-item" href="{{ route('monitor-server.index') }}">Website
                                                        Monitor</a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('zabbix-webhook-data.index') }}">Zabbix Webhook
                                                        Data</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('config-refactor.index') }}">Config Refactors</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('project.index') }}">Projects</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('project.buildProcessLogs') }}">Project Build
                                                        Process Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('project.buildProcessErrorLogs') }}">Project Build
                                                        Process Error Logs</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ route('project-theme.index') }}">Project
                                                        Themes</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('magento-css-variable.index') }}">Magento CSS
                                                        Variables</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ route('deployement-version.index') }}">Deployment
                                                        Version</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('list.voucher') }}">Vouchers
                                                Coupons</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('list.voucher.coupon.code') }}">Vouchers
                                                Coupon Code List</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('get.ip.logs') }}">Ip log</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('get.ssh.logins') }}">Ssh Logins</a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('get.file.permissions') }}">File
                                                Permissions</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('get.backup.monitor.lists') }}">Database
                                                Backup Monitoring</a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item" href="{{ route('technical-debt-lists') }}">Technical
                                                Debt</a>
                                        </li>

                                        <li class="nav-item dropdown dropdown-submenu">

                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre="">Google Developer Reports <span class="caret"></span></a>

                                            <ul class="dropdown-menu multi-level">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('google/developer-api/anr') }}">ANR Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('google/developer-api/logs') }}">Developer Reporting
                                                        Logs Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item"
                                                       href="{{ url('google/developer-api/crash') }}">Crash Report</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="nav-item dropdown dropdown-submenu">

                                            <a id="navbarDropdown" class="" href="#" role="button"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                               v-pre="">IOS App Reports <span class="caret"></span></a>

                                            <ul class="dropdown-menu multi-level">
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('appconnect/usage') }}">Usage
                                                        Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('appconnect/sales') }}">Sales
                                                        Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('appconnect/ads') }}">Ads
                                                        Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('appconnect/payments') }}">Payments
                                                        Report</a>
                                                </li>
                                                <li class="nav-item dropdown">
                                                    <a class="dropdown-item" href="{{ url('appconnect/ratings') }}">Ratings
                                                        Report</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>

                        <div style="width: 100%">
                            <div class="header-search-bar">
                                <div class="nav-item dropdown" id="search_li"><input type="text"
                                                                                     class="form-control nav-link w-100"
                                                                                     placeholder="Search"
                                                                                     style="margin-top : 1%;min-width:120px;"
                                                                                     id="search">
                                    <ul class="dropdown-menu multi-level" id="search_container">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

    </nav>
    @if (in_array($route_name, ["development.issue.index", "task.index", "development.summarylist", "chatbot.messages.list"]))
        @include('development.partials.estimate-shortcut')
    @endif

    @if(auth()->user())
        <div id="ajax-assets-manager-listing-modal"></div>
        <div id="sticky_note_boxes" class="sticknotes_content"></div>

        {{-- @include('twilio.receive_call_popup') --}}
        {{-- @include('partials.modals.quick-task') --}}
        {{-- @include('partials.modals.quick-instruction') --}}
        {{-- @include('partials.modals.quick-development-task') --}}

        {{-- @include('partials.modals.quick-notes') --}}
        <input type="hidden" id="live_chat_key" value="@if(isset($key_ls)){{ $key_ls->key}}@endif">
        @include('partials.chat')
        {{-- Dynamic Model --}}
        <div id="dynamic-model-section"></div>
    @endif
    @if (trim($__env->yieldContent('large_content')))
        <div class="col-md-12">
            @yield('large_content')
        </div>
    @elseif (trim($__env->yieldContent('core_content')))
        @yield('core_content')
    @else
        <main class="container container-grow" style="display: inline-block;">
            @yield('content')
        </main>
    @endif
    <a id="back-to-top" href="javascript:;" class="btn btn-light btn-lg back-to-top" role="button"><i
                class="fa fa-chevron-up"></i></a>
</div>

@auth
    <div class="chat-button-wrapper">
        <div class="col-md-12 page-chat-list-rt dis-none">
            <div class="help-list well well-lg">
                <div class="row">
                    <div class="col-md-3 chat" style="margin-top : 0px !important;">
                        <div class="card_chat mb-sm-3 mb-md-0 contacts_card">
                            <div class="card-header">
                                <div class="input-group">

                                </div>
                            </div>
                            <div class="card-body contacts_body">

                                <ul class="contacts" id="customer-list-chat">
                                    @foreach ($chatIds as $chatId)
                                      @php
                                        $customer = $chatId->customer;
                                        if($customer) {
                                           $customerInitial = strtoupper(substr($customer->name, 0, 1));
                                           $statusClass = $chatId->status == 0 ? 'offline' : 'online';
                                        }
                                      @endphp
                                      @if($customer)
                                        <li onclick="getChats('{{ $customer->id }}')" id="user{{ $customer->id }}"
                                            style="cursor: pointer;">
                                            <div class="d-flex bd-highlight">
                                                <div class="img_cont">
                                                    <soan class="rounded-circle user_inital">{{ $customerInital ?? '' }}</soan>
                                                    <span
                                                            class="online_icon {{ $statusClass }}"></span>
                                                </div>
                                                <div class="user_info">
                                                    <span>{{ $customer->name }}</span>
                                                    <p>{{ $customer->name }} is {{ $statusClass }} </p>
                                                </div>
                                                @if($chatId->seen == 0)
                                                    <span class="new_message_icon"></span>
                                                @endif
                                            </div>
                                        </li>
                                      @endif
                                    @endforeach

                                </ul>
                            </div>
                            <div class="card-footer"></div>
                        </div>
                    </div>
                    <div class="col-md-6 chat">
                        <div class="card_chat">
                            <div class="card-header msg_head">
                                <div class="d-flex bd-highlight align-items-center justify-content-between">
                                    <div class="img_cont">
                                        <soan class="rounded-circle user_inital" id="user_inital"></soan>


                                    </div>
                                    <div class="user_info" id="user_name">

                                    </div>
                                    <div class="video_cam">
                                        <span><i class="fa fa-video"></i></span>
                                        <span><i class="fa fa-phone"></i></span>
                                    </div>
                                    @php
                                        $path = storage_path('/');
                                        $content = File::get($path."languages.json");
                                        $language = json_decode($content, true);
                                    @endphp
                                    <div class="selectedValue">
                                        <select id="autoTranslate" class="form-control auto-translate">
                                            <option value="">Translation Language</option>
                                            @foreach ($language as $key => $value)
                                                <option value="{{$value}}">{{$key}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <span id="action_menu_btn"><i class="fa fa-ellipsis-v"></i></span>
                                <div class="action_menu">

                                </div>
                            </div>
                            <div class="card-body msg_card_body" id="message-recieve">

                            </div>
                            <div class="typing-indicator" id="typing-indicator"></div>
                            <div class="card-footer">
                                <div class="input-group">

                                    <div class="card-footer">
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text attach_btn" onclick="sendImage()"><i
                                                            class="fa fa-paperclip"></i></span>
                                                <input type="file" id="imgupload" style="display:none" />
                                            </div>
                                            <input type="hidden" id="message-id" name="message-id" />
                                            <textarea name="" class="form-control type_msg"
                                                      placeholder="Type your message..." id="message"></textarea>
                                            <div class="input-group-append">
                                                <span class="input-group-text send_btn" onclick="sendMessage()"><i
                                                            class="fa fa-location-arrow"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 customer-info">
                        <div class="chat-righbox">
                            <div class="title">General Info</div>
                            <div id="chatCustomerInfo"></div>

                        </div>
                        <div class="chat-righbox">
                            <div class="title">Visited Pages</div>
                            <div id="chatVisitedPages">

                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Additional info</div>
                            <div class="line-spacing" id="chatAdditionalInfo">

                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Technology</div>
                            <div class="line-spacing" id="chatTechnology">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif



<div id="loading-image-preview"
     style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
</div>

@yield('models')
@stack('component-scripts')
@yield('scripts')

<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.richtext.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.cookie.js') }} "></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/custom_global_script.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/common-function.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js"></script>
<script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script type="text/javascript" src="{{ asset('js/common-helper.js') }}"></script>
<script>
    // Initialize Summernote
    $(document).ready(function() {
        window.Echo.channel(`reverbtest`)
        .listen('*', (e) => {
            console.log("reverbtest=",e);
        });
            
        $("#reply-message").summernote({
        height: 300, // Set the height of the editor
        placeholder: "Write your content here...", // Placeholder text
        toolbar: [
            ["style", ["style"]],
            ["font", ["bold", "italic", "underline", "clear"]],
            ["fontname", ["fontname"]],
            ["fontsize", ["fontsize"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["height", ["height"]],
            ["insert", ["link", "picture", "video"]],
            ["view", ["fullscreen", "codeview"]],
            ["help", ["help"]]
        ]
        });
    });

    function addTextToEditor(text) {
        $("#reply-message").summernote("code", text);
    }

    $("#ipusers").select2({ width: "20%" });
    $("#task_user_id").select2({ width: "20%" });
    $("#quicktask_user_id").select2({ width: "20%" });

    // CKEDITOR.replace("content-app-layout");
    // CKEDITOR.replace("content");
    // if (CKEDITOR.instances['sop_edit_content']) {
    //     CKEDITOR.instances['sop_edit_content'].destroy(true);
    // }
    // CKEDITOR.replace("sop_edit_content");

    @if ($message = Session::get('actSuccess'))
        toastr["success"]('{{$message}}', "success");
    @endif

    @if ($message = Session::get('actError'))
        toastr["error"]('{{$message}}', "error");
    @endif
</script>

@include('layouts.partial.app_js')
<script type="text/javascript" src="{{ asset('js/bootstrap-multiselect.min.js') }} "></script>
<script>
    $(document).ready(function () {
        $(".multiselect").multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'Please Select'
        });
    });

    function isPusherInitialized() {
        return window.Echo.connector ? true : false;
    }
</script>

@if($isAdmin)
    @if(!empty(isCurrentUriIsEnableForEmailAlert()))
    
        <script src="{{ mix("webpack-dist/js/email-alert-echo.js") }}"></script>
    @endif
    <script src="{{ mix("webpack-dist/js/header-icons-echo.js") }}"></script>
@endif

<script>
    if(isPusherInitialized()) {
        window.Echo.private('notification.user.{{ Auth::id()}}')
            .listen('.appointment.found', (e) => {

                if (e.userAppointments?.newAppointments?.length > 0) {
                    Swal.fire({
                        title: e.userAppointments.newAppointments[0].user.name + ' Want to connect on Zoom  - Accept Decline',
                        text: '',
                        showCancelButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Accept',
                        cancelButtonText: 'Decline'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var requeststatus = 1;
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            var requeststatus = 2;

                            $("#decline-remarks #appointment_requests_id").val(e.userAppointments.newAppointments[0].id);
                        }

                        if (requeststatus == 1 || requeststatus == 2) {
                            $.ajax({
                                type: 'POST',
                                url: configs.routes.event_updateAppointmentRequest,
                                beforeSend: function () {
                                    $("#loading-image-modal").show();
                                },
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    id: e.userAppointments.newAppointments[0].id,
                                    request_status: requeststatus
                                },
                                dataType: "json"
                            }).done(function (response) {
                                $("#loading-image-modal").hide();
                                if (response.code == 200) {

                                    let status = ((requeststatus === 1) ? 'accepted' : 'declined');
                                    toastr['success'](`You have successfully ${ status } the request.`, 'success');

                                    window.Echo.private('notification.user.{{ Auth::id()}}')
                                        .whisper('appointment-responded', { status });
                                }
                            }).fail(function (response) {
                                $("#loading-image-modal").hide();
                                toastr['error']('Sorry, something went wrong', 'error');
                            });

                            if (requeststatus == 2) {

                                $("#modal-container-2").load("/decline-remarks", function () {
                                    $("#decline-remarks").modal("show");
                                });
                            }
                        }
                    });
                }

                if (e.userAppointments?.reactedUnseenAppointments?.length > 0) {

                    if (e.userAppointments?.reactedUnseenAppointments[0].request_status == 1) {
                        var msgText = e.userAppointments.reactedUnseenAppointments[0].userrequest.name + ' Confirmed your meeting Request pls. Join zoom'
                    } else {
                        var msgText = e.userAppointments.reactedUnseenAppointments[0].userrequest.name + ' delicate your meeting request - ' + e.userAppointments.reactedUnseenAppointments[0].decline_remarks
                    }

                    Swal.fire({
                        title: 'Hello!',
                        text: msgText,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showCancelButton: false,
                        confirmButtonText: 'Okay'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: configs.routes.event_updateuserAppointmentRequest,
                                beforeSend: function () {
                                    $("#loading-image-modal").show();
                                },
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    id: e.userAppointments.reactedUnseenAppointments[0].id,
                                },
                                dataType: "json"
                            }).done(function (response) {
                                $("#loading-image-modal").hide();
                                toastr['success']('Successfully marked as seen.', 'success');

                                window.Echo.private('notification.user.{{ Auth::id()}}')
                                    .whisper('responded-appointment-seen', {});
                            }).fail(function (response) {
                                $("#loading-image-modal").hide();
                            });
                        }
                    });
                }

            })
            .listenForWhisper('appointment-responded', (e) => {
                toastr['success'](`You have successfully ${ e.status } the request.`, 'success');
                Swal.close();
            })
            .listenForWhisper('responded-appointment-seen', (e) => {
                toastr['success']('Successfully marked as seen.', 'success');
                Swal.close();
            });
    }

    if (autoIframe == undefined || (autoIframe !== undefined && typeof autoIframe !== 'function')) {

        function autoIframe(frameId) {
            try {
                frame = document.getElementById(frameId);
                innerDoc = (frame.contentDocument) ?
                    frame.contentDocument : frame.contentWindow.document;
                objToResize = (frame.style) ? frame.style : frame;
                objToResize.height = innerDoc.body.scrollHeight + 10 + 'px';
            }
            catch (err) {
                window.status = err.message;
            }
        }
    }
</script>

</body>

</html>

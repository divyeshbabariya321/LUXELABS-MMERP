<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ mix('webpack-dist/css/app-custom.css?v=0.1') }} ">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
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
    </script>
    @stack("jquery")
    <script type="text/javascript" src="{{ mix('webpack-dist/js/app.js') }} "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}
    <script type="text/javascript" src="https://media.twiliocdn.com/sdk/js/client/v1.14/twilio.min.js"></script>
    <script src="https://sdk.twilio.com/js/taskrouter/v1.21/taskrouter.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-notify.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/calls.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/custom.js') }} "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/timepicker@1.14.0/jquery.timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clockpicker@0.0.7/dist/bootstrap-clockpicker.min.js"></script>
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
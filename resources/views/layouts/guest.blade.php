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
    $title = ((isset($metaData->page_title) && $metaData->page_title != '') ? $metaData->page_title : trim($__env->yieldContent('title')));
    @endphp
    @if (trim($__env->yieldContent('favicon')))
    <link rel="shortcut icon" type="image/png" href="/favicon/@yield ('favicon')" />
    @endif
    <title>{!! $title !!}</title>
    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(isset($metaData->page_description) && $metaData->page_description!='')
        <meta name="description" content="{{ $metaData->page_description }}">
    @else
        <meta name="description" content="{{ config('app.name') }}">
    @endif


    
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/app.css') }} ">
    
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> --}}

    
    <script>var BASE_URL = "{{config('app.url')}}";</script>
    

    
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/app-custom.css') }} ">
    @stack('link-css')
    @yield('link-css')

    <script>
    let Laravel = {};
    Laravel.csrfToken = "{{csrf_token()}}";
    window.Laravel = Laravel;
    </script>
    

    @stack("jquery")
    <script type="text/javascript" src="{{ mix('webpack-dist/js/app.js') }} "></script>
    

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}

    

    

    <script type="text/javascript" src="{{ mix('webpack-dist/js/custom.js') }} "></script>

    
    
    <script type="text/javascript">
    var BASE_URL = '{{ config('app.url ') }}';
    </script>


    <!-- Fonts -->

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <!-- Styles -->

    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet"> --}}


    <link rel="stylesheet" href="{{ mix('webpack-dist/css/custom.css') }} ">

    <link rel="stylesheet" href="{{ mix('webpack-dist/css/bootstrap.min.css') }} ">

    
    @yield("styles")
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
    
    <script>
        const firebaseConfig = {
            apiKey: '{{config('env.FCM_API_KEY')}}',
            authDomain: '{{config('env.FCM_AUTH_DOMAIN')}}',
            projectId: '{{config('env.FCM_PROJECT_ID')}}',
            storageBucket: '{{config('env.FCM_STORAGE_BUCKET')}}',
            messagingSenderId: '{{config('env.FCM_MESSAGING_SENDER_ID')}}',
            appId: '{{config('env.FCM_APP_ID')}}',
            measurementId: '{{config('env.FCM_MEASUREMENT_ID')}}'
        };
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function (response) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("store.token") }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function (response) {
                    },
                    error: function (error) {
                        console.error(error);
                    },
                });
            }).catch(function (error) {
            alert(error);
        });
        messaging.onMessage(function (payload) {
            const title = payload.notification.title;
            const options = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(title, options);
        });
    </script>

    
    
    @stack("styles")
    
    
    
    <script type="text/javascript">
        const IS_ADMIN_USER = false;
        const LOGGED_USER_ID = null;
    </script>
</head>

<body>
    @stack('modals')

    

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

                       
                    </div>
                    <div class="secondary-header">
                        <!-- Left Side Of Navbar -->

                        <!-- Right Side Of Navbar -->

                        <ul id="navs" class="navbar-nav ml-auto pl-0"
                            style="display:flex;text-align: center;flex-grow: 1;gap:6px">
                            <li class="nav-item">

                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>

                            </li>
                        </ul>
                        
                    </div>
                </div>

            </div>

        </nav>
 
        @php
        $route = request()->route()->getName();
        @endphp
        

       

   
        
        @if (trim($__env->yieldContent('large_content')))
        <div class="col-md-12">
            @yield('large_content')
        </div>
        @elseif (trim($__env->yieldContent('core_content')))
        @yield('core_content')
        @else
        <main class="container container-grow" style="display: inline-block;">
            
            @yield('content')
            <!-- End of fb page like  -->
        </main>
        @endif


        <a id="back-to-top" href="javascript:;" class="btn btn-light btn-lg back-to-top" role="button"><i class="fa fa-chevron-up"></i></a>
    </div>
   
    <!-- Scripts -->

    
    <div id="loading-image-preview"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
    </div>


    <!-- Like page plugin script  -->
    @yield('models')


    @yield('scripts')

    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.cookie.js') }} "></script>
    

    
    <script type="text/javascript" src="{{ mix('webpack-dist/js/common-function.js') }} "></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    
   
    <script>
    

    
    </script>
    @if ($message = Session::get('actSuccess'))
    <script>
    toastr['success']('{{$message}}', 'success');
    </script>
    @endif
    @if ($message = Session::get('actError'))
    <script>
    toastr['error']('{{$message}}', 'error');
    </script>
    @endif
    

</body>

</html>

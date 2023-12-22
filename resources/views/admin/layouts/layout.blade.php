<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Dashboard</title>
    <meta name="shopify-app-iframe" content="true">

    <!-- Google Font: Source Sans Pro -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta class="base-url" id="base-url" content="{{env('APP_URL')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/select2/css/select2.min.css')}}">

    <!--lightbox-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">

    <link href="{{ URL::asset('./admin_/custom/css/style.css') }}" rel="stylesheet" type="text/css" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .spinner-loader {
            position: fixed;
            top: 50%;
            left: 50%;
            z-index: 9999999;
        }
        .loader-wrapper {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 100%;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-wrapper:after {
            content: '';
            background: rgba(0,0,0,0.5);
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            position: absolute;
        }
    </style>

</head>

<body class="" style="background-color: {{ $background_color ?? '' }};">

    <script src="https://unpkg.com/@shopify/app-bridge@ {{env('APP_BRIDGE_VERSION')}} "></script>

    <script>
        const AppBridge = window['app-bridge'];
        const config = {
            apiKey: "{{env('API_KEY')}}",
            host: new URLSearchParams(location.search).get("host"),
            forceRedirect: true
        };
        console.log('config => ', config);
        const app = AppBridge.createApp(config);
    </script>

    {{-- <div class="spinner-border spinner-loader" role="status">
        <span class="sr-only"></span>
    </div> --}}
    <div class="loader-wrapper">
        <div class="spinner-border spinner-loader" role="status">
            <span class="sr-only"></span>
        </div>
    </div>


    <div class="">
        <div class="">

            <section class="content">
                <div class="container-fluid">
                    @include('admin.layouts.inc.header')
                    @if(session('success'))
                        <div class="alert session-alert-msg custom-alert-hide success-message">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="alert alert-msg custom-alert-hide" style="display: none"></div>
                    {{-- @php print_r($is_recur_approve); @endphp --}}
                    @isset($is_recur_approve)
                        @if($is_recur_approve == 0)
                            <div id="sub_modal" class="modal fade show" aria-modal="true" style="padding-right: 17px; display: block;" data-url="{{$confirmation_url}}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <p>In order to use the request a quote app, you will need to accept the subscription charges.<br/>
                                                If you do not wish to accept the app charges, please proceed with deleting the app.</p>
                                            <p class="text-danger"><small>Click on the Continue button to be taken to Approve Subscription page.</small></p>
                                        </div>
                                        <div class="modal-footer">
                                            <a type="button" href="{{ route('app.redirect_approval') }}?url={{base64_encode($confirmation_url)}} ?>"  class="btn btn-primary" >Continue</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-backdrop fade show"></div>
                        @endif
                    @endisset
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
    </div>
    

    <script src="{{ asset('./admin_/temp/plugins/jquery/jquery.js')}}"></script>

    <script src="{{ asset('./admin_/temp/plugins/jquery-validation/jquery.validate.js')}}"></script>
    <script src="{{ asset('./admin_/temp/plugins/jquery-validation/additional-methods.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('./admin_/custom/js/custom.js')}}"></script>

@yield('script')

</body>

</html>

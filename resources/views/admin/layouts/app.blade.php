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
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    {{-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('admin_/temp/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    {{-- <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/summernote/summernote-bs4.min.css') }}"> --}}
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('admin_/temp/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<!-- css file -->
     <link href="{{ URL::asset('./admin_/custom/css/style.css') }}" rel="stylesheet" type="text/css" />

     <!-- select file -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" /> -->


    <!-- CodeMirror -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/codemirror/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/codemirror/theme/monokai.css') }}">
    <!-- SimpleMDE -->
    {{-- <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/simplemde/simplemde.min.css') }}"> --}}
    <!-- Scripts -->
    {{-- @vite(['resources/sass/admin/app.scss', 'resources/js/admin/app.js']) --}}
    {{-- <link rel="stylesheet" href="{{env('APP_URL').'/resources/sass/admin/app.scss'}}"> --}}
    {{-- JQUERY --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    {{-- <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'> --}}

    {{-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> --}}

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_/temp/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!--lightbox-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .spinner-loader {
            position: fixed;
            top: 50%;
            left: 50%;
            z-index: 9999999;
        }
    </style>

</head>

<body class="" style="background-color: {{ $background_color ?? '' }};">
    <div class="spinner-border spinner-loader" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <div class="">

        <!-- Preloader -->
        {{-- <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
    </div> --}}

        {{-- @include('admin.layouts.inc.nav')

        @include('admin.layouts.inc.sidebar') --}}



        <!-- Content Wrapper. Contains page content -->
        <div class="">
            <!-- Content Header (Page header) -->
            {{-- <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('bread-title')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('bread-content')
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div> --}}
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                     @include('admin.layouts.inc.header')
                     @if(session('success'))
                        <div class="alert session-alert-msg custom-alert-hide success-message">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="alert alert-msg custom-alert-hide" style="display: none"></div>
                    @yield('content')

                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->


    <!-- jQuery -->
    {{-- <script src="{{ asset('admin_/temp/plugins/jquery/jquery.min.js') }}"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM=" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script> --}}
    <!-- jQuery UI 1.11.4 -->
    {{-- <script src="{{ asset('admin_/temp/plugins/jquery-ui/jquery-ui.min.js') }}"></script> --}}
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        // $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    {{-- <script src="{{ asset('admin_/temp/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    <!-- ChartJS -->
    {{-- <script src="{{ asset('admin_/temp/plugins/chart.js/Chart.min.js') }}"></script> --}}
    <!-- Sparkline -->
    {{-- <script src="{{ asset('admin_/temp/plugins/sparklines/sparkline.js') }}"></script> --}}
    <!-- JQVMap -->
    {{-- <script src="{{ asset('admin_/temp/plugins/jqvmap/jquery.vmap.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script> --}}
    <!-- jQuery Knob Chart -->
    {{-- <script src="{{ asset('admin_/temp/plugins/jquery-knob/jquery.knob.min.js') }}"></script> --}}
    <!-- daterangepicker -->
    <script src="{{ asset('admin_/temp/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin_/temp/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    {{-- <script src="{{ asset('admin_/temp/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script> --}}
    <!-- Summernote -->
    {{-- <script src="{{ asset('admin_/temp/plugins/summernote/summernote-bs4.min.js') }}"></script> --}}
    <!-- overlayScrollbars -->
    {{-- <script src="{{ asset('admin_/temp/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script> --}}
    <!-- AdminLTE App -->
    {{-- <script src="{{ asset('admin_/temp/dist/js/adminlte.js') }}"></script> --}}
    <!-- AdminLTE for demo purposes -->
    {{-- <script src="{{ asset('admin_/temp/dist/js/demo.js')}}"></script> --}}
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    {{-- <script src="{{ asset('admin_/temp/dist/js/pages/dashboard.js') }}"></script> --}}
    <!-- DataTables  & Plugins -->
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables/jquery.dataTables.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/jszip/jszip.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/pdfmake/pdfmake.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/pdfmake/vfs_fonts.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin_/temp/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script> --}}
    <!-- Select2-min-js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <!-- jquery-validation -->
    {{-- <script src="{{ asset('admin_/temp/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin_/temp/plugins/jquery-validation/additional-methods.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>

    <!-- CodeMirror -->
    {{-- <script src="{{ asset('admin_/temp/plugins/codemirror/codemirror.js') }}"></script>
    <script src="{{ asset('admin_/temp/plugins/codemirror/mode/css/css.js') }}"></script>
    <script src="{{ asset('admin_/temp/plugins/codemirror/mode/xml/xml.js') }}"></script>
    <script src="{{ asset('admin_/temp/plugins/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script> --}}

    <!-- Select2 -->
    {{-- <script src="{{ asset('admin_/temp/plugins/select2/js/select2.full.min.js')}}"></script> --}}

    
    
    <!-- custom js -->
    <script src="{{ asset('./admin_/custom/js/custom.js')}}"></script>


    <script>
        $(function() {
            // Summernote
            // $('#description').summernote({height: 200});

            //Initialize Select2 Elements
            $('.select2').select2();
            //Initialize Select2 Elements
            $('.select2bs4').select2({
            theme: 'bootstrap4'
            });
        });
    </script>

@yield('script')

</body>

</html>

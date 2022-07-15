<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('page_title')</title>

        <!-- Bootstrap core CSS -->
        <link href="{{ asset('vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendors/nprogress/nprogress.css') }}" rel="stylesheet">
        <link href="{{ asset('css/custom.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendors/switchery/dist/switchery.min.css') }}" rel="stylesheet">
        @stack('style')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />

        <!--[if lt IE 9]>
        <script src="../assets/js/ie8-responsive-file-warning.js"></script>
        <![endif]-->

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                @include('admin.layouts.sidebar')
                @include('admin.layouts.top_nav')

                <div class="right_col" role="main">
                    <div class="">
                        <div class="row mb-2">
                            <div class="col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route("admin.dashboard") }}">{{ trans('global.dashboard') }}</a></li>
                                    @yield('breadcrumb')
                                </ol>
                            </div>
                        </div>

                        @include('alerts.flash_message')

                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 ">
                                <div class="x_panel">
                                    <div class="push-right">
                                        @yield('add_new_button')
                                    </div>

                                    <div class="x_content">
                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer>
                    <div class="pull-right">
                        Limitless Factor Pte.Ltd.</a>
                    </div>
                    <div class="clearfix"></div>
                </footer>
            </div>
        </div>

        <!-- jQuery -->
        <script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
        <!-- FastClick -->
        <script src="{{ asset('vendors/fastclick/lib/fastclick.js') }}"></script>
        <!-- NProgress -->
        <script src="{{ asset('vendors/nprogress/nprogress.js') }}"></script>

        <script src="{{ asset('js/custom.min.js') }}"></script>
        @stack('script')
    </body>
</html>

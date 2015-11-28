<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>LCH Albion Online - Management App</title>

        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/select2.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/select2-bootstrap.css') }}">

        <link rel="stylesheet" href="{{ URL::asset('css/sb-admin-2.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/dataTables.bootstrap.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/dataTables-responsive.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/font-awesome.css') }}">
        <!--<link rel="stylesheet" href="{{ URL::asset('css/all.css') }}">-->

        @yield('customHeader')


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <!-- /.navbar-top-links -->
            @include('layouts.partials.topbar')

            <!-- /.navbar-static-side -->
            @include ('layouts.partials.sidebar')


            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="container-fluid">
                    @yield ('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->




        <script src="{{ URL::asset('js/all.js') }}"></script>
        @include('layouts.partials.customScripts')
        @yield ('customScripts')


    </body>

</html>
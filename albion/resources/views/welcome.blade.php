<!--<!DOCTYPE html>-->
<!--<html>-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>LCH Albion Online - Management App</title>
    
        <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/css/_bower.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/css/customstyles.css') }}" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    
    </head>
    <body class="custom-welcome">
        <div class="wrapper">
            <div class="container">
                <div class="row col-sm-6 col-sm-offset-3" style="margin-top: 10%; margin-bottom: 20%">
                    <div class="custom-logo center-block">
                        <!--<img src="http://www.consortium-horizon.com/uploads/XFPSVGFMRGZF.jpg" alt="Le Consortium Horizon">-->
                        <img src="http://www.consortium-horizon.com/assets/img/logo.png" alt="Le Consortium Horizon">
                    </div>
                    <img src="{{URL::asset('assets/images/logoAlbion.png')}}" alt="Albion Online" class="img-responsive center-block">
                    
                    <h1 class="text-center custom-titles text-uppercase" style="color: #D47E6A"><strong>Management App</strong></h1>
                    
                    <div class="text-center" >
                        <a style="margin-top: 20px" type="button" class="btn btn-primary btn-lg col-sm-3 col-sm-offset-2" href="{{ URL::to('auth/login') }}" >LOGIN</a>
                        <a style="margin-top: 20px" type="button" class="btn btn-primary btn-lg col-sm-3 col-sm-offset-2" href="{{ URL::to('auth/register') }}" >REGISTER</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ URL::asset('assets/js/_bower.min.js') }}"></script>
    </body>
</html>

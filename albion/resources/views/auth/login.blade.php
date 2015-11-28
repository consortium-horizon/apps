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

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="login-panel panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Please Log In</h3>
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="/auth/login" role="form">
                                {!! csrf_field() !!}

                                <fieldset>
                                    <!-- <div class="form-group has-success has-feedback">
                                        <label class="control-label" for="inputSuccess2">Input with success</label>
                                        <input type="text" class="form-control" id="inputSuccess2" aria-describedby="inputSuccess2Status">
                                        <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                                        <span id="inputSuccess2Status" class="sr-only">(success)</span>
                                    </div>
                                    <div class="form-group has-warning has-feedback">
                                        <label class="control-label" for="inputWarning2">Input with warning</label>
                                        <input type="text" class="form-control" id="inputWarning2" aria-describedby="inputWarning2Status">
                                        <span class="glyphicon glyphicon-warning-sign form-control-feedback" aria-hidden="true"></span>
                                        <span id="inputWarning2Status" class="sr-only">(warning)</span>
                                    </div>
                                    <div class="form-group has-error has-feedback">
                                        <label class="control-label" for="inputError2">Input with error</label>
                                        <input type="text" class="form-control" id="inputError2" aria-describedby="inputError2Status">
                                        <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                        <span id="inputError2Status" class="sr-only">(error)</span>
                                    </div> -->
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Login" name="email" type="email" value="{{ old('email') }}" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Password" name="password" type="password" id="password">
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="remember" type="checkbox">Remember Me
                                        </label>
                                    </div>
                                    <!-- Change this to a button or input when using this as a form -->
                                    <button class="btn btn-lg btn-success btn-block" type="submit">Login</button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script href="{{ URL::asset('assets/css/_bower.min.js') }}" rel="text/javascript"></script>

    </body>

</html>
var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix .sass('../sass/bootstrap/bootstrap.scss', 'resources/assets/css/bootstrap.css')
        .sass('../sass/select2/core.scss', 'resources/assets/css/select2.css')
        .sass('../sass/select2-bootstrap/build.scss', 'resources/assets/css/select2-bootstrap.css')
        .sass('../bower/datatables-responsive/css/responsive.bootstrap.scss', 'resources/assets/css/datatables-responsive.css')
        .sass('../bower/font-awesome/scss/font-awesome.scss', 'resources/assets/css/font-awesome.css')
        .less('../bower/morrisjs/less/morris.core.less', 'resources/assets/css/morris.css')
        .less('../less/sb-admin-2.less', 'resources/assets/css/sb-admin-2.css')
        .styles([
            'resources/assets/css/bootstrap.css',
            'resources/assets/css/select2.css',
            'resources/assets/css/select2-bootstrap.css',
            'resources/assets/css/dataTables.bootstrap.css',
            'resources/assets/css/datatables-responsive.css',
            'resources/assets/css/font-awesome.css',
            'resources/assets/css/morris.css',
            'resources/assets/css/sb-admin-2.css',
            'resources/assets/css/customstyles.css'
        ])
        .copy('resources/assets/css/bootstrap.css', 'public/css/bootstrap.css')
        .copy('resources/assets/css/select2.css', 'public/css/select2.css')
        .copy('resources/assets/css/select2-bootstrap.css', 'public/css/select2-bootstrap.css')
        .copy('resources/assets/css/font-awesome.css', 'public/css/font-awesome.css')
        .copy('resources/assets/css/morris.css', 'public/css/morris.css')
        .copy('resources/assets/css/sb-admin-2.css', 'public/css/sb-admin-2.css')
        .copy('resources/assets/css/dataTables.bootstrap.css', 'public/css/dataTables.bootstrap.css')
        .copy('resources/assets/css/datatables-responsive.css', 'public/css/datatables-responsive.css')
        .scripts([
            '../bower/jquery/dist/jquery.js',
            '../bower/bootstrap/dist/js/bootstrap.js',
            '../bower/metisMenu/dist/metisMenu.js',
            '../bower/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js',
            '../bower/select2/dist/js/select2.js',
            '../bower/anchor-js/anchor.js',
            '../bower/respond/dest/respond.src.js',
            '../bower/respond/dest/respond.matchmedia.addListener.src.js',
            '../bower/raphael/raphael.js',
            '../bower/morrisjs/morris.js',
            '../bower/datatables/media/js/jquery.dataTables.js',
            '../bower/datatables/media/js/dataTables.bootstrap.js',
            '../bower/datatables-plugins/filtering/type-based/accent-neutralise.js',
            '../bower/datatables-responsive/js/dataTables.responsive.js',
            '../bower/holderjs/holder.js',

        ])
    ;



});
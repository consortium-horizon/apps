<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use app\accClass;


class ViewsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        //Ajouter condition si log ou non
        view()->composer('*', function ($view){

            $userID = null;
            $userName = null;
            $userLevel = null;

            if (Auth::check()) {
                $userID = Auth::user()->id;
                $userName = Auth::user()->name;
                $userLevel = Auth::user()->level;
            }

            $view->with('userID', $userID);
            $view->with('userName', $userName);
            $view->with('userLevel', $userLevel);
        });

        view()->composer('referents.finances.accounting', function ($view){
            $classList = accClass::orderBy('className')->lists('className', 'id');
            $view->with('classList', $classList);
        });



    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

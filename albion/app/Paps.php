<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paps extends Model
{
    protected $table = 'participation';
    protected $fillable = ['eventID', 'userID'];

}

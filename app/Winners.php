<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winners extends Model
{
    //
    protected $fillable = [
        'user_id', 'prizes_id','numbers_id'
    ];


    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function prize()
    {
        return $this->hasOne('App\UserPrizes','id','prizes_id');
    }

    public function number()
    {
        return $this->hasOne('App\WinningNumbers','id','numbers_id');
    }

}

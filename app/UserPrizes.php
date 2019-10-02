<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPrizes extends Model
{
    //

    protected $fillable = [
        'name', 'desc'
    ];

    public function winner()
    {
        return $this->hasMany('App\Winners','prizes_id');
    }

}

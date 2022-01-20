<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    //Relación muchos a uno, es decir, muchas cuentas pueden pertencer a un solo usuario
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    //Relación uno a muchos, es decir, una cuenta puede tener muchos movimientos
    public function movements(){
        return $this->hasMany('App\Movement');
    }
}

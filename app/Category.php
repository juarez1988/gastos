<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    //Relación uno a muchos, es decir, una categoria puede tener muchos movimientos
    public function movements(){
        return $this->hasMany('App\Movement');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $table = 'movements';

    public function category(){
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function account(){
        return $this->belongsTo('App\Account', 'account_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    //Relacion uno a muchos

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function empresa(){
        return $this->belongsTo('App\Models\Empresa');
    }
}

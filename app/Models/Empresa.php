<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;


    //Relacion de uno a muchos
    public function clientes(){
        return $this->hasMany('App\Models\Cliente');
    }

    public function users() {
        return $this->hasMany('App\Models\User');
    }

    public function cajas(){
        return $this->hasMany('App\Models\Caja');
    }

    public function servicios() {
        return $this->hasMany('App\Models\Servicio');
    }

    public function pago() {
        return $this->belongsTo('App\Models\Pago');
    }
}

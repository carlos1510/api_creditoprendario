<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    //Relacion de uno a muchos
    public function users(){
        return $this->hasMany('App\Models\User');
    }

    public function empresas() {
        return $this->hasMany('App\Models\Empresa');
    }

    public function creditos() {
        return $this->hasMany('App\Models\Credito');
    }
}

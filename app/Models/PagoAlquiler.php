<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoAlquiler extends Model
{
    use HasFactory;

    protected $table = 'pago_alquiler';

    //Relacion uno a muchos

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function tipobanco(){
        return $this->belongsTo('App\Models\TipoBanco');
    }

    public function saldoAlquileres(){
        return $this->hasMany('App\Models\SaldoAlquiler');
    }
}

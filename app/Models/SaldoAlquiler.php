<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoAlquiler extends Model
{
    use HasFactory;

    protected $table = 'saldo_alquiler';

    public function pagoAlquiler(){
        return $this->belongsTo('App\Models\PagoAlquiler');
    }
}

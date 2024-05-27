<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCredito extends Model
{
    use HasFactory;

    protected $table = 'detalle_creditos';

    public function creditos() {
        return $this->hasMany('App\Models\Credito');
    }

    public function servicios() {
        return $this->hasMany('App\Models\Servicio');
    }
}

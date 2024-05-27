<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    //Relacion uno a muchos
    
    public function empresa(){
        return $this->belongsTo('App\Models\Empresa');
    }

    public function detalleCredito() {
        return $this->belongsTo('App\Models\DetalleCredito');
    }
}

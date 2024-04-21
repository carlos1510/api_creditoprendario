<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    use HasFactory;

    //Relacion de uno a muchos
    
    public function pago() {
        return $this->belongsTo('App\Models\Pago');
    }
}

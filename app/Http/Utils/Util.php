<?php 
namespace App\Http\Utils;

class Util{
    public static function convertirStringFecha($stringfecha,$conHoras = false, $lenguaje_origen= "ES", $lenguaje_destino= "EN"  ){
        //por defecto d/m/Y
        $response="";

        if(!is_null($stringfecha)){
            if(trim($stringfecha)!=""){
                if($lenguaje_origen=="ES" && $lenguaje_destino=="EN"){
                    $horas="";
                    if($conHoras) {
                        $th=explode(" ",$stringfecha);
                        $stringfecha = $th[0];
                        $horas=" ".$th[1];
                    }
                    $t=explode("/",$stringfecha);
                    $response=$t[2]."-".$t[1]."-".$t[0].$horas;
                }
            }
        }
        return $response;
    }

    public static function nombremes($mes){
        setlocale(LC_TIME, 'spanish');
        $nombre=strftime("%B",mktime(0, 0, 0, $mes, 1, 2000));
        return $nombre;
    }
}
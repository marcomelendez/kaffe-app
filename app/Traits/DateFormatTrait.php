<?php namespace App\Traits;

use Carbon\Carbon;

trait DateFormatTrait
{
//    public function setFechaInicioAttribute($value) {
//
//        $this->attributes['fechainicio'] =  Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
//    }

    public function getFechaInicioAttribute($value)
    {
        return  Carbon::createFromFormat('Y-m-d',$value)->format('d/m/Y');
    }

//    public function setFechaFinAttribute($value)
//    {
//        if($value !=""){
//
//            $this->attributes['fechafin'] = Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
//
//        }else{
//
//            $this->attributes['fechafin'] = null;
//        }
//    }

    public function getFechaFinAttribute($value)
    {
        if($value != null){

            return Carbon::createFromFormat('Y-m-d',$value)->format('d/m/Y');
        }

        return $value;
    }

    public function setFechaLimiteAttribute($value) {

        $this->attributes['fechalimite'] =  Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
    }

    public function getFechaLimiteAttribute($value)
    {
        if($value != null){

            return  Carbon::createFromFormat('Y-m-d',$value)->format('d/m/Y');
        }
    }

    public function setFechaAttribute($value) {

        $this->attributes['fecha'] =  Carbon::createFromFormat('d/m/Y',$value)->format('Y-m-d');
    }

    public function getFechaAttribute($value)
    {
        return  Carbon::createFromFormat('Y-m-d',$value)->format('d/m/Y');
    }
}

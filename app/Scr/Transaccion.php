<?php

namespace App\Scr;

use App\Models\Habitaciones;
use App\Models\Ocupacion;
use App\Models\Quote_alojamiento_hab;
use App\Models\TmpRoomHab;

class Transaccion
{
    public function updateQuoteHab()
    {

        $quoteAlojamiento = Quote_alojamiento_hab::all();

        foreach($quoteAlojamiento as $quote){

            $ocupacion = Ocupacion::find($quote->ocupacion_id);
            $tmpRoomHab = new TmpRoomHab();
            $objTmpRoomHab = $tmpRoomHab->getByHabitacion($ocupacion->habitaciones_id);

            $quoteAlojamientoHab = Quote_alojamiento_hab::find($quote->id);
            $quoteAlojamientoHab->ocupacion_id = $objTmpRoomHab->room_id;

            //$quoteAlojamientoHab->save();

        }
        echo "LISTO";
    }
}

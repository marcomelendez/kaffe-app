<?php
namespace App\Traits;

use App\Models\Quote;
use App\Models\Quote_excursiones;
use App\Models\Quote_producto;
use App\Models\Quote_alojamiento;
use App\Models\Quote_alojamiento_hab;
use App\Models\Quote_alojamiento_sup;
use App\Models\Quotetarexcursiones;
use App\Models\Quotetartraslados;
use App\Models\Quote_status_history;
use App\Models\Quote_traslados;
use App\Models\Quotetaralojamiento;
use App\Extensions\Quotesession;
use App\Scr\Product;
use App\Scr\RoomRate;
use MongoDB\Driver\Session;

trait QuoteTrait
{
    public function procesar($persona)
    {
        $datQuote = Session()->get('_quote.product');

        $descuento = 0;
        $subTotal = 0;
        $total = 0;

        $usersID = \Auth::user()->id;

        $quote = new Quote();
        $quote->personas_id = $persona->id;
        $quote->users_id    = $usersID;
        $quote->mercado_id  = 1;
        $quote->status      = 1;
        $quote->agencias    = 1;

        $quote->save();

        $quoteId = $quote->id;
        $totalGeneral = 0;

        /* Request history */

        $quoteStatusHistory = new Quote_status_history();
        $quoteStatusHistory->status = 1;
        $quoteStatusHistory->users_id = $usersID;
        $quote->history()->save($quoteStatusHistory);

        /* Comprueba todos los productos de la qoute */

        if(isset($datQuote)){
            /** @var Product $contenido */
            foreach($datQuote as $contenido){

                $totalPVP = 0;
                $discount = 0;
                $totalPVD = 0;

                $checkIn  = $contenido->getRequest()->getStartDate()->format('Y-m-d');
                $ckeckOut = $contenido->getRequest()->getEndDate()->format('Y-m-d');

                /** @var RoomRate $rate */
                foreach($contenido->getRates() as $rate){

                    $discount   += $rate->getAmountDiscount();
                    $totalPVD   += $rate->getAmountPVD();

                    $totalPVP   += $rate->getAmountPVP()->getQuantity() - $discount;
                }

                /* Crea Nueva Quote_Producto */
                $dataQuoteProd = array(
                    'quote_id'=>$quoteId,
                    'producto_id'=>$contenido->getProducto()->id,
                    'fechainicio'=>$checkIn,
                    'fechafin'   =>$ckeckOut,
                    'descuento'  =>$discount,
                    'totalneto'  =>$totalPVD,
                    'totalgeneral'=>$totalPVP,
                    'status'      =>1,
                    'vouchers'    =>0,
                    'pagado'      =>0
                );

                $descuento = $discount;
                $totalNeto = $totalPVD;
                $total     = $totalPVP;


                $quoteProducto = Quote_producto::create($dataQuoteProd);

                $idQuoteProd = $quoteProducto->id;

                switch($contenido->getClassification()){

                    case 1:
                        $this->_quoteAlojamiento($contenido,$idQuoteProd);
                        break;
                    case 2:
                        $this->_quoteExcursion($contenido,$idQuoteProd);
                        break;
                    case 3:
                        $this->_quoteTraslado($contenido,$idQuoteProd);
                        break;
                }
            }
        }

        $quote->descuento = $descuento;
        $quote->montoneto = $totalNeto;
        $quote->montototal = $total;
        $quote->save();

        Session()->flash("success","Procesado con exito!");

        Session()->forget('_quote');

        $this->enviarEmail($quoteId);

        return redirect()->route('quote.show',['id'=>$quoteId]);
    }

    /**
     * Producto
     * @$contenido
     * @$id
     */
    protected function _quoteAlojamiento(Product $contenido, $id)
    {

        $plan = $contenido->getRates()[0]->getPlan()->id;

        /* Request alojamiento */
        $dataQuoteAloja = ['quote_producto_id'=>$id,
            'regimen_id'=>$plan
        ];
        $quoteAlojamiento = Quote_alojamiento::create($dataQuoteAloja);
        //
        /* Request alojamiento habitaciones */

        if(count($contenido->getRates()) >0){
            /** @var RoomRate $rate */
            foreach($contenido->getRates() as $rate){

                $ocupacion = $rate->getRoom()->getId();
//              $tarifas   = $habitaciones->getTarifas();

                $totalHab = $rate->getAmountPVP();
                $idQuoteAloja = $quoteAlojamiento->id;
                $quoteTarifas = null;

                $dataQuoteAlojaHab = [
                    'quote_alojamiento_id'=>$quoteAlojamiento->id,
                    'ocupacion_id'=>$ocupacion,
                    'cantidad'=>1,
                    'adultos'=> $rate->getRoom()->getRoomOccupancy()->getTotalAdults(),
                    'childs'=> $rate->getRoom()->getRoomOccupancy()->getTotalChildren(),
                    'infantes'=>implode(",",$rate->getRoom()->getRoomOccupancy()->getChildren()),
                ];

                Quote_alojamiento_hab::create($dataQuoteAlojaHab);

            }
        }

        /* Request alojamiento suplmentos */

//        if($contenido->getSuplementos() != null){
//            foreach($contenido->getSuplementos() as $suplemento){
//
//                $infoSuplemento = $suplemento->getSuplemento();
//
//                $tarifas   = $suplemento->getTarifas();
//                $totalSup = $tarifas->totalAdultos() + $tarifas->totalChild();
//
//                $dataQuoteSuple = array('quote_alojamiento_id'=>$quoteAlojamiento->id,
//                    'suplementos_id'=>$infoSuplemento->id,
//                    'cantidad'=>$suplemento->getCantidad(),
//                    'adultos'=>$tarifas->getAdultos(),
//                    'childs'=>$tarifas->getChild(),
//                    'totalsuplemento'=>$totalSup);
//
//                $quoteSuplemento = Quote_alojamiento_sup::create($dataQuoteSuple);
//                $idQuoteAloja = $quoteSuplemento->id;
//
//                /* Tarifas de suplementos */
//                $i = 0;
//                /* Adultos */
//                foreach($tarifas->getMontoAdultos() as $montoAdl){
//
//                    $dataTarifaAloja = array('quote_alojamiento_id'=>$quoteAlojamiento->id,
//                        'table_id'=>$infoSuplemento->id,
//                        'table'=>'sup',
//                        'noches'=>$montoAdl->getCantidad(),
//                        'comision'=>$montoAdl->getComision(),
//                        'descuento'=>$montoAdl->getDescuento());
//
//                    $quoteTarifas = Quotetaralojamiento::create($dataTarifaAloja);
//                    $dataMontoAdl = ['quote_taralojamiento_id' => $quoteTarifas->id,'montoadulto'=>$montoAdl->getMontoAdl()];
//
//                    $quoteTaralojamientoAdl = Quotetaralojamientoadl::create($dataMontoAdl);
//
//                    /* Childs */
//                    if(count($tarifas->getMontoChild()) > 0){
//
//                        $montoChild = $tarifas->getMontoChild()[$i];
//
//                        $dataMontoChild = array('quote_taralojamiento_id'=>$quoteTarifas->id,
//                            'edadchild' =>$montoChild->getTabEdad(),
//                            'cantidad' => $montoChild->getCantidad(),
//                            'montochild' => $montoChild->getMontoChild());
//
//                        $quoteTarifasChild = Quotetaralojamientochild::create($dataMontoChild);
//                    }
//                    $i++;
//                }
//            }
//        }
    }

    protected function _quoteExcursion($contenido,$id)
    {
        /* Request Excursiones */
        $dataQuoteExcursiones = ['quote_producto_id'=>$id,
            'adultos'=>$contenido->getAdultos(),
            'childs'=>$contenido->getChilds(),
            'infantes'=>$contenido->getInfantes(),
            'adultomayor'=>$contenido->getPreferencial()
        ];
        //

        $quoteExcursiones = Quote_excursiones::create($dataQuoteExcursiones);

        $idQuoteExcursiones = $quoteExcursiones->id;

        $tarifas = $contenido->getTarifas();

        $i = 0;
        /* Adultos */

        foreach($tarifas->getMontoAdultos() as $montoAdl){

            $montoChild = $tarifas->getMontoChild()[$i];

            $dataTarifaExcur = array('quote_excursiones_id'=>$idQuoteExcursiones,
                'montoadulto'=>$montoAdl->getMontoAdl(),
                'montochild'=>$montoChild->getMontoChild(),
                'montoinfante'=>$montoChild->getMontoInf(),
                'montoadultomayor'=>$montoAdl->getMontoPreferencial(),
                'comision'=>$montoAdl->getComision(),
                'descuento'=>$montoAdl->getDescuento());

            $quoteTarifas = Quotetarexcursiones::create($dataTarifaExcur);
            $i++;
        }
    }

    protected function _quoteTraslado($contenido,$id)
    {
        /* Request Excursiones */
        $dataQuoteTraslado = ['quote_producto_id'=>$id,
            'traslados_id'=>$contenido->getTraslado()->id,
            'region_id'=>$contenido->getDestino()->id,
            'adultos'=>$contenido->getAdultos(),
            'childs'=>$contenido->getChilds(),
            'infantes'=>$contenido->getInfantes(),
            'adultomayor'=>0,
            'personas'=>$contenido->getAdultos() + $contenido->getChilds(),
            'salida'=>$contenido->getSalida(),
            'observacion'=>$contenido->getObservacion()
        ];

        $quoteTraslados = Quote_traslados::create($dataQuoteTraslado);

        $idQuoteTraslado = $quoteTraslados->id;

        $tarifas = $contenido->getTarifas();

        $i = 0;
        /* Adultos */

        foreach($tarifas->getMontoAdultos() as $montoAdl){

            $dataTarifaTraslado = array('quote_traslados_id'=>$idQuoteTraslado,
                'comision'=>$montoAdl->getComision(),
                'descuento'=>$montoAdl->getDescuento(),
                'monto'=>$montoAdl->getMontoAdl());

            $quoteTarifas = Quotetartraslados::create($dataTarifaTraslado);
            $i++;
        }
    }

    public function atendido(\Illuminate\Http\Request $request,$id)
    {
        $quote = Quote::findOrFail($id);
        $quote->atendido = 1;
        $quote->status = 5;
        $quote->save();

        /* Actualiza Request status History */

        $userID =  \Auth::user()->id;

        $quoteStatusHistory = new Quote_status_history();
        $quoteStatusHistory->quote_id = $id;
        $quoteStatusHistory->status = 5;
        $quoteStatusHistory->users_id = $userID;
        $quoteStatusHistory->save();

        session()->flash("success","Cotizacion # ".$quote->codigo." marcada como atendida");

        return $this->redireccion();

    }

    public function redireccion()
    {
        return redirect()->route('quote.index',1);
    }
}

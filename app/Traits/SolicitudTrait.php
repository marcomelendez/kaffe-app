<?php
namespace App\Traits;

use App\Models\Clientes;
use App\Models\Comentarios;
use App\Models\Personas;
use App\Models\Quote;
use App\Models\Quote_personas;
use App\Models\Quote_status_history;


trait SolicitudTrait
{
    public function _actPax($data)
    {
        $agenciaId = 0;
        $identificacion = $data['tipoiden'].$data['identificacion'];

        if(\Auth::user()->hasRole('agencia')){

            $agenciaId = \Auth::user()->agencias->agencias->id;
        }

        $clientes = Clientes::firstOrNew(['identificacion'=>$identificacion,'agencias_id'=>$agenciaId],
                                         ['tipoiden' =>$data['tipoiden'],
                                          'identidad'=>$data['identificacion'],
                                          'clase'=>1
                                         ]);

        if($clientes->personas == null) {

            $nombre = $data['nombre'];
            $apellido = $data['apellido'];
            $fullName = "$nombre $apellido";

            if(isset($data['personas_id']) && $data['personas_id'] != ""){

                $personas = Personas::findOrFail($data['personas_id']);

            }else{

                $personas = new Personas();

                $personas->nombre = $data['nombre'];
                $personas->apellido = $data['apellido'];
                $personas->email = $data['email'];
                $personas->telefonolocal = $data['telefonolocal'];
                $personas->telefonomovil = $data['telefonomovil'];
                $personas->full_name = $fullName;
                $personas->save();
            }

            $personas->clientes()->save($clientes);

            $personaPax = $personas;

        }else{

            $personaPax = $clientes->personas;
        }

        return $personaPax;

    }

    public function store(\Illuminate\Http\Request $request)
    {
        $datosPost = \Session::get('confirma');

        $data = $request->all();
        $idQuote = $data['quote_id'];

        /* Pax principal */

        $personas = $this->_actPax($datosPost);

        /* Actualiza quote */
        $quotes = Quote::findOrFail($idQuote);

        $quotePersona = Quote_personas::firstOrNew(['quote_id'=>$idQuote,'personas_id'=>$personas->id],
                                                   ['principal'=>1,
                                                   'facturacion'=>0
                                                   ]);
        //
        $quotePersona->save();

        $quotes->atendido = 1;
        $quotes->status = 2;
        $quotes->save();

        $userID =  \Auth::user()->id;

        /* Actualiza Request status History */

        $quoteStatusHistory = new Quote_status_history();
        $quoteStatusHistory->status = 2;
        $quoteStatusHistory->users_id = $userID;

        $quotes->history()->save($quoteStatusHistory);

        /* Verifica y guarda algun comentario */

        if($data['comentario'] != ""){

            $comentarios = ['quote_id' =>$idQuote,
                'users_id'=>$userID,
                'comentario'=>$data['comentario']
            ];

            Comentarios::create($comentarios);
        }

        return $this->redireccion();
    }

    public function redireccion()
    {
        session()->flash("success","Solicitud de reserva enviada");
        return redirect()->route('quote.index',2);
    }
}

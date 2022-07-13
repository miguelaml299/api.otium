<?php

namespace App\Http\Controllers\Transfer;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // hoteles
        $hoteles = DB::table('base_hotel')->get();
        // agencia - información
        $agencia_id = request('agencia_id');
        // tabla renglon - son los de agencia
        $tipo_renglon = 6;
        // servicio - información
        $fecha_desde = request('fecha_desde');
        $fecha_hasta = request('fecha_hasta');
        $aeropuerto_referencia = request('aeropuerto_referencia');
        $adulto = request('adulto');
        $nino = request('nino');
        $infante = request('infante');
        // servicio (traslado) - consulta de precios
        // 1 entrada, siempre será un aeropuerto
        // 2 salida, siempre será un hotel o puerto
        // 3 multiservicio
            // buscamos el tarifario
            $tarifario = DB::table('agencia')->where('id', $agencia_id)->value('id_tarifario');

            $id_base = DB::table('base_hotel')->where('codigo', $aeropuerto_referencia)->value('id_base');

            $id_mb = DB::table('monto_base')->where('id_mb', $tarifario)->value('id_mb');

            $renglon = DB::table('matriz_monto_base')->where('id_mb', $id_mb)->where('id_origen', $id_base)->get();

            // 0 (lo puse de primero para poder validar el que tiene precio)
                $traslado00 = $renglon[0];
                $traslado0 = $traslado00->id_mmb;
                if ($renglon[0]->tipo_traslado === '1') {
                    $titulo_opcion = "IN";
                } else {
                    $titulo_opcion = "OUT";
                }
                // regular
                $montoRegularAdulto0 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '45')->get("valor_uno");
                $montoRegularNino0 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '46')->get("valor_uno");
                $montoRegularInfante0 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '47')->get("valor_uno");
                $totalRegular0 = ($montoRegularAdulto0[0]->valor_uno * $adulto) + ($montoRegularNino0[0]->valor_uno * $nino) + ($montoRegularInfante0[0]->valor_uno * $infante);
                // privado 1-6
                $montoPrivado160 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '48')->get("valor_uno");
                // privado 7-21
                $montoPrivado7210 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '49')->get("valor_uno");
                // privado 22-33
                $montoPrivado22330 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '50')->get("valor_uno");
                // privado 34-45
                $montoPrivado34450 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '50')->get("valor_uno");
                // privado 46-58
                $montoPrivado46580 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '52')->get("valor_uno");
                // privado 55-59 (eliminar este que no se ve)
                $montoPrivado55590 = DB::table('monto')->where('id_mmb', "109344")->where('id_renglon', '53')->get("valor_uno");
                if ($montoPrivado55590 == "[]") {
                    $montoPrivado55590 = 0;
                }
            // 1
                $traslado11 = $renglon[1];
                $traslado1 = $traslado11->id_mmb;
                if ($renglon[1]->tipo_traslado === '1') {
                    $titulo_opcion = "IN";
                } else {
                    $titulo_opcion = "OUT";
                }
                // regular
                $montoRegularAdulto1 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '45')->get("valor_uno");
                $montoRegularNino1 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '46')->get("valor_uno");
                $montoRegularInfante1 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '47')->get("valor_uno");
                $totalRegular1 = ($montoRegularAdulto1[0]->valor_uno * $adulto) + ($montoRegularNino1[0]->valor_uno * $nino) + ($montoRegularInfante1[0]->valor_uno * $infante);
                // privado 1-6
                $montoPrivado161 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '48')->get("valor_uno");
                // privado 7-21
                $montoPrivado7211 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '49')->get("valor_uno");
                // privado 22-33
                $montoPrivado22331 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '50')->get("valor_uno");
                // privado 34-45
                $montoPrivado34451 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '50')->get("valor_uno");
                // privado 46-58
                $montoPrivado46581 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '52')->get("valor_uno");
                // privado 55-59 (eliminar este que no se ve)
                $montoPrivado55591 = DB::table('monto')->where('id_mmb', $traslado1)->where('id_renglon', '53')->get("valor_uno");
                if ($montoPrivado55591 == "[]") {
                    $montoPrivado55591 = 0;
                }
        // servicio - respuesta
        $data = [
            "request" => [
                "fecha_desde" => $fecha_desde,
                "fecha_hasta" => $fecha_hasta,
                "aeropuerto_referencia" => $aeropuerto_referencia,
                "adulto" => $adulto,
                "nino" => $nino,
                "infante" => $infante,
            ],
            "silla_nino" => [
                "cantidad" => "x",
                "tipo" => []
            ],
            "silla_infante" => [
                "cantidad" => "x",
                "tipo" => []
            ],
            "traslados" => [
                [
                    "tipo_traslado" => $renglon[0]->tipo_traslado,
                    "id_origen" => $renglon[0]->id_origen,
                    "id_destino" => $renglon[0]->id_destino,
                    "tipo_servicio" => "regular",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $totalRegular0,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[0]->tipo_traslado,
                    "id_origen" => $renglon[0]->id_origen,
                    "id_destino" => $renglon[0]->id_destino,
                    "tipo_servicio" => "Privado (1 a 6 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado160[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[0]->tipo_traslado,
                    "id_origen" => $renglon[0]->id_origen,
                    "id_destino" => $renglon[0]->id_destino,
                    "tipo_servicio" => "Privado (7 a 21 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado7210[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[0]->tipo_traslado,
                    "id_origen" => $renglon[0]->id_origen,
                    "id_destino" => $renglon[0]->id_destino,
                    "tipo_servicio" => "Privado (22 a 33 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado22330[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[0]->tipo_traslado,
                    "id_origen" => $renglon[0]->id_origen,
                    "id_destino" => $renglon[0]->id_destino,
                    "tipo_servicio" => "Privado (34 a 45 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado34450[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[0]->tipo_traslado,
                    "id_origen" => $renglon[0]->id_origen,
                    "id_destino" => $renglon[0]->id_destino,
                    "tipo_servicio" => "Privado (46 a 58 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado46580[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[0]->tipo_traslado,
                    "id_origen" => $renglon[0]->id_origen,
                    "id_destino" => $renglon[0]->id_destino,
                    "tipo_servicio" => "Privado (55 a 59 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado55590,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[1]->tipo_traslado,
                    "id_origen" => $renglon[1]->id_origen,
                    "id_destino" => $renglon[1]->id_destino,
                    "tipo_servicio" => "regular",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $totalRegular1,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[1]->tipo_traslado,
                    "id_origen" => $renglon[1]->id_origen,
                    "id_destino" => $renglon[1]->id_destino,
                    "tipo_servicio" => "Privado (1 a 6 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado161[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[1]->tipo_traslado,
                    "id_origen" => $renglon[1]->id_origen,
                    "id_destino" => $renglon[1]->id_destino,
                    "tipo_servicio" => "Privado (7 a 21 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado7211[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[1]->tipo_traslado,
                    "id_origen" => $renglon[1]->id_origen,
                    "id_destino" => $renglon[1]->id_destino,
                    "tipo_servicio" => "Privado (22 a 33 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado22331[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[1]->tipo_traslado,
                    "id_origen" => $renglon[1]->id_origen,
                    "id_destino" => $renglon[1]->id_destino,
                    "tipo_servicio" => "Privado (34 a 45 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado34451[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[1]->tipo_traslado,
                    "id_origen" => $renglon[1]->id_origen,
                    "id_destino" => $renglon[1]->id_destino,
                    "tipo_servicio" => "Privado (46 a 58 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado46581[0]->valor_uno,
                    "moneda" => "1"
                ],
                [
                    "tipo_traslado" => $renglon[1]->tipo_traslado,
                    "id_origen" => $renglon[1]->id_origen,
                    "id_destino" => $renglon[1]->id_destino,
                    "tipo_servicio" => "Privado (55 a 59 Pax)",
                    "titulo_opcion" => $titulo_opcion,
                    "precio" => $montoPrivado55591,
                    "moneda" => "1"
                ],
            ],
            "hoteles" => [
                "zona" => "zona",
                "hotel" => [
                    "id_hotel" => "id",
                    "nombre_hotel" => "nombre"
                ]
            ]
        ];
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

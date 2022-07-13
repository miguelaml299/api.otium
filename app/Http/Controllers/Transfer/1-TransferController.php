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
        // información de agencia
        $agencia_id = request('agencia_id');

        // información de servicio
        $origen = request('origen');
        $destino = request('destino');
        $tipo_traslado = request('tipo_traslado');
            // fechas
            if(!request('fecha_llegada') || request('fecha_llegada') === '' || request('fecha_llegada') === '0')
            {
                $fecha_llegada = 0;
            } else {
                $fecha_llegada = request('fecha_llegada');
            }
            if(!request('fecha_salida') || request('fecha_salida') === '' || request('fecha_salida') === '0')
            {
                $fecha_salida = 0;
            } else {
                $fecha_salida = request('fecha_salida');
            }
        $adulto = request('adulto');
        $nino = request('nino');
        $infante = request('infante');
        if(!request('silla_nino') || request('silla_nino') === '' || request('silla_nino') === '0')
        {
            $silla_nino = 0;
        } else {
            $silla_nino = request('silla_nino');
        }
        if(!request('silla_infante') || request('silla_infante') === '' || request('silla_infante') === '0')
        {
            $silla_infante = 0;
        } else {
            $silla_infante = request('silla_infante');
        }
        $silla_nino_precio = 20;
        $silla_infante_precio = 20;

        // traslados
        // 1 entrada, siempre será un aeropuerto
        // 2 salida, siempre será un hotel o puerto
        // 3 multiservicio
        if($tipo_traslado == 1)
        {
            // obtenemos el listado de vuelos
            $vuelos = DB::table('vuelo_temporal')->select('id','nro_vuelo')->get();
            // buscamos el id de la sub_zona con el id de origen
            $subzona = DB::table('base_hotel')->where('id_base', $destino)->value('id_subzona');
            // buscamos el id de la zona con el id de la sub_zona
            $zona_destino = DB::table('destino')->where('id', $subzona)->value('id_zona');
            // identificamos el id de la zona
            $zona = DB::table('zonat')->where('id', $zona_destino)->value('id');
            // buscamos el id del renglón de precios
            $montos_renglon = DB::table('matriz_monto_base')->where('id_mb', $agencia_id)->where('tipo_traslado', $tipo_traslado)->where('id_origen', $origen)->where('id_destino', $zona)->max('id_mmb');
            // identificamos los precios con el id del renglón
            $montos = DB::table('monto')->where('id_mmb', $montos_renglon)->get();
            //regular
                // adulto
                $id_renglon_adulto = 45;
                $monto_regular_adulto = DB::table('monto')->select('valor_uno')->where('id_mmb', $montos_renglon)->where('id_renglon', $id_renglon_adulto)->get()->max('valor_uno');
                // nino
                $id_renglon_nino = 46;
                $monto_regular_nino = DB::table('monto')->select('valor_uno')->where('id_mmb', $montos_renglon)->where('id_renglon', $id_renglon_nino)->get()->max('valor_uno');
                // infante
                $id_renglon_infante = 47;
                $monto_regular_infante = DB::table('monto')->select('valor_uno')->where('id_mmb', $montos_renglon)->where('id_renglon', $id_renglon_infante)->get()->max('valor_uno');
            // privado estándar
                $id_renglon_privado_estandar = 119;
                $monto_privado_estandar = DB::table('monto')->select('valor_uno')->where('id_mmb', $montos_renglon)->where('id_renglon', $id_renglon_privado_estandar)->get()->max('valor_uno');
            // privado otro
                $id_renglon_privado_otro = 120;
                $monto_otro = DB::table('monto')->select('valor_uno')->where('id_mmb', $montos_renglon)->where('id_renglon', $id_renglon_privado_otro)->get()->max('valor_uno');
            // calculo de precios
                // regular
                $monto_regular_total = ($adulto * $monto_regular_adulto) + ($nino * $monto_regular_nino) + ($silla_nino * $silla_nino_precio) + ($silla_infante * $silla_infante_precio);
                // privado
                $total_pasajeros = $adulto + $nino;
                if($silla_infante > 0)
                {
                    $total_pasajeros = $adulto + $nino + $silla_infante;
                }
                // privado estándar y otro
                    if($total_pasajeros <= 8)
                    {
                        if($silla_nino > 0 && $silla_infante > 0)
                        {
                            $monto_privado_estandar_total = $monto_privado_estandar + ($silla_nino_precio * $silla_nino) + ($silla_infante_precio * $silla_infante);
                        } else if($silla_nino > 0 && $silla_infante === 0)
                        {
                            $monto_privado_estandar_total = $monto_privado_estandar + ($silla_nino_precio * $silla_nino);
                        } else if($silla_nino === 0 && $silla_infante > 0)
                        {
                            $monto_privado_estandar_total = $monto_privado_estandar + ($silla_infante_precio * $silla_infante);
                        } else 
                        {
                            $monto_privado_estandar_total = $monto_privado_estandar;
                        }
                    } else
                    {
                        $monto_privado_estandar_total = 0;
                    }

                    if($total_pasajeros > 8)
                    {
                        // $monto_otro_total = 0000;
                        if($silla_nino > 0 && $silla_infante > 0)
                        {
                            $monto_otro_total = $monto_otro + ($silla_nino_precio * $silla_nino) + ($silla_infante_precio * $silla_infante);
                        } else if($silla_nino > 0 && $silla_infante === 0)
                        {
                            $monto_otro_total = $monto_otro + ($silla_nino_precio * $silla_nino);
                        } else if($silla_nino === 0 && $silla_infante > 0)
                        {
                            $monto_otro_total = $monto_otro + ($silla_infante_precio * $silla_infante);
                        } else 
                        {
                            $monto_otro_total = $monto_otro;
                        }
                    } else 
                    {
                        $monto_otro_total = 0;
                    }
            // estructura de respuesta
            $data = [
                "servicios" => [
                    "tipo_traslado" => $tipo_traslado,
                    "codigo_origen" => $origen,
                    "codigo_destino" => $destino,
                    "fecha_llegada" => $fecha_llegada,
                    "fecha_salida" => $fecha_salida,
                    "traslados_disponibles" => [
                        "regular" => [
                            'precio' => $monto_regular_total,
                        ],
                        "privado_estandar" => [
                            'precio' => $monto_privado_estandar_total,
                        ],
                        "otro" => [
                            'precio' => $monto_otro_total,
                        ],
                    ],
                    "vuelos" => $vuelos,
                    "adulto" => $adulto,
                    "nino" => $nino,
                    "infante" => $infante,
                    "silla_nino" => $silla_nino,
                    "silla_infante" => $silla_infante,
                ],
            ];
            return $data;
            // return $montos_renglon;
        } else if ($tipo_traslado == 2)
        {
            return "En construcción 2";    
        }
        return "En construcción";
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

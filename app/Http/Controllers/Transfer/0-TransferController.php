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
        $agenciaId = request('agenciaId');
        // información de servicio
        // tipo_servicio:[{ 1, entrada }{ 2, salida }, { 3 } ]
        $origen = request('origen');
        $destino = request('destino');
        $tipo_traslado = request('tipo_traslado');
        $fecha_llegada = request('fecha_llegada');

        // operaciones
        // 1 Entrada
        // 2 Salida
        if($tipo_traslado == 1)
        {
            $subzona = DB::table('base_hotel')->where('id_base', $destino)->value('id_subzona');
            $zona_destino = DB::table('destino')->where('id', $subzona)->value('id_zona');
            $zona = DB::table('zonat')->where('id', $zona_destino)->value('id');
            $montos_renglon = DB::table('matriz_monto_base')->where('id_mb', $agenciaId)->where('tipo_traslado', $tipo_traslado)->where('id_origen', $origen)->where('id_destino', $zona)->max('id_mmb');
            $montos = DB::table('monto')->where('id_mmb', $montos_renglon)->get();
            return $montos;
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

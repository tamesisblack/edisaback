<?php

namespace App\Http\Controllers;

use App\Models\PeriodosInstitucion;
use Illuminate\Http\Request;
use DB;

class PeriodosInstitucionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if ($request->id > 0) {
            $datos = PeriodosInstitucion::find($request->id);
        }else{
            $datos = new PeriodosInstitucion();
        }
        $datos->periodoescolar_idperiodoescolar = $request->periodoescolar_idperiodoescolar;
        $datos->institucion_idinstitucion = $request->institucion_idinstitucion;        
        $datos->save();
        return $datos;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PeriodosInstitucion  $periodosInstitucion
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PeriodosInstitucion  $periodosInstitucion
     * @return \Illuminate\Http\Response
     */
    public function edit(PeriodosInstitucion $periodosInstitucion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PeriodosInstitucion  $periodosInstitucion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PeriodosInstitucion $periodosInstitucion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PeriodosInstitucion  $periodosInstitucion
     * @return \Illuminate\Http\Response
     */
    public function destroy(PeriodosInstitucion $periodosInstitucion)
    {
        //
    }
    public function periodosXInstitucion($id)
    {
        $dato = DB::table('periodoescolar_has_institucion as phi')
        ->where('institucion_idinstitucion','=',$id)
        ->leftjoin('periodoescolar as pe','phi.periodoescolar_idperiodoescolar','=','pe.idperiodoescolar')
        ->leftjoin('region as r','pe.region_idregion','r.idregion')
        ->select('pe.descripcion','pe.periodoescolar','pe.estado','r.nombreregion','phi.id','phi.updated_at')
        ->get();
        return $dato;
    }
    public function verificaPeriodoInstitucion(Request $request)
    {
        $verifica = DB::table('periodoescolar_has_institucion')
        ->where('periodoescolar_idperiodoescolar','=',$request->idperiodoescolar)
        ->where('institucion_idinstitucion','=',$request->idInstitucion)
        ->count();
        if ($verifica > 0) {
            DB::table('contratos')
            ->where('contrato_id', $request->contrato_id)
            ->update(['id_periodo' =>$request->idperiodoescolar]);
            return $verifica;
        }else{
            DB::table('contratos')
            ->where('contrato_id', $request->contrato_id)
            ->update(['id_periodo' =>$request->idperiodoescolar]);

            //para guardar en la tabla periodos
            $dato = new PeriodosInstitucion();
            $dato->periodoescolar_idperiodoescolar = $request->idperiodoescolar;
            $dato->institucion_idInstitucion = $request->idInstitucion;
            $dato->save();
            return $dato;            
        }
    }
    public function eliminarPeriodosXInstitucion($id)
    {
        $dato = PeriodosInstitucion::find($id);
        $dato->delete();
        return $dato;
    }
}

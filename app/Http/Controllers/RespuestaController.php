<?php

namespace App\Http\Controllers;

use App\Models\Respuesta;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
class RespuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "hola mundo";
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

            if($request->actividad){
                //para ingresar actividad
                $idusuario = $request->idusuario;
                $idtarea = $request->idtarea;
                $descripcion = $request->descripcion;
                $file = $request->archivo;
                $extension = $request->extension;
                $codigo = $request->codigo;
         
                $year  = date('Y');  
                $mes   = date('m');
           
                DB::INSERT("INSERT INTO `respuesta_actividad`(`actividad_id`, `nombre_tarea`, `url`, `usuario_idusuario`, `descripcion`, `year`, `mes`) VALUES (?,?,?,?,?,?,?)",[$idtarea,$file,$codigo,$idusuario,$descripcion,$year,$mes]);
            }else{
                //para ingresar tarea
                $idusuario = $request->idusuario;
                $idtarea = $request->idtarea;
                $descripcion = $request->descripcion;
                $file = $request->archivo;
                $extension = $request->extension;
                $codigo = $request->codigo;
         
                $year  = date('Y');  
                $mes   = date('m');
           
                DB::INSERT("INSERT INTO `respuesta`(`tarea_idtarea`, `nombre`, `url`, `usuario_idusuario`, `descripcion`, `year`, `mes`) VALUES (?,?,?,?,?,?,?)",[$idtarea,$file,$codigo,$idusuario,$descripcion,$year,$mes]);
            }
           
      

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function show(Respuesta $respuesta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function edit(Respuesta $respuesta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Respuesta $respuesta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Respuesta $respuesta)
    {
        //
    }
}

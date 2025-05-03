<?php

namespace App\Http\Controllers;

use App\Models\Periodos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function updatedata(){
    //     // set_time_limit(60000);
    //     // ini_set('max_execution_time', 60000);

    //     $res = DB::table('codigoslibros')
    //     ->where('codigo', 'like', '%BGU%')
    //     ->where('serie', 'like', 'manantial')
    //     ->take(10)
    //     ->update(['serie' => 'manantial BGU']);
    //     if($res){
    //         return "Se actualizo correctamente";
    //     }else{
    //         return "No se pudo actualizar";
    //     }
        
    // }
    public function index(Request $request)
    {
        if($request->todo){
            $periodo = DB::SELECT("SELECT * FROM periodoescolar ORDER BY idperiodoescolar  DESC");
            return $periodo;
        }
        if($request->buscaregion){
            if($request->buscaregion == 1){
                $dato =   DB::table('periodoescolar')
                ->where('region_idregion', "1")
                ->get();
                return $dato;
            }
            if($request->buscaregion == 2){
                $dato =   DB::table('periodoescolar')
                ->where('region_idregion', "2")
                ->get();
                return $dato;
            }else{
                return "Institucion no tiene region";
            }
        }
        
        else{
            $region = $request->region;
            if($region == "S"){
                $dato =   DB::table('periodoescolar')
                ->where('region_idregion', "1")
                ->get();
            }else{
             $dato =   DB::table('periodoescolar')
             ->where('region_idregion', "2")
             ->get();
            }
             // $dato = Periodos::get();
              return $dato;
        }
      

      
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
        // return $request->idperiodoescolar;
        if ($request->idperiodoescolar > 0) {
            $datos = Periodos::find($request->idperiodoescolar);
        }else{
            $datos = new Periodos();
        }
        $datos->fecha_inicial = $request->fecha_inicial;
        $datos->fecha_final = $request->fecha_final;
        $datos->region_idregion = $request->region_idregion;
        $datos->descripcion = $request->descripcion;
        $datos->periodoescolar = $request->periodoescolar;
        $datos->save();
        return $datos;

    }
    public function activar(Request $request){
        $idperiodoescolar=$request->idperiodoescolar;
        $res = DB::table('periodoescolar')
        ->where('idperiodoescolar', $idperiodoescolar)
        ->update(['estado' => "1"]);
         return $res;
        
    }

    public function desactivar(Request $request){
        $idperiodoescolar=$request->idperiodoescolar;
        $res = DB::table('periodoescolar')
        ->where('idperiodoescolar', $idperiodoescolar)
        ->update(['estado' => "0"]);
         return $res;
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Periodos  $periodos
     * @return \Illuminate\Http\Response
     */
    public function show(Periodos $periodos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Periodos  $periodos
     * @return \Illuminate\Http\Response
     */
    public function edit(Periodos $periodos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Periodos  $periodos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Periodos $periodos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Periodos  $periodos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }
    public function eliminaPeriodo($id)
    {
        $dato = Periodos::find($id);
        $dato->delete();
        return $dato;
    }
    public function institucionTraerPeriodo(Request $request){
           //para traer el periodo de una institucion
           $periodoInstitucion = DB::SELECT("SELECT idperiodoescolar AS periodo ,(SELECT nombre FROM institucion where idInstitucion = '$request->institucion_id' ) as nombreInstitucion, periodoescolar AS descripcion
           FROM periodoescolar WHERE idperiodoescolar = (
               SELECT  pir.periodoescolar_idperiodoescolar as id_periodo
               from institucion i,  periodoescolar_has_institucion pir
               WHERE i.idInstitucion = pir.institucion_idInstitucion
               AND pir.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi
               WHERE phi.institucion_idInstitucion = i.idInstitucion
               AND i.idInstitucion = '$request->institucion_id'))
           ");
           return $periodoInstitucion;
    }
}

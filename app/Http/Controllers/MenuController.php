<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Roles;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function index(Request $request)
    {
            // $menu = DB::SELECT("SELECT * FROM menu WHERE grupo_idgrupo = ?",[$request->idgrupo]);
        $menu = DB::table('menu')
        ->where('grupo_idgrupo','=',$request->idgrupo)
        ->orderBy('orden')
        ->get();
        return $menu;
    }
    public function statusPeriodo(Request $request){
        $filtroInstitucion = DB::SELECT("SELECT * FROM  institucion WHERE idinstitucion  ='$request->institucion_idinstitucion;'");
        $EncontrarRegion  = $filtroInstitucion[0]->region;
        if ($EncontrarRegion ==  2) {
            //validar que el periodo aun no termine la fecha limite
            $todate  = date('Y-m-d');  
            $filtrarPeriodo = DB::SELECT("SELECT * FROM  periodoescolar p
            WHERE p.region_idregion = '2'
            AND p.estado = '1'
            ORDER BY p.idperiodoescolar DESC
            ");
            if(count($filtrarPeriodo)>0){
                $f_final = $filtrarPeriodo[0]->fecha_final;
                if($todate > $f_final){
                    return ["status" => "0","message"=>"El periodo ya caduco comuniquese con sistemas"];
                }
                return ["status" => "1","message"=>"Periodo se encuentra se encuentra activo"];
            }else{
                return ["status" => "0","message"=>"No existe periodo para la region costa"];
            }
        } else {
            //validar que el periodo aun no termine la fecha limite
            $todate  = date('Y-m-d');  
            $filtrarPeriodo = DB::SELECT("SELECT * FROM  periodoescolar p
            WHERE p.region_idregion = '1'
            AND p.estado = '1'
            ORDER BY p.idperiodoescolar DESC
            ");
            if(count($filtrarPeriodo)>0){
                $f_final = $filtrarPeriodo[0]->fecha_final;
                if($todate >= $f_final){
                    return ["status" => "0","message"=>"El periodo ya caduco comuniquese con sistemas"];
                }
            }else{
                return ["status" => "0","message"=>"No existe periodo para la region sierra"];
            }
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
        if(!empty($request->idmenu) && $request->idmenu != null  ){
            $datos = Menu::find($request->idmenu);
        }else{
            $datos = new Menu();
        }
        // return $datos;

        $datos->url = $request->url;
        $datos->name = $request->name;
        $datos->icon = $request->icon;
        $datos->orden = $request->orden;
        $datos->grupo_idgrupo = $request->grupo_idgrupo;
        
        $datos->save();

        return $datos;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
    public function eliminaMenu($id)
    {
        $dato = Menu::find($id);
        $dato->delete();
        return $dato;
    }
    public function listaMenu()
    {
        $dato = DB::table('menu')
        ->join('grupo as g','g.idgrupo','=','menu.grupo_idgrupo')
        ->select('menu.*','g.nombre as grupo','g.detalle')
        ->get();
        return $dato;
    }
}

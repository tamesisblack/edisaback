<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Tema;

class TemaController extends Controller
{
    public function index(Request $request)
    {   
        $temas = DB::SELECT("SELECT t.id AS id, t.idusuario, t.nombre_tema , a.idlibro, t.unidad, a.nombre,  t.id_unidad , CONCAT(u.unidad, ' - ', u.nombre_unidad) as label, u.id_unidad_libro
        FROM `temas` t, `libro` a , unidades_libros u
        WHERE t.id_asignatura = a.idlibro
        AND t.id_unidad  = u.id_unidad_libro 
        AND t.id_unidad = $request->id_unidad
        AND t.estado =1 
        ORDER BY a.idlibro
        ");

        return $temas;

    }

    public function store(Request $request)
    {
        if( $request->id ){
            $tema = Tema::find($request->id);
        }else{
            $tema = new Tema();
        }

        $tema->nombre_tema = $request->nombre;
        $tema->id_asignatura = $request->asignatura;
        $tema->unidad = $request->unidad;
        $tema->id_unidad = $request->id_unidad;
     
        $tema->idusuario = $request->idusuario;
        $tema->estado = $request->estado;
        $tema->save();

        return $tema;
    }
    public function eliminar_tema(Request $request)
    {
        $temas = DB::UPDATE("UPDATE `temas` SET `idusuario`=$request->idusuario,`estado`=0 WHERE `id` = $request->id_tema;");

        return $temas;
    }
}

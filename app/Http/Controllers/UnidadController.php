<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Unidad;

class UnidadController extends Controller
{
    public function index(Request $request){
      
    }
    //api:get//>/api/unidadesX_Libro

    public function unidadesX_Libro($id)
    {
        // $unidades = DB::SELECT("SELECT ul.*
        // FROM unidades_libros ul
        // WHERE ul.id_libro = $id
        // AND estado ='1'
        
        
        // ");

        $unidades = DB::SELECT('SELECT u .*,
         concat(u.unidad, " - ", u.nombre_unidad) as label_unidad,
         concat(u.unidad, " - ", u.nombre_unidad) as label, u.id_unidad_libro as id, l.url as weblibro
         FROM unidades_libros u, libro l 
         WHERE u.id_libro = l.idlibro 
         AND l.idlibro = ?
         ORDER BY u.unidad',[$id]);
        return $unidades;
       
    }


    public function store(Request $request){
        if( $request->id_unidad_libro ){
            $dato = Unidad::find($request->id_unidad_libro);
        }else{
            $dato = new Unidad();
        }
        $dato->id_libro = $request->id_libro;
        $dato->unidad = $request->unidad;
        $dato->nombre_unidad = $request->nombre_unidad;
        $dato->pag_inicio = $request->pag_inicio;
        $dato->pag_fin = $request->pag_fin;
        $dato->estado = '1';
        $dato->save();

        return $dato;
    }

    public function buscar_unidad_libro(Request $request){
        $buscarTemas = DB::select("SELECT t.*
        FROM temas t 
        WHERE id_unidad = $request->id_unidad_libro
        ");

        return $buscarTemas;
    }

    public function eliminar_unidad_libro(Request $request){
       $res =  DB::table('unidades_libros')
        ->where('id_unidad_libro', $request->id_unidad_libro)
        ->update(['estado' => "2"]);

        return $res;
    }
}

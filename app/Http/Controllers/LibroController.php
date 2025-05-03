<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;
use DB;

class LibroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $libro = Libro::Where('estado','1')->get();
        $libro = DB::SELECT("SELECT * FROM libro
        WHERE estado = '1'
        ORDER BY nombre ASC 
        ");
        return $libro;
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
        if(!empty($request->idlibro)){
            $libro = Libro::find($request->idlibro)->update($request->all());
            return $libro;
        }else{
            // $libro = new Libro($request->all());
            $libro = new Libro;
            $libro->nombre = $request->nombre;
            $libro->detalle = $request->detalle;
            $libro->url = $request->url;
            $libro->id_area = $request->id_area;
            $libro->c_primaria =uniqid();;

            $libro->save();
            return $libro;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Libro  $libro
     * @return \Illuminate\Http\Response
     */
    public function show(Libro $libro)
    {
        return $libro;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Libro  $libro
     * @return \Illuminate\Http\Response
     */
    public function edit(Libro $libro)
    {
        return $libro;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Libro  $libro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Libro $libro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Libro  $libro
     * @return \Illuminate\Http\Response
     */
    public function destroy(Libro $libro)
    {
        $libro = Libro::find($libro->idlibro)->update(['estado' => '0']);
        return $libro;
    }

        //api>>libroDocente
    public function docente(Request $request)
    {
    
        $libro = DB::select("SELECT *,libro.detalle, libro.url,libro.nombre,libro.idlibro  FROM usuario_has_libro ,libro
        WHERE usuario_has_libro.usuario_idusuario  = $request->idusuario
        and usuario_has_libro.estado = '1'
        and  libro.idlibro  = usuario_has_libro.asignatura_idasignatura 
        and libro.estado = '1'
  
      ");
      
     
       return $libro;
    }
}

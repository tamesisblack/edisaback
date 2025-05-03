<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\UsuarioHasLibro;
class AsignaturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $asignatura = DB::SELECT("SELECT * FROM asignatura WHERE estado = '1' ORDER BY fecha_create DESC");
        return $asignatura;
    }

    //api>>/traerLibros
    public function traerLibros(Request $request){
        $libros = DB::SELECT("SELECT * FROM libro WHERE estado = '1' ORDER BY fecha_create DESC");
        return $libros;
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
        if(!empty($request->idasignatura)){
            $asignatura = Asignatura::find($request->idasignatura)->update($request->all());
            return $asignatura;
        }else{
            $asignatura = new Asignatura($request->all());
            $asignatura->save();
            return $asignatura;
        }
    }
    //api >> /AsignaturasDocente
    public function AsignaturasDocente(Request $request){
        $asignaturas = DB::select("SELECT *,libro.detalle  FROM usuario_has_libro ,libro
           WHERE usuario_idusuario  = $request->id
           and usuario_has_libro.estado = '1'
           and  libro.idlibro  = usuario_has_libro.asignatura_idasignatura 

        ");
        return $asignaturas;
    }
    //api >>/e_asignaturasDocentes
    public function e_asignaturasDocentes(Request $request){
        $asignatura =   DB::table('usuario_has_libro')
        ->where('id', $request->id)
        ->update(['estado' => "0"]);
    }
    
    //api>> /guardarAsignaturasDocente
    public function guardarAsignaturasDocente(Request $request){


        $verificarIfAsignatura = DB::select("SELECT *  FROM usuario_has_libro 
        WHERE usuario_idusuario  = $request->usuario_idusuario
        and asignatura_idasignatura = $request->asignatura_idasignatura
        and estado = '1'
    

     ");

     if(count($verificarIfAsignatura) <=0){
            $asignaturaDocente = new UsuarioHasLibro;
            $asignaturaDocente->usuario_idusuario = $request->usuario_idusuario;
            $asignaturaDocente->asignatura_idasignatura  = $request->asignatura_idasignatura;
            $asignaturaDocente->save();

            if($asignaturaDocente){
                return "Se guardo corretamente";
            }else{
                return "No se pudo guardar";
            }
     }else{
       return "Ya existe una asignatura asignada a este  docente";
     }

        
    }   

    /**
     * Display the specified resource.
     *
     * @param  \App\Asignatura  $asignatura
     * @return \Illuminate\Http\Response
     */
    public function show(Asignatura $asignatura)
    {
        return $asignatura;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Asignatura  $asignatura
     * @return \Illuminate\Http\Response
     */
    public function edit(Asignatura $asignatura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Asignatura  $asignatura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Asignatura $asignatura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Asignatura  $asignatura
     * @return \Illuminate\Http\Response
     */
    public function destroy(Asignatura $asignatura)
    {
        $asignatura = Asignatura::find($asignatura->idasignatura)->update(['estado' => '0']);
        return $asignatura;
    }
}

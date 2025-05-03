<?php

namespace App\Http\Controllers;

use App\Models\CursoEstudiante;
use Illuminate\Http\Request;
use DB;
class CursoEstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $estudiantes = DB::SELECT("SELECT * FROM curso_estudiante JOIN curso on curso.codigo = curso_estudiante.codigo  WHERE curso_estudiante.usuario_idusuario = ? AND curso.estado = '1'",[$request->idusuario]);
        return $estudiantes;
    }

    public function alumnos(Request $request)
    {
        $estudiantes = DB::SELECT("SELECT usuario.* FROM usuario join curso_estudiante on curso_estudiante.usuario_idusuario = usuario.idusuario join curso on curso.codigo = curso_estudiante.codigo   WHERE curso_estudiante.codigo = ? AND curso.estado = '1'",[$request->codigo]);
        return $estudiantes;
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
        
     
            ///Para buscar el periodo

            $verificarperiodoinstitucion = DB::table('periodoescolar_has_institucion')
            
            ->select('periodoescolar_has_institucion.periodoescolar_idperiodoescolar')

            ->where('periodoescolar_has_institucion.institucion_idInstitucion','=',$request->institucion)
            ->get();
            

            foreach($verificarperiodoinstitucion  as $clave=>$item){
                $verificarperiodos =DB::SELECT("SELECT p.idperiodoescolar
                FROM periodoescolar p
                WHERE p.estado = '1'
                and p.idperiodoescolar = $item->periodoescolar_idperiodoescolar
                ");
            }
        

            if(count($verificarperiodoinstitucion) <=0){
                return ["status"=>"0", "message" => "No existe el periodo lectivo por favor, asigne un periodo a esta institucion"];
            }

        
        
            //verificar que el periodo exista
            if(count($verificarperiodos) <= 0){
                        
                return ["status"=>"0", "message" => "No existe el periodo lectivo por favor, asigne un periodo a esta institucion"];

            
            }
            
        //fin de busqueda del periodo
            //almancenar el periodo
        $periodo =  $verificarperiodos[0]->idperiodoescolar;

  

        // $verificarSiexisteCodigo = DB::SELECT("SELECT * FROM curso_estudiante WHERE codigo = ?  ",["$request->codigo"]);
        // if(count($verificarSiexisteCodigo) <=0){
            
                $curso = DB::SELECT("SELECT * FROM curso WHERE codigo = ? AND estado ='1' AND id_periodo = $periodo ",["$request->codigo"]);

           

                if(!empty($curso)){
                $res =   DB::INSERT("INSERT INTO `curso_estudiante`(`usuario_idusuario`, `codigo`) VALUES (?,?)",[$request->idusuario,"$request->codigo"]);
                    if($res){
                    
                        return ["status"=>"1", "message" => "Se ingreso correctamente"];
                    }else{
                    
                        return ["status"=>"0", "message" => "No se pudo ingresar/ o curso no se encuentra activo"];
                    }
                }else{
                    return ["status"=>"0", "message" => "No se pudo ingresar/ No se encontro el curso"];
                
                }

      //  }else{
            // return ["status"=>"0", "message" => "El codigo ya ha sido asignado a un estudiante o se encuentra en otro periodo"];
        

        //}


       


       

        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CursoEstudiante  $cursoEstudiante
     * @return \Illuminate\Http\Response
     */
    public function show(CursoEstudiante $cursoEstudiante)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CursoEstudiante  $cursoEstudiante
     * @return \Illuminate\Http\Response
     */
    public function edit(CursoEstudiante $cursoEstudiante)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CursoEstudiante  $cursoEstudiante
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CursoEstudiante $cursoEstudiante)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CursoEstudiante  $cursoEstudiante
     * @return \Illuminate\Http\Response
     */
    public function destroy(CursoEstudiante $cursoEstudiante)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;
use DB;
use Dirape\Token\Token;
class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $curso = DB::SELECT("SELECT * FROM curso WHERE  usuario_idusuario = ? OR estado ='0' ORDER BY fecha_create DESC",[$request->idusuario]);
        
        // $curso = DB::table('curso')
        // ->where('usuario_idusuario','=',$request->idusuario)
        // ->where('estado','<>','3')
        // ->get();
        // return $curso;

        $cursos = DB::select("SELECT c.* , l.detalle as detallelibro ,CONCAT(u.nombre, ' ', u.apellido) as docente
        FROM curso c
        LEFT JOIN libro l ON  c.asignatura_idasignatura  = l.idlibro 
        LEFT JOIN usuario u ON c.usuario_idusuario  = u.idusuario 
        WHERE c.estado <> '3'
        and usuario_idusuario = $request->idusuario

        ");
        return $cursos;

      
    }
    //api:get>>/cursosAdmin
    public function cursosAdmin(Request $request){
        $cursos = DB::select("SELECT c.* , l.detalle as detallelibro ,CONCAT(u.nombre, ' ', u.apellido) as docente
        FROM curso c
        LEFT JOIN libro l ON  c.asignatura_idasignatura  = l.idlibro 
        LEFT JOIN usuario u ON c.usuario_idusuario  = u.idusuario 

        ");
        return $cursos;
    }

    //api::get>>/cursosArchivados
    public function cursosArchivados(Request $request){
        $cursos = DB::select("SELECT c.* , l.detalle as detallelibro ,CONCAT(u.nombre, ' ', u.apellido) as docente
        FROM curso c
        LEFT JOIN libro l ON  c.asignatura_idasignatura  = l.idlibro 
        LEFT JOIN usuario u ON c.usuario_idusuario  = u.idusuario 
        WHERE c.estado = '2'

        ");
        return $cursos;

    }

    function generateRandomString($length = 8) {
        $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ23456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function generateRandomString2($length = 8) {
        $characters = '23456789ABCDEFGHJKMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function generateRandomString3($length = 8) {
        $characters = 'TUVWXYZ234ABCDEFGHJKMNOPQRS56789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function codigo(){
        $generarCodigo = $this->generateRandomString();
        $verificarIfExistsCode = DB::table("curso")
        ->where('codigo',$generarCodigo)
        ->where('estado','1')
        ->count();
         $contador =$verificarIfExistsCode;
     
      //primera validacion
       if($contador <=0){
            $data=[
            'codigo'=>$generarCodigo
            ];
            return $data;
       }
       else{
           
            $generarCodigo2 = $this->generateRandomString2();

            $verificarIfExistsCode2 = DB::table("curso")
            ->where('codigo',$generarCodigo2)
            ->where('estado','1')
            ->count();
            $contador2 =$verificarIfExistsCode2;
            //segunda validacion
            if($contador2 <=0){
                $data=[
                'codigo'=>$generarCodigo2
                ];
                return $data;

            }else{
                $generarCodigo3 = $this->generateRandomString3();

                $verificarIfExistsCode3 = DB::table("curso")
                ->where('codigo',$generarCodigo3)
                ->where('estado','1')
                ->count();
                $contador3 =$verificarIfExistsCode3;
                //segunda validacion
                if($contador3 <=0){
                    $data=[
                    'codigo'=>$generarCodigo3
                    ];
                    return $data;

                }else{
                    return "Hay que volver a recargar la pagina para generar mas codigos";
                }

              
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
        if($request->docente){
            $curso =  Curso::findOrFail($request->idcurso);
            $curso->nombre = $request->nombre;
            $curso->paralelo = $request->paralelo;
            $curso->anio = $request->anio;
            $curso->asignatura_idasignatura  = $request->asignatura_idasignatura;
            $curso->estado  = $request->estado;
            $curso->save();
            if($curso){
                return "Se guardo correctamente";
    
            }else{
                return "No se pudo guardar";
            }

        }
        if(!empty($request->idcurso)){
            $curso =  Curso::findOrFail($request->idcurso);
            $curso->nombre = $request->nombre;
            $curso->paralelo = $request->paralelo;
            $curso->anio = $request->anio;
            $curso->usuario_idusuario  = $request->usuario_idusuario;
            $curso->codigo = $request->codigo;
            $curso->asignatura_idasignatura  = $request->asignatura_idasignatura;
            $curso->estado  = $request->estado;
        }else{

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

            $curso = new Curso;
            $curso->nombre = $request->nombre;
            $curso->paralelo = $request->paralelo;
            $curso->anio = $request->anio;
            $curso->usuario_idusuario  = $request->usuario_idusuario;
            $curso->codigo = $request->codigo;
            $curso->asignatura_idasignatura  = $request->asignatura_idasignatura;
            $curso->id_periodo = $periodo;
            $curso->estado  = "0";

            
        }
        $curso->save();
        if($curso){
            return ["status"=>"1", "message" => "Se guardo correctamente"];
          

        }else{
            return ["status"=>"1", "message" => "No se pudo guardar"];
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function show(Curso $curso)
    {
        return $curso;

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function edit(Curso $curso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Curso $curso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curso $curso)
    {
        $curso = Curso::find($curso->idcurso)->update(['estado' => '0']);
        return $curso;
    }
}

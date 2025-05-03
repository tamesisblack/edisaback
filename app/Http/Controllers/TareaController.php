<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Respuesta;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tarea = DB::SELECT("SELECT * FROM tarea WHERE curso_idcurso = ? AND estado = '1' AND tipo = '0' ",[$request->idcurso]);
        return $tarea;
    }
    
    public function tareaEstudiantePendiente(Request $request){
        $data=array();
        $fechaActual  = date('Y/m/d'); 
     
        $tarea = DB::SELECT("SELECT * 
        FROM tarea
        join archivo on archivo.idarchivo = tarea.archivo_idarchivo
        WHERE tarea.curso_idcurso = ? 
        AND tarea.estado = '1' 
        AND tarea.fecha_fin >= '$fechaActual'
        ",[$request->idcurso]
        );

   
        foreach ($tarea as $key => $post) {
            $verifica = DB::SELECT("SELECT * FROM respuesta WHERE tarea_idtarea = ? AND usuario_idusuario = ?",[$post->idtarea,$request->idusuario]);
            if(!empty($verifica)){
                
            }else{
                array_push ($data , $post);
            }
        }
        return $data;
    }
    public function tareaEstudianteRealizada(Request $request){

        if($request->docente){

            $TareasEstudiante = DB::select("SELECT t.* ,r.idrespuesta ,r.year,r.mes, r.url ,r.descripcion,r.nota,r.observacion ,CONCAT(u.nombre, ' ', u.apellido) as estudiante, u.cedula,r.nombre
            FROM tarea t, respuesta r , usuario u
            WHERE  r.tarea_idtarea = t.idtarea
            AND  r.usuario_idusuario = u.idusuario 
            AND  t.curso_idcurso = $request->idcurso
            AND   t.estado = '1'
            AND   tipo = '0'
            AND  r.tarea_idtarea = $request->idtarea
            
            ");

            return $TareasEstudiante;
        }

        else{

            $data=array();
            $tarea = DB::SELECT("SELECT * FROM tarea WHERE curso_idcurso = ? AND estado = '1' ",[$request->idcurso]);
            foreach ($tarea as $key => $post) {
                $verifica = DB::SELECT("SELECT * FROM respuesta join tarea on tarea.idtarea = respuesta.tarea_idtarea  WHERE tarea_idtarea = ? AND usuario_idusuario = ?",[$post->idtarea,$request->idusuario]);
                if(!empty($verifica)){
                    array_push ($data , $verifica[0]);
                }else{
                }
            }
            return $data;
        }   
      
    }

    public function editarCalificacionTarea(Request $request){
        $verificarsiExiste = DB::select("SELECT * FROM respuesta WHERE idrespuesta = $request->idrespuesta");

        if(!empty($verificarsiExiste)){
             DB::table('respuesta')
            ->where('idrespuesta', $request->idrespuesta)
            ->update([
                'nota' => $request->nota,
                'observacion' => $request->observacion
        ]);

            return ["status"=> "1","message" => "Se guardo correctamente"];
        }else{
            return ["status"=> "0","message" => "Ocurrio un error al guardar"];
        }
  
    }

    public function eliminarTareaEstudiante(Request $request){
        $verificarsiExiste = DB::select("SELECT * FROM respuesta WHERE idrespuesta = $request->idrespuesta");

        if(!empty($verificarsiExiste)){
            
            $year = $request->year;
            $mes  = $request->mes;
            $archivo  = $request->url;
                        
            if(file_exists('archivos/tareas/'.$year.'/'.$mes.'/'.$archivo) ){
                unlink('archivos/tareas/'.$year.'/'.$mes.'/'.$archivo);
                

            }   
           
            $archivo = Respuesta::findOrFail($request->idrespuesta);
            $archivo->delete();

            return ["status"=> "1","message" => "Se devolvio correctamente la tarea"];
        }else{
            return ["status"=> "0","message" => "No se pudo devolver la tarea al estudiante"];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty($request->idarea)){
            $tarea = Tarea::find($request->idarea)->update($request->all());
            return $tarea;
        }else{
            $tarea = new Tarea($request->all());
            $tarea->save();
            return $tarea;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function show(Tarea $tarea)
    {
        return $tarea;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarea $tarea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarea $tarea)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarea $tarea)
    {
        $tarea = Tarea::find($tarea->idarea)->update(['estado' => '0']);
        return $tarea;
    }
}

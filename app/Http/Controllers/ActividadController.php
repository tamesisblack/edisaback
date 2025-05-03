<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadHasCurso;
use App\Models\RespuestaActividad;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Tarea;
class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if(!empty($request->id)){
            // $actividades = DB::SELECT("SELECT * FROM actividad WHERE libro_idlibro = ? AND estado='1'",[$request->id]);
            $actividades= DB::SELECT("SELECT t.nombre_tema, a.*, t.id as idtema ,ul.nombre_unidad, ul.id_unidad_libro , ul.unidad, lib.url as weblibro, lib.nombre as nombrelibro, concat(ul.unidad, ' - ', ul.nombre_unidad) as label
            FROM temas t, actividad a,  unidades_libros ul, libro lib
            WHERE t.id_asignatura = $request->id
            and t.id_asignatura = lib.idlibro
            and a.id_tema = t.id
            and ul.id_unidad_libro = t.id_unidad
            ");


            return $actividades;
        }




    }




    public function store(Request $request)
    {


        if( $request->idactividad ){
            $actividad = Actividad::find($request->idactividad);
        }else{
            $actividad = new Actividad();
        }


        $actividad->id_tema = $request->id_tema;
        $actividad->url = $request->url;
        $actividad->page = $request->page;
        $actividad->libro_idlibro = $request->libro_idlibro;
        $actividad->nombre = $request->carpeta;

        $actividad->save();

        return $actividad;
    }

    //para guardar la asignacion actividad - curso
    public function guardarActividadCurso(Request $request){

        //verificar si ya ha sido la actividad en el curso
        $verificar = DB::select("SELECT *
        FROM tarea
        WHERE curso_idcurso = $request->curso_id
        AND actividad_idactividad =  $request->actividad_id
        AND tipo ='1'

        ");

        if(!empty($verificar)){
            return ["status" => "0", "message" => "Actividad ya se encuentra asignada al curso"];
        }else{
            $actividad = new Tarea();
            $actividad->detalle = $request->detalle;
            $actividad->actividad_idactividad  = $request->actividad_id;
            $actividad->curso_idcurso  = $request->curso_id;
            $actividad->fecha_inicio = $request->fecha_inicio;
            $actividad->fecha_fin = $request->fecha_fin;
            $actividad->nombre_actividad = $request->nombre_actividad;
            $actividad->estado = "1";
            $actividad->tipo = "1";

            $actividad->save();


            if($actividad){
                return ["status" => "1", "message" => "Se guardo correctamente"];
            }else{
                return ["status" => "0", "message" => "No se pudo guardar"];
            }
        }






    }

    //para ver las actividades asignadas del maestro
    public function ActividadesAsignadas(Request $request){

        $actividades = DB::SELECT("SELECT ac.* , a.url, a.page, a.nombre
        FROM tarea ac
        LEFT JOIN actividad a ON ac.archivo_idarchivo  = a.idactividad
        WHERE ac.curso_idcurso  = ?
        AND ac.estado = '1' ",[$request->idcurso]

        );

        $actividades = DB::SELECT("SELECT ac.* , a.url, a.page, a.nombre
        FROM tarea ac
        LEFT JOIN actividad a ON ac.actividad_idactividad  = a.idactividad
        WHERE ac.curso_idcurso  = ?
        AND ac.estado = '1'
        AND ac.tipo = '1'
        ",[$request->idcurso]

        );


        return $actividades;
    }

    public function actividadEstudiantePendiente(Request $request){
        $data=array();
        $fechaActual  = date('Y/m/d');
        $tarea = DB::SELECT("SELECT * FROM tarea join actividad on actividad.idactividad = tarea.actividad_idactividad
         WHERE tarea.curso_idcurso  = ? AND tarea.estado = '1'  AND tarea.tipo = '1'
         AND tarea.fecha_fin >= '$fechaActual'
         ",[$request->idcurso]
        );

        foreach ($tarea as $key => $post) {
            $verifica = DB::SELECT("SELECT * FROM respuesta_actividad WHERE actividad_id = ? AND usuario_idusuario = ?",[$post->idtarea,$request->idusuario]);
            if(!empty($verifica)){
            }else{
                array_push ($data , $post);
            }
        }
        return $data;
    }

    public function ActividadEstudianteRealizada(Request $request){


        if($request->docente){

            $actividadEstudiante = DB::select("SELECT t.* ,r.res_actividad_id ,r.year,r.mes, r.url ,r.descripcion,r.nota,r.comentario ,CONCAT(u.nombre, ' ', u.apellido) as estudiante, u.cedula
            FROM tarea t, respuesta_actividad r , usuario u
            WHERE  r.actividad_id = t.idtarea
            AND  r.usuario_idusuario = u.idusuario
            AND  t.curso_idcurso = $request->idcurso
            AND   t.estado = '1'
            AND   tipo = '1'
            AND  r.actividad_id = $request->idtarea
            ");

            return $actividadEstudiante;

        }
        else{
            $data=array();
            $tarea = DB::SELECT("SELECT * FROM tarea WHERE curso_idcurso = ? AND estado = '1' AND tipo ='1' ",[$request->idcurso]);
            foreach ($tarea as $key => $post) {
                $verifica = DB::SELECT("SELECT * FROM respuesta_actividad join tarea on tarea.idtarea = respuesta_actividad.actividad_id  WHERE actividad_id = ? AND usuario_idusuario = ?",[$post->idtarea,$request->idusuario]);
                if(!empty($verifica)){
                    array_push ($data , $verifica[0]);
                }else{
                }
            }
            return $data;
        }

    }

    public function editarCalificacionActividad(Request $request){

        $verificarsiExiste = DB::select("SELECT * FROM respuesta_actividad WHERE res_actividad_id = $request->res_actividad_id");

        if(!empty($verificarsiExiste)){
             DB::table('respuesta_actividad')
            ->where('res_actividad_id', $request->res_actividad_id)
            ->update([
                'nota' => $request->nota,
                'comentario' => $request->comentario
        ]);

            return ["status"=> "1","message" => "Se guardo correctamente"];
        }else{
            return ["status"=> "0","message" => "Ocurrio un error al guardar"];
        }

    }

    public function eliminarActividadEstudiante(Request $request){


        $verificarsiExiste = DB::select("SELECT * FROM respuesta_actividad WHERE res_actividad_id = $request->res_actividad_id");

        if(!empty($verificarsiExiste)){

            $year = $request->year;
            $mes  = $request->mes;
            $archivo  = $request->url;

            if(file_exists('archivos/tareas/'.$year.'/'.$mes.'/'.$archivo) ){
                unlink('archivos/tareas/'.$year.'/'.$mes.'/'.$archivo);


            }

            $archivo = RespuestaActividad::findOrFail($request->res_actividad_id);
            $archivo->delete();

            return ["status"=> "1","message" => "Se devolvio correctamente la tarea"];
        }else{
            return ["status"=> "0","message" => "No se pudo devolver la tarea al estudiante"];
        }
    }

    public function temasUnidad(Request $request)
    {
        $temas = DB::SELECT("SELECT t.nombre_tema, t.id AS id, t.nombre_tema AS label, t.id_asignatura, t.unidad, l.nombre
        FROM temas t, libro l
        WHERE t.id_asignatura = l.idlibro
        AND t.id_unidad = $request->unidad
        AND t.id_asignatura = $request->asignatura
        AND t.estado = 1
        ORDER BY  t.nombre_tema + 0 ASC
        ");

        return $temas;
    }

    //api:get>>/api/actividadEliminar?
    public function actividadEliminar(Request $request)
    {


        $actividad = DB::table('actividad')
            ->where('idactividad', $request->idactividad)
            ->update(['estado' => $request->estado]);
        return $actividad;
    }

    public function actividades_libros_unidad($id_unidad)
    {
        $actividades = DB::SELECT('SELECT * FROM
        actividad aa, temas t
        WHERE aa.id_tema = t.id
        AND t.id_unidad = ?',[$id_unidad]

        );

        return $actividades;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Actividad  $actividad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Actividad $actividad)
    {
        $actividad = Actividad::find($actividad->idactividad)->update(['estado' => '0']);
        return $actividad;
    }
}

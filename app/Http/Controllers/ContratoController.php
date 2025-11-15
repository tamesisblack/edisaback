<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contrato;
use App\Models\Institucion;
use App\Models\Usuario;
use App\Models\Codigo;
use App\Models\HistoricoChangeContrato;
use App\Models\PeriodosInstitucion;
use Illuminate\Support\Facades\DB;
class ContratoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contratos = DB::SELECT("SELECT c.* ,i.nombre as institucion,
        p.periodoescolar as periodo,
        IF(u.apellido is null,CONCAT(u.cedula,' ',u.nombre),CONCAT(u.cedula,' ',u.nombre,' ',u.apellido)) as docente
        FROM contratos c
        LEFT JOIN institucion i on c.id_institucion = i.idinstitucion
        LEFT JOIN usuario u  on c.id_docente = u.idusuario
        LEFT JOIN periodoescolar p  ON c.id_periodo = p.idperiodoescolar

        ORDER  BY c.contrato_id  DESC
        ");
        $instituciones = DB::SELECT("SELECT institucion.* FROM institucion ORDER BY idinstitucion DESC");
        $docentes = DB::SELECT("SELECT CONCAT(nombre, ' ',apellido) as docente ,idusuario, persona_id FROM usuario WHERE grupo_idgrupo  = '2' ORDER BY idusuario DESC");
        return["contratos" => $contratos, "instituciones" => $instituciones, "docentes" => $docentes];
    }
    //api:get/contratosXPeriodo?id_periodo=1
    public function contratosXPeriodo(Request $request){
        $contratos = DB::SELECT("SELECT c.* ,i.nombre as institucion,
        p.periodoescolar as periodo,
        IF(u.apellido is null,CONCAT(u.cedula,' ',u.nombre),CONCAT(u.cedula,' ',u.nombre,' ',u.apellido)) as docente
        FROM contratos c
        LEFT JOIN institucion i on c.id_institucion = i.idinstitucion
        LEFT JOIN usuario u  on c.id_docente = u.idusuario
        LEFT JOIN periodoescolar p  ON c.id_periodo = p.idperiodoescolar
        WHERE c.id_periodo = '$request->id_periodo'
        ORDER  BY c.contrato_id  DESC
        ");
        return $contratos;
    }
    //para desactivar/activar los contratos
    public function contratodesactivar(Request $request)
    {
        DB::table('contratos')
        ->where('contrato_id', $request->contrato_id)
        ->update(['estado' => $request->estado]);
    }
    public function contratoEliminarBd(Request $request){
        //validar que no tenga validaciones
        $query = DB::SELECT("SELECT * FROM verificacion_codigo c
        WHERE c.contrato = '$request->contrato'
        ");
        if(count($query) > 0){
            return ["status" => "0", "message" => "No se puede eliminar el contrato $request->contrato porque tiene verificaciones"];
        }
        $contrato = Contrato::findOrFail($request->contrato_id)->delete();
    }
    //para traer los profesores por institucion
    public function traerprofesor(Request $request){
        $institucion = $request->idinstitucion;
        $profesores = DB::SELECT("SELECT u.idusuario, u.cedula,
        IF(u.apellido is null,CONCAT(u.cedula,' ',u.nombre),CONCAT(u.cedula,' ',u.nombre,' ',u.apellido)) as docente
        FROM usuario u
        where u.grupo_idgrupo = '2'
        AND u.institucion_idinstitucion = '$institucion'
        AND u.estado = '1'
        ");
        return $profesores;
    }
    //para guardar o actualizar el contrato
    public function contratoGuardar(Request $request){
        if($request->contrato_id){
            $contrato = Contrato::findOrFail($request->contrato_id);
            $contrato->id_docente = $request->id_docente;
            $contrato->save();
            if($contrato){
                $historico = new HistoricoChangeContrato();
                $historico->contrato            = $request->contrato;
                $historico->docente_anterior    = $request->docente_anterior;
                $historico->docente_nuevo       = $request->id_docente;
                $historico->usuario_editor      = $request->usuario_editor;
                $historico->institucion_id      = $request->institucion_id;
                $historico->periodo_id          = $request->periodo_id;
                $historico->observacion         = "Se cambio de docente el contrato";
                $historico->save();

                return "Se guardo correctamente";
            }else{
                return "No se pudo guardar/actualizar";
            }
        }else{
            $contrato = new Contrato;
            $contrato->id_docente = $request->id_docente;
            $contrato->id_periodo = $request->id_periodo;
        }
        $contrato->save();
        if($contrato){
            return "Se guardo correctamente";
        }else{
            return "No se pudo guardar/actualizar";
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
          //para actualizar el estado del contrato
        if($request->contrato_id){
            $contrato = Contrato::find($request->contrato_id);
            $contrato->estado = $request->estado;

            $contrato->save();

    //para hacer un post
        }else{

            $contrato =  new Contrato;
            $contrato->cod_contrato = $request->cod_contrato;
            $contrato->ciudad = $request->ciudad;
            $contrato->year = $request->year;
            $contrato->id_temporada = $request->id_temporada;
            $contrato->id_institucion = $request->id_institucion;
            $contrato->id_docente = $request->id_docente;
            $contrato->save();

        }
        return $contrato;
    }

    public function generarContrato(Request $request){
        $verificarcontratos = DB::table('contratos')
        ->select('contratos.cod_contrato')
        ->where('contratos.cod_contrato','=',$request->cod_contrato)
         ->get();
         $id_periodo = 0;
        //verificar que existe el contrato
        if(count($verificarcontratos) <= 0){
            //verificar que existe la institucion
            $traerinstitucion = $request->id_institucion;
            $verificarinstitucion = DB::table('institucion')
            ->select('institucion.idinstitucion')
            ->where('institucion.temporal_id','=',$traerinstitucion)
            ->get();
                //LIMITAR LAS FECHAS LIMITE PERIODO
                $EncontrarRegion =  $request->id_temporada;
                    $pos = strpos($EncontrarRegion, "C");
                    if ($pos === false) {
                        $region = "1";
                        //validar que el periodo aun no termine la fecha limite
                        $todate  = date('Y-m-d H:i:s');
                        $filtrarPeriodo = DB::SELECT("SELECT * FROM  periodoescolar p
                        WHERE p.region_idregion = '1'
                        AND p.estado = '1'
                        ORDER BY p.idperiodoescolar DESC
                        ");
                        //si hay mas de 1 periodo activo mostrar hay varios periodos activos... comunicarse con soporte
                        if(count($filtrarPeriodo)>1){
                            return ["status" => "0","message"=>"hay varios periodos activos... comunicarse con soporte"];
                        }
                        if(count($filtrarPeriodo)>0){
                            $f_final = $filtrarPeriodo[0]->fecha_final;
                            if($todate > $f_final){
                                return ["status" => "0","message"=>"Ya paso la fecha limite del periodo debe crear uno nuevo"];
                            }
                        }else{
                            return ["status" => "0","message"=>"No existe periodo para la region sierra"];
                        }
                        $id_periodo = $filtrarPeriodo[0]->idperiodoescolar;
                    } else {
                        $region = "2";
                        //validar que el periodo aun no termine la fecha limite
                        $todate  = date('Y-m-d');
                        $filtrarPeriodo = DB::SELECT("SELECT * FROM  periodoescolar p
                        WHERE p.region_idregion = '2'
                        AND p.estado = '1'
                        ORDER BY p.idperiodoescolar DESC
                        ");
                        //si hay mas de 1 periodo activo mostrar hay varios periodos activos... comunicarse con soporte
                        if(count($filtrarPeriodo)>1){
                            return ["status" => "0","message"=>"hay varios periodos activos... comunicarse con soporte"];
                        }
                        if(count($filtrarPeriodo)>0){
                            $f_final = $filtrarPeriodo[0]->fecha_final;
                            if($todate >= $f_final){
                            return ["status" => "0","message"=>"Ya paso la fecha limite del periodo debe crear uno nuevo"];
                            }
                        }else{
                            return ["status" => "0","message"=>"No existe periodo para la region costa"];
                        }
                        $id_periodo = $filtrarPeriodo[0]->idperiodoescolar;
                    }
                //si no existe la institucion
                if(count($verificarinstitucion) <= 0){
                    $institucion = new Institucion();
                    $institucion->temporal_id  = $traerinstitucion;
                    $institucion->nombre = $request->nombre_institucion;
                    $institucion->region = $region;
                    $institucion->save();
                    $ingresoInstitucion = $institucion->idinstitucion;
                }else{
                    $ingresoInstitucion =$verificarinstitucion[0]->idinstitucion;
                }
            //Fin de validar la institucion
            //verificar que existe el profesor
            $traerdocente = $request->id_docente;
            $traercedula  = $request->docente_cedula;
            $traeremail   = $request->docente_email;
            //validar
            $verificardocente = DB::SELECT("SELECT idusuario,email,persona_id
            FROM usuario u
            WHERE u.persona_id = '$traerdocente'
            AND u.cedula = '$traercedula'
            ");
            if(count($verificardocente) <= 0){
                //validar que la cedula no existe ni el email
                $verificardocente = DB::SELECT("SELECT idusuario,email,persona_id
                FROM usuario u
                WHERE u.cedula = '$traercedula'
                ");
                if(count($verificardocente) > 0){
                    return ["status" => "0", "message" => "La cédula ya existe"];
                }
                $apellido = $request->input('docente_apellidos');
                $docente = new Usuario();
                $docente->persona_id  = $traerdocente;
                $docente->nombre = $request->docente_nombres;
                $docente->apellido = ($apellido === null || trim($apellido) === '' || strtolower(trim($apellido)) === 'null') ? null : $apellido;
                $docente->apellido  = $request->docente_apellidos?? null;
                $docente->cedula  = $request->docente_cedula;
                $docente->email   = $request->docente_email;
                $docente->password = bcrypt($request->docente_cedula);
                $docente->telefono  = $request->docente_telefono;
                $docente->genero = $request->docente_genero;
                $docente->institucion_idinstitucion   =$ingresoInstitucion;
                $docente->grupo_idgrupo  = 2;
                $docente->save();
                //Si todo sale bien ingresar el contrato
                if(($request->cod_contrato != null)){
                    $contrato = new Contrato();
                    $contrato->cod_contrato = $request->cod_contrato;
                    $contrato->ciudad = $request->ciudad;
                    $contrato->year = $request->year;
                    $contrato->id_temporada = $request->id_temporada;
                    $contrato->id_institucion = $ingresoInstitucion;
                    $contrato->id_docente = $docente->idusuario;
                    $contrato->id_periodo = $id_periodo;
                    $contrato->save();
                }
                else{
                    return "no se pudo ingresar el contrato";
                }
                //si existe el docente
            } else{
                $siexiste_docente = $verificardocente[0]->idusuario;
                //Si todo sale bien ingresar el contrato
                if(($request->cod_contrato != null)){
                    $contrato = new Contrato();
                    $contrato->cod_contrato = $request->cod_contrato;
                    $contrato->ciudad = $request->ciudad;
                    $contrato->year = $request->year;
                    $contrato->id_temporada = $request->id_temporada;
                    $contrato->id_institucion = $ingresoInstitucion;
                    $contrato->id_docente = $siexiste_docente;
                    $contrato->save();
                }
                else{
                    return "no se pudo ingresar el contrato";
                }
            }
        }else{
            return "ya existe el contrato";
        }
        return $contrato;


    }


    //api para mostrar los contrato del  docente
    public function contratodocente(Request $request){
        $institucion = $request->institucion;
        $docente     = $request->docente;
        //region de institucion
        $query = DB::SELECT("SELECT * FROM institucion i
        WHERE i.idinstitucion = '$institucion'
        ");
        $region = $query[0]->region;
        //obtener periodos
        $query2 = DB::SELECT("SELECT * FROM periodoescolar p
        WHERE p.region_idregion = '$region'
        AND p.estado = '1'");
        if(empty($query2)){
            return ["status" => "0", "message" => "No existe un periodo activo en la region"];
        }
        //si existe de mas de  periodos activos
        if(count($query2) > 1){
            return ["status" => "0", "message" => "Existe mas de 2 periodos activos en la misma region"];
        }
        $periodo = $query2[0]->idperiodoescolar;
        //verificar que la institucion tenga el periodo ultimo activo
        $query3 = DB::SELECT("SELECT * FROM periodoescolar_has_institucion ph
        WHERE ph.periodoescolar_idperiodoescolar = '$periodo'
        AND ph.institucion_idInstitucion         = '$institucion'
        ");
        //si no existe existe el periodo ultimo activo en la instiucion lo creo
        if(empty($query3)){
            $dato = new PeriodosInstitucion();
            $dato->periodoescolar_idperiodoescolar = $periodo;
            $dato->institucion_idInstitucion = $institucion;
            $dato->save();
        }

        //===PROCESO=====
        //actualizar los contratos con periodo 0
        DB::UPDATE("UPDATE contratos SET id_periodo = '$periodo' WHERE id_docente = '$docente' AND id_periodo = '0'");
        //traer contratos
        $traercontratos = DB::SELECT("SELECT * FROM contratos c
        WHERE c.id_docente = '$docente'
        AND c.id_institucion = '$institucion'
        AND c.id_periodo = '$periodo'
        AND c.verificacion_estado = '0'
        AND c.estado = '1'
        ");
        return $traercontratos;
    }
    public function verificacionesInfo(Request $request){
        $verificarperiodoinstitucion = DB::table('periodoescolar_has_institucion')
        ->select('periodoescolar_has_institucion.periodoescolar_idperiodoescolar')
        ->where('periodoescolar_has_institucion.institucion_idInstitucion','=',$request->institucion_id)
        ->orderBy('periodoescolar_idperiodoescolar','desc')
        ->get();
        $datos =[];
        $contador = 1;
        if(count($verificarperiodoinstitucion)>0){
            $periodo =  $verificarperiodoinstitucion[0]->periodoescolar_idperiodoescolar;
            $traerverificacion = DB::table('verificacion_codigo')
            ->select('verificacion_codigo.*')
            ->where('verificacion_codigo.contrato','=',$request->contrato)
            ->where('verificacion_codigo.institucion_id','=',$request->institucion_id)
            ->orderBy('verificacion_codigo.verificacion_id', 'asc')
            ->get();
            if(count($traerverificacion) == 0){
                return ["status" => "0","message" => "No existe verificaciones para este contrato"];
            }
            foreach($traerverificacion as $key => $item){
                $verCodigos = DB::select("SELECT  c._id
                    from codigos c
                    LEFT JOIN usuario u ON  c.profesor_id = u.idusuario
                    LEFT JOIN libro l ON c.idlibro = l.c_primaria
                    where c.id_verificacion = '$item->verificacion_id'
                    and c.estado  = '1'
                ");
                $datos[$key] = [
                    "num_verificacion" => $contador,
                    "contrato" => $item->contrato,
                    "created_at" => $item->created_at,
                    "descripcion" => $item->descripcion,
                    "docente_id" => $item->docente_id,
                    "estado" => $item->estado,
                    "fecha_finalizacion"=> $item->fecha_finalizacion,
                    "fecha_inicio" => $item->fecha_inicio,
                    "id_periodo" => $item->id_periodo,
                    "institucion_id" => $item->institucion_id,
                    "updated_at" => $item->updated_at,
                    "verificacion_id" => $item->verificacion_id,
                    "codigos" => count($verCodigos)
                ];
                $contador++;
            }
            return $datos;
        }else{
            return ["status" => "0","message" => "No existe el periodo"];
        }


    }

    //api para mostrar los listado de verificaciones del  docente
    public function verificacionlistado(Request $request){
            $docente = $request->idusuario;
            $institucion= $request->institucion_idinstitucion;
            //verificar si la institucion tiene periodo y a cual pertenece  el docente
            $verificarperiodoinstitucion = DB::table('periodoescolar_has_institucion')
            ->select('periodoescolar_has_institucion.periodoescolar_idperiodoescolar')
            ->where('periodoescolar_has_institucion.institucion_idInstitucion','=',$institucion)
            ->orderBy('periodoescolar_idperiodoescolar','desc')
            ->get();
            $periodo =  $verificarperiodoinstitucion[0]->periodoescolar_idperiodoescolar;
            //verificar que existe el periodo
            if(count($verificarperiodoinstitucion) <= 0){
                return "no existe el periodo para la institucion";
            }else{
                $datos = [];
                //para la verificacion anterior
                if($request->verificacion){
                    //traer los contratos de los docentes de acuerdo al periodo
                    $traerverificacion = DB::table('verificacion_codigo')
                    ->select('verificacion_codigo.*')
                    ->where('verificacion_codigo.docente_id','=',$docente)
                    ->where('verificacion_codigo.id_periodo','<>',$periodo)
                    ->where('verificacion_codigo.institucion_id','=',$institucion)
                    // ->where('verificacion_codigo.estado','=',"1")
                    ->orderBy('fecha_inicio', 'desc')
                    ->get();
                    foreach($traerverificacion as $key => $item){
                        $verCodigos = DB::select("SELECT  c._id
                            from codigos c
                            LEFT JOIN usuario u ON  c.profesor_id = u.idusuario
                            LEFT JOIN libro l ON c.idlibro = l.c_primaria
                            where c.id_verificacion = '$item->verificacion_id'
                            and c.estado  = '1'
                        ");
                        $datos[$key] = [
                            "contrato" => $item->contrato,
                            "created_at" => $item->created_at,
                            "descripcion" => $item->descripcion,
                            "docente_id" => $item->docente_id,
                            "estado" => $item->estado,
                            "fecha_finalizacion"=> $item->fecha_finalizacion,
                            "fecha_inicio" => $item->fecha_inicio,
                            "id_periodo" => $item->id_periodo,
                            "institucion_id" => $item->institucion_id,
                            "updated_at" => $item->updated_at,
                            "verificacion_id" => $item->verificacion_id,
                            "codigos" => count($verCodigos)
                        ];
                    }
                    return $datos;
                    //para la verificacion actual
                }else{
                    //traer los contratos de los docentes de acuerdo al periodo
                    $traerverificacion = DB::table('verificacion_codigo')
                    ->select('verificacion_codigo.*')
                    ->where('verificacion_codigo.docente_id','=',$docente)
                    ->where('verificacion_codigo.id_periodo','=',$periodo)
                    ->where('verificacion_codigo.institucion_id','=',$institucion)
                    // ->where('verificacion_codigo.estado','=',"1")
                    ->orderBy('verificacion_id', 'asc')
                    ->get();
                    foreach($traerverificacion as $key => $item){
                        $verCodigos = DB::select("SELECT  c._id
                            from codigos c
                            LEFT JOIN usuario u ON  c.profesor_id = u.idusuario
                            LEFT JOIN libro l ON c.idlibro = l.c_primaria
                            where c.id_verificacion = '$item->verificacion_id'
                            and c.estado  = '1'
                        ");
                        $datos[$key] = [
                            "contrato" => $item->contrato,
                            "num_verificacion" => $item->num_verificacion,
                            "created_at" => $item->created_at,
                            "descripcion" => $item->descripcion,
                            "docente_id" => $item->docente_id,
                            "estado" => $item->estado,
                            "fecha_finalizacion"=> $item->fecha_finalizacion,
                            "fecha_inicio" => $item->fecha_inicio,
                            "id_periodo" => $item->id_periodo,
                            "institucion_id" => $item->institucion_id,
                            "updated_at" => $item->updated_at,
                            "verificacion_id" => $item->verificacion_id,
                            "codigos" => count($verCodigos)
                        ];
                    }
                    return $datos;

                }

            }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    public function contratoVerificacion($contrato,$numero){
        $buscarVerificacionesContrato = DB::select("SELECT  v.*
        FROM  verificacion_codigo v
        WHERE contrato = '$contrato'
        AND num_verificacion = '$numero'
        ");
        if(count($buscarVerificacionesContrato) <=0){
            return ["status" => 0, "message" => "No hay verificaciones para ese contrato"];
        }else{
            foreach($buscarVerificacionesContrato as $clave => $item){
                $verCodigos = DB::select("SELECT  c._id
                    from codigos c
                    LEFT JOIN usuario u ON  c.profesor_id = u.idusuario
                    LEFT JOIN libro l ON c.idlibro = l.c_primaria
                    where c.id_verificacion = '$item->verificacion_id'
                    and c.estado  = '1'
                ");
                $data[$clave] = [
                    'verificacion' => $verCodigos

                ];
            }
            return $data;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function showCodigos($contrato){
        //C-S22-0000007-AC
        $buscarVerificacionesContrato = DB::select("SELECT c._id AS codigo, c.detalle as nombrelibro,
        IF(u.apellido is null,CONCAT(u.nombre),CONCAT(u.nombre,' ',u.apellido)) as docente,
        i.nombre AS institucion
       FROM codigos c
       LEFT JOIN usuario u ON  c.profesor_id = u.idusuario
       LEFT JOIN institucion i ON  u.institucion_idinstitucion  = i.idinstitucion
       LEFT JOIN  verificacion_codigo v ON c.id_verificacion = v.verificacion_id
       WHERE v.contrato = '$contrato'
       AND c.estado  = '1'
       ");
       if(empty($buscarVerificacionesContrato)){
        return ["status" => "0","message" => "No hay codigos para este contrato"];
       }else{
        return $buscarVerificacionesContrato;
       }

    }
}

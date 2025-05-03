<?php

namespace App\Http\Controllers;

use App\Models\HistoricoCodigos;
use Illuminate\Http\Request;
use App\Models\Verificacion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class VerificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $verificar = Verificacion::orderBy('verificacion_id','desc')->get();

        return $verificar;
    }
    //para ver el administrador
    public function verAdmin(Request $request){
        $administrador = $request->id_verificador;
        $admin = DB::table('usuario')
        ->select('usuario.nombre','usuario.apellido')
        ->where('grupo_idgrupo',"1")
        ->where('idusuario',$administrador)
        ->get();

        return $admin;
    }
    //para traer los docentes 

    public function traerDocentes(){
        $docentes = DB::table('usuario')
        ->where('grupo_idgrupo',"2")
        ->where('estado',"1")
        ->get();

        return $docentes;
    }
    //api:post>/codigos/importar
    //importar codigos en la verificacion
    public function codigosImportar(Request $request){
        set_time_limit(6000000);
        ini_set('max_execution_time', 6000000);
        $codigos = json_decode($request->data_codigos);  
        $datos=[];
        $codigosNoCambiados=[];
        $codigosYaVerificados =[];
        $codigoNoExiste = [];
        $porcentaje = 0;
        $contador = 0;
        foreach($codigos as $key => $item){
        //validar si el codigo existe
            $validar = DB::SELECT("SELECT c._id as codigo,contrato,c.estado,
            (CASE 
                WHEN(c.estado = '0') THEN 'Codigo libre'
                WHEN(c.estado = '1') THEN 'Codigo utilizado'
                WHEN(c.estado = '2') THEN 'Codigo devuelto'
                ELSE 'Codigo sin estado'
                end
            ) as estadoCodigo
            FROM codigos c
            WHERE c._id = '$item->codigo'
            ");
            //valida que el codigo existe
            if(count($validar)>0){
                //validar si el codigo se encuentra en un contrato
                $ifhaveContracto = $validar[0]->contrato;
                //validar que sea un codigo libre
                $ifCodigoLibre   = $validar[0]->estado;
                if($ifCodigoLibre == '0' && ($ifhaveContracto == null || $ifhaveContracto == '0')){
                    $date = Carbon::now();   
                    $codigo = DB::table('codigos')
                       ->where('_id', $item->codigo)
                       ->where('estado','=','0')
                       ->update(
                           [
                           'profesor_id'    =>  $request->docente_id, 
                           'contrato'       =>  $request->contrato,
                           'estado'         =>  1,
                           'id_verificador' =>  $request->usuario_editor,
                           'updated_at'     =>  $date->toDateTimeString(),
                           'id_verificacion' => $request->id_verificacion,
                           ]
                        );
                    if($codigo){
                        $porcentaje++;
                        //ingresar en el historico
                        $historico = new HistoricoCodigos();
                        $historico->id_usuario     = $request->usuario_editor;
                        $historico->codigo_libro   = $item->codigo;
                        $historico->usuario_editor = $request->usuario_editor;
                        $historico->idInstitucion  = $request->institucion_id;
                        $historico->id_periodo     = $request->id_periodo;
                        $historico->contrato       = $request->contrato;
                        $historico->observacion    = 'Se ingresa codigo en el contrato';
                        $historico->save();
                    }else{
                        $codigosNoCambiados[$key] =[
                            "codigo" => $item->codigo
                        ];
                    }  
                }else{
                    $codigosYaVerificados[$contador] = [
                        "codigo" => $item->codigo,
                        "contrato" => $validar[0]->contrato,
                        "estadoCodigo" => $validar[0]->estadoCodigo,
                    ];
                    $contador++;
                }
            }else{
                $codigoNoExiste[$key] =[
                    "codigo" => $item->codigo
                ];
            }
        }
        return [
            "cambiados" => $porcentaje,
            "codigosNoCambiados" => $codigosNoCambiados,
            "codigosYaVerificados" => $codigosYaVerificados,
            "codigoNoExiste" => $codigoNoExiste
        ];
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->verificacion_id){
            try{
                DB::beginTransaction();
                $date = Carbon::now();
                $verificar = Verificacion::find($request->verificacion_id);
                $verificar->fecha_finalizacion = $date->toDateTimeString();
                $verificar->estado = $request->estado;
                $verificar->save();
                  //el docente pasaria a poder tener otra verificacion
                //   $res2 = DB::table('usuario')
                //   ->where('persona_id',  $request->docente_id)
                //   ->update(['verificacion_estado' => 0]);
                //el docente pasaria a poder tener otra verificacion
                $res = DB::table('contratos')
                ->where('cod_contrato', $request->contrato)
                ->update(['verificacion_estado' => 0]);
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                return "no se pudo guardar";
            }
            return $verificar;
         
    
        }else{
            //verificar que existe el id_profesor no tenga una verificacion abierta
            $traerdocente = $request->docente_id;
            $traercontrato = $request->contrato;
            $verificardocenteverificacion = DB::table('contratos')
            ->select('contratos.verificacion_estado')
            ->where('contratos.cod_contrato','=',$traercontrato)
            ->where('contratos.verificacion_estado','=',1)
            ->get();
            if(count($verificardocenteverificacion) <= 0){
                //PARA OBTENER EL PERIODO
                    $buscarPeriodo = $this->traerPeriodo($request->institucion_id);
                    if($buscarPeriodo["status"] == "1"){
                        $periodo = $buscarPeriodo["periodo"][0]->periodo;   
                    }  
                    if($buscarPeriodo["status"] == "0"){
                        return "no existe el periodo para la institucion";
                    } 
                    $contador = 0;
                    //validar que sea la primera verificacion
                    $ifVerificacion = DB::SELECT("SELECT * FROM verificacion_codigo v
                    WHERE v.contrato = '$request->contrato'
                    ");
                    if(count($ifVerificacion)>0){
                        //validar en que numero de verificacion se encuentra
                        $getCount = DB::SELECT("SELECT max(num_verificacion) as contador FROM verificacion_codigo v
                        WHERE v.contrato = '$request->contrato'
                        ");
                        $contador = $getCount[0]->contador;
                    }
                    $date = Carbon::now();
                    $verificar                      =  new Verificacion;
                    $verificar->descripcion         = $request->descripcion;
                    $verificar->institucion_id      = $request->institucion_id;
                    $verificar->docente_id          = $request->docente_id;
                    $verificar->fecha_inicio        = $date->toDateTimeString();   
                    $verificar->codigos             = 0;
                    $verificar->contrato            = $request->contrato;
                    $verificar->id_periodo          = $periodo;
                    $verificar->num_verificacion    = $contador+1;
                    $verificar->save();  
                    //el docente pasaria a tener un verificacion abierta
                    $res = DB::table('contratos')
                    ->where('cod_contrato', $request->contrato)
                    ->update(['verificacion_estado' => 1]);
                    return $verificar;

                    
             }else{

                return "Una verificacion se encuentra abierta para este docente";
             }
           
        }
       
    }

    public function traerPeriodo($institucion){
        $periodoInstitucion = DB::SELECT("SELECT idperiodoescolar AS periodo , periodoescolar AS descripcion FROM periodoescolar WHERE idperiodoescolar = ( 
            SELECT  pir.periodoescolar_idperiodoescolar as id_periodo
            from institucion i,  periodoescolar_has_institucion pir         
            WHERE i.idInstitucion = pir.institucion_idInstitucion
            AND pir.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi
            WHERE phi.institucion_idInstitucion = i.idInstitucion
            AND i.idInstitucion = '$institucion'))
        ");
        if(count($periodoInstitucion)>0){
            return ["status" => "1", "message"=>"correcto","periodo" => $periodoInstitucion];
        }else{
            return ["status" => "0", "message"=>"no hay periodo"];
        }
    }

    //api:post//>/api/changeEstadoCode
    public function changeEstadoCode(Request $request){
        //validar que no envie numeros extraños
        $estado = $request->estado;
        if($estado !=2){
            return ["status" => "0","message" => "Estado no existe"]; 
        }
        //validar que el codigo existe
        $validar = DB::SELECT("SELECT 
        (CASE 
            WHEN(c.estado = '0') THEN 'Codigo libre'
            WHEN(c.estado = '1') THEN 'Codigo utilizado'
            WHEN(c.estado = '2') THEN 'Codigo devuelto'
            ELSE 'Codigo sin estado'
            end
        ) as estadoCodigo,
         c.* FROM codigos c WHERE  c._id = '$request->codigo'");
        if(count($validar)>0){
            //validar no este verificado
            $ifVerificado = $validar[0]->estado;
            if($ifVerificado != '1'){
                $cambiar = DB::table('codigos')
                ->where('_id',$request->codigo)   
                ->update([
                   'estado' => $estado
                ]);    
               $codigo = $this->validar($request->codigo);
               $observacion  = $codigo[0]->estadoCodigo; 
                //ingresar en el historico
                $historico = new HistoricoCodigos();
                $historico->id_usuario     = 0;
                $historico->codigo_libro   = $request->codigo;
                $historico->usuario_editor = 0;
                $historico->idInstitucion  = 0;
                $historico->id_periodo     = "";
                $historico->contrato       = "";
                $historico->observacion    = $observacion;
                $historico->save();
               return $codigo;
            }else{
                return ["status" => "0","message" => "El codigo ya esta verificado"];
            }
        }else{
            return ["status" => "0","message" => "No existe el codigo"];
        }
    }
    //api:get//>/api/changeEstadoCode/{code}
    public function estadoCodigo($codigo){
        //validar que el codigo existe
        $validar = $this->validar($codigo);
        if(count($validar)>0){  
            return $validar;
        }else{
            return ["status" => "0","message" => "No existe el codigo"];
        }
    }

    public function validar($codigo){
        $validar = DB::SELECT("SELECT 
        (CASE 
            WHEN(c.estado = '0') THEN 'Codigo libre'
            WHEN(c.estado = '1') THEN 'Codigo utilizado'
            WHEN(c.estado = '2') THEN 'Codigo devuelto'
            ELSE 'Codigo sin estado'
            end
        ) as estadoCodigo,
         c.* FROM codigos c WHERE  c._id = '$codigo'");
         return $validar;
    }
}

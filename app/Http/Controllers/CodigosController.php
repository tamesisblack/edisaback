<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Codigo;
use Illuminate\Support\Facades\DB;
// use App\Imports\CodigoImport;
// use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class CodigosController extends Controller
{
    //para listar la tabla codigo para importar la data excel
    public function listar(){
      $data = Codigo::paginate(20);
        return view('test.codigoexcel',compact('data'));
    }
    public function index()
    {
           $codigos = DB::select("SELECT * FROM codigos ORDER BY codigo_id DESC LIMIT 1000");
         return $codigos;
    }
     //para ver codigos
     public function vercodigos(Request $request){
        $id_verificacion = $request->id_verificacion;
        $verCodigos = DB::select("SELECT  c._id, c.id_verificador, c.updated_at as fecha, l.nombre as libro,
          IF(u.apellido is null,CONCAT(u.cedula,' ',u.nombre),CONCAT(u.cedula,' ',u.nombre,' ',u.apellido)) as docente
            from codigos c
            LEFT JOIN usuario u ON  c.profesor_id = u.idusuario  
            LEFT JOIN libro l ON c.idlibro = l.c_primaria     
            where c.id_verificacion = '$id_verificacion'
            and c.estado  = '1'
         ");
     return $verCodigos;
    }

    //api para milton para ver codigos de un contrato
    public function verificacionContrato($contrato){
        $buscarVerificacionesContrato = DB::select("SELECT  v.*
        FROM  verificacion_codigo v
        WHERE contrato = '$contrato'
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

     //api para milton para ver codigos de un contrato
     public function informacionContrato(Request $request){
        $verCodigos = DB::select("SELECT  c._id as codigo,  u.cedula,
            IF(u.apellido is null,CONCAT(u.nombre),CONCAT(u.nombre,' ',u.apellido)) as docente
            from codigos c
            LEFT JOIN usuario u ON  c.profesor_id = u.idusuario  
            LEFT JOIN libro l ON c.idlibro = l.c_primaria     
            where c.id_verificacion = '$request->verificacion_id'
            and c.estado  = '1'
        ");
        return $verCodigos;
    }
    public function store(Request $request)
    {
      
       $id = $request->_id;
       $estado = $request->estado;

       $buscarCodigo = DB::select('SELECT  * FROM codigos  where _id = ?', [$id]);
        
       if(!empty($buscarCodigo)){
           $res = DB::table('codigos')
            ->where('_id', $id)
            ->update(['estado' => $estado]);

        if($res){
            return ["status" => "1", "message" => "Se actualizo correctamente"]; 
        }else{
            return ["status" => "0", "message" =>  "No se actualizo porque el estado es el mismo"]; 
        }
        
       }else{
           return ["status" => "0", "message" =>  "No se encontro el codigo"];
       }
     
    
    

    }
    public function savecodigo(Request $request){
        $traercontrato = $request->contrato;
         //verificar que existe el codigo en la tabla codigos
         $traercodigo = $request->codigo;
         $verificarcodigo = DB::table('codigos')
         ->select('codigos.*')
         ->where('codigos._id','=',$traercodigo)
         ->get();
        //verificar que el codigo ya exista en la tabla codigos
        $traerdocente = $request->id_docente;
             if(count($verificarcodigo) <= 0){
                return ["status"=>"0","message"=>"no existe ese codigo"];
             }
             //validar que el codigo no este devuelto
             $ifEstado = $verificarcodigo[0]->estado;
             if($ifEstado == 2){
               return ["status"=>"0","message"=>"El codigo ya sido devuelto"];
             }
             //si existe el codigo
             else{
                //validar que el codigo ya haya sido usado en otro contrato
                $validarStatus = DB::SELECT("SELECT * FROM `codigos` 
                WHERE `_id` = '$traercodigo'
                AND (contrato IS NULL OR contrato = '0')
                ");
                if(count($validarStatus) <=0){
                    $contratoAsignado = $verificarcodigo[0]->contrato;
                    return ["status"=>"0","message"=>"El codigo ya se encuentra asignado al contrato: $contratoAsignado"];
                }else{
                    //validar que el codigo no este verificado
                    if($ifEstado == 1){
                    return ["status"=>"0","message"=>"El codigo ya esta verificado"];
                    }
                    //esto es cuando el docente es ingresado por un administrador   
                    if($request->id_verificador){
                        $date = Carbon::now();   
                        $res = DB::table('codigos')
                        ->where('_id', $traercodigo)
                        ->update(
                            [
                            'profesor_id' => $traerdocente, 
                            'contrato' => $traercontrato,
                            'estado'=>1,
                            'id_verificador' => $request->id_verificador,
                            'updated_at'=>$date->toDateTimeString(),
                            'id_verificacion' => $request->verificacionid,
                            
                            ]
                        );
                    }
                    //esto es cuando el docente  es ingresado por el mismo
                    else{
                        $date = Carbon::now();     
                        $res = DB::table('codigos')
                        ->where('_id', $traercodigo)
                        ->update(
                            [
                            'profesor_id' => $traerdocente,
                             'contrato' => $traercontrato,
                             'estado'=>1,
                             'id_verificacion' => $request->verificacionid,
                             'updated_at'=>$date->toDateTimeString(),
                            ]);
                    }
                    return ["status"=>"1", "message"=>"Se ingreso correctamente"];      
                }       
             }
    }
    public function show($codigo)
    {   
        $traercodigos = DB::table('codigos')
        ->select('codigos.*')
     
        ->where('codigos.idcreate','=',$codigo)
        ->get();
        return $traercodigos;
    }

    public function listarcodigo($codigo){
       
        $traercodigo = DB::table('codigos')
        ->select('codigos.*')
     
        ->where('codigos._id','=',$codigo)
        ->get();
        return $traercodigo;
    }
    public function findCode($id)
    {
        $traercodigos = DB::table('codigos as cd')
        ->select('cd.*', 'u.nombre as prof_nombre', 'u.apellido as prof_apellido', 'u.cedula', 'u.email',
        'usu.nombre as verif_nombre', 'usu.apellido as verif_apellido')     
        ->leftjoin('usuario as u','cd.profesor_id','=','u.idusuario')
        ->leftjoin('usuario as usu','cd.id_verificador','=','usu.idusuario')
        ->where('cd._id','like','%'.$id.'%')
        ->get();
        return $traercodigos;
    }

}

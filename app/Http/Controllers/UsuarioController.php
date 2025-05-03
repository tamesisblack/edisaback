<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use DB;
class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $usuario = DB::SELECT("SELECT *,  CONCAT(usuario.nombre,' ',usuario.apellido) as docente FROM usuario WHERE estado = '1' AND grupo_idgrupo='2' ORDER BY fecha_create DESC");
        $usuario = DB::table('usuario as u')
        ->leftjoin('institucion as i','u.institucion_idinstitucion','=','i.idinstitucion')
        ->leftjoin('region as r','i.region','=','r.idregion')
        ->where('u.grupo_idgrupo','=','2')
        ->select('u.idusuario', 'u.cedula','u.email', 'u.nombre','u.apellido','i.nombre as institucion','r.nombreregion','u.estado', 'u.fecha_create','u.institucion_idinstitucion')
        ->get();
        return $usuario;
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

    public function password(){
        return str_random(8);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty($request->idusuario)){
            
            $usuario = Usuario::findOrFail($request->idusuario);
            $usuario->cedula  = $request->cedula;
            $usuario->nombre  = $request->nombre;
            $usuario->apellido  = $request->apellido;
            $usuario->estado  = $request->estado;
            if($request->email=="null"){
                $usuario->email  = "";
            }else{
                $usuario->email  = $request->email;
            }
           
            $usuario->telefono  = $request->telefono;
            $usuario->genero  = $request->genero;
            $usuario->grupo_idgrupo  = $request->grupo_idgrupo;
            if($request->password =="" || $request->password =="null"){

            }else{
                $usuario->password = bcrypt($request->password);
            }
            
            $usuario->institucion_idinstitucion  = $request->institucion_idinstitucion;

            
           
        }else{
            $usuario = new Usuario;
            $usuario->cedula  = $request->cedula;
            $usuario->nombre  = $request->nombre;
            $usuario->apellido  = $request->apellido;
           
            $usuario->email  = $request->email;
        
           
            $usuario->telefono  = $request->telefono;
            $usuario->genero  = $request->genero;
            $usuario->grupo_idgrupo  = $request->grupo_idgrupo;
          
            $usuario->password = bcrypt($request->password);
        
            
            $usuario->institucion_idinstitucion  = $request->institucion_idinstitucion;
           
            
        }

        $usuario->save();
        if($usuario){
            return ["status" =>"1", "message"=> "Se guardo correctamente"];
        }else{
            return ["status" =>"0", "message"=> "No se pudo realizar cambios"];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show(Usuario $usuario)
    {
        return $usuario;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usuario $usuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usuario $usuario)
    {
        $usuario = Usuario::find($usuario->idusuario)->update(['estado' => '0']);
        return $usuario;
    }
    public function admins()
    {
        $usuario = DB::table('usuario as u')
        ->leftjoin('institucion as i','u.institucion_idinstitucion','=','i.idinstitucion')
        ->leftjoin('region as r','i.region','=','r.idregion')
        ->where('u.grupo_idgrupo','=','1')
        ->select('u.idusuario', 'u.cedula','u.email', 'u.nombre','u.apellido','i.nombre as institucion','r.nombreregion','u.estado', 'u.fecha_create')
        ->get();
        return $usuario;

        // $dato = DB::table('usuario')
        // ->where('grupo_idgrupo','=','1')
        // ->get();
        // return $dato;
    }
}

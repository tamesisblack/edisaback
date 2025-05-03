<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libros;
use App\Models\Codigo;
use Illuminate\Support\Facades\DB;

// use App\Imports\LibrosImport;
// use Maatwebsite\Excel\Facades\Excel;

class LibrosController extends Controller
{

    //para listar la tabla libros para importar la data excel
    public function listar(){
        $data = Libros::orderBy('libro_id','desc')->get();
        return view('test.librosexcel',compact('data'));
    }
    public function index(Request $request){
        $libros = DB::SELECT("SELECT ls.* FROM libros ls
            WHERE ls.idlibro = '$request->libro_id'
            ORDER BY ls.libro_id desc
        ");
        // $libros = Libros::orderBy('libro_id','desc')->get();
        return $libros;
    }

    public function makeid(){
        $characters = '123456789abcdefghjkmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
   
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            for ($i = 0; $i < 16; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
      
        
         }   
    }
    public function create()
    {
        //
    }

    
     //api::post>>/generarLibros
     public function generarLibros(Request $request){
        set_time_limit(6000000);
        ini_set('max_execution_time', 6000000);
        if( $request->libro_id ){
           $libros = Libros::find($request->libro_id);
           $libros->_id = $request->_id;
           $libros->idlibro = $request->idlibro;
           $libros->nombre = $request->nombre;
           $libros->cantidad = $request->cantidad;
           $libros->prefijo = $request->prefijo;
       }
       else{
           try{
               DB::beginTransaction();
               //variable para generar datos aleatorios para almacenar en id
               $datosrandom = substr(md5(time()), 0, 24);
               $verificarid = DB::table('libros')
               ->select('libros._id','libros.nombre')
               ->where('libros._id','=',$datosrandom)
               ->get();
               if(count($verificarid) <= 0){
                $libros = new Libros();
                $libros->_id = $datosrandom;
                $libros->idlibro = $request->idlibro;
                $libros->nombre = $request->nombre;
                $libros->cantidad = $request->cantidad;
                $libros->prefijo = $request->prefijo;          
               }else{
               return "ya existe el id genero de nuevo";
               }
               $libros->save();
               $idlibro=$request->idlibro;
               $cantidad=$request->cantidad;
               $nombre = $request->nombre;
               $prefijo = $request->prefijo;
             //para inresar en la tabla codigos
               $cont =0;
               while ($cont < $cantidad) {
                    $ingresar = false;
                    while ($ingresar == false) {
                        $traercodigo = $this->makeid();
                        //validar si existe ya existe el codigo
                        $validate = DB::SELECT("SELECT * FROM codigos WHERE _id = '$traercodigo'");
                        if(empty($validate)){
                            //si el codigo no existe lo creo
                            $codigos=new Codigo;
                            $codigos->_id=$prefijo."".$traercodigo;
                            $codigos->idlibro=$idlibro;
                            $codigos->detalle=$nombre;
                            $codigos->url=$nombre;
                            $codigos->idcreate=$libros->_id;
                            $codigos->nombre=$nombre;
                            $codigos->save(); 
                            $ingresar = true;
                            $cont=$cont+1;    
                        }
                        //si ya existe le mando a generar de nuevo
                        else{
                            $ingresar = false;
                        }
                    }
                //    $traercodigo = $this->makeid();
                //        $verificarcodigo = DB::table('codigos')
                //        ->select('codigos._id','codigos.nombre')
                //        ->where('codigos._id','=',$traercodigo)
                //        ->get();
                //        if(count($verificarcodigo) <= 0){
                //            $codigos=new Codigo;
                //            $codigos->_id=$prefijo."".$traercodigo;
                //            $codigos->idlibro=$idlibro;
                //            $codigos->detalle=$nombre;
                //            $codigos->url=$nombre;
                //            $codigos->idcreate=$libros->_id;
                //            $codigos->nombre=$nombre;
                //            $codigos->save(); 
                //            $cont=$cont+1;     
                //             }else{
                //            return "ya existe el id en la tabla codigos genero de nuevo";
                //            }
               }
               DB::commit();
           }catch(\Exception $e){
               DB::rollback();
           }
       }
       return response()->json($libros);
   }

    public function store(Request $request)
    {
        set_time_limit(6000000);
        ini_set('max_execution_time', 6000000);
        if( $request->libro_id ){
            $libros = Libros::find($request->libro_id);
            $libros->_id = $request->_id;
            $libros->idlibro = $request->idlibro;
            $libros->nombre = $request->nombre;
            $libros->cantidad = $request->cantidad;
            $libros->prefijo = $request->prefijo;
        }
        else{
            try{
                DB::beginTransaction();
                //variable para generar datos aleatorios para almacenar en id
                $datosrandom = substr(md5(time()), 0, 24);
                $verificarid = DB::table('libros')
                ->select('libros._id','libros.nombre')
                ->where('libros._id','=',$datosrandom)
                ->get();
                if(count($verificarid) <= 0){
                    $libros = new Libros();
                    $libros->_id = $datosrandom;
                    $libros->idlibro = $request->idlibro;
                    $libros->nombre = $request->nombre;
                    $libros->cantidad = $request->cantidad;
                    $libros->prefijo = $request->prefijo;          
                }else{
                return "ya existe el id genero de nuevo";
                }
                $libros->save();
                $idlibro=$request->idlibro;
                $cantidad=$request->cantidad;
                $nombre = $request->nombre;
                $prefijo = $request->prefijo;
              //para inresar en la tabla codigos
                $cont =0;
                while ($cont < $cantidad) {
                    $ingresar = false;
                    while ($ingresar == false) {
                        $traercodigo = $this->makeid();
                        //validar si existe ya existe el codigo
                        $validate = DB::SELECT("SELECT * FROM codigos WHERE _id = '$traercodigo'");
                        if(empty($validate)){
                            //si el codigo no existe lo creo
                            $codigos=new Codigo;
                            $codigos->_id=$prefijo."".$traercodigo;
                            $codigos->idlibro=$idlibro;
                            $codigos->detalle=$nombre;
                            $codigos->url=$nombre;
                            $codigos->idcreate=$libros->_id;
                            $codigos->nombre=$nombre;
                            $codigos->save();
                            $ingresar = true;
                            $cont=$cont+1;    
                        }
                        //si ya existe le mando a generar de nuevo
                        else{
                            $ingresar = false;
                        }
                    }
                    // $traercodigo = $this->makeid();
                        // $verificarcodigo = DB::table('codigos')
                        // ->select('codigos._id','codigos.nombre')
                        // ->where('codigos._id','=',$traercodigo)
                        // ->get();
                        // if(count($verificarcodigo) <= 0){
                        //     $codigos=new Codigo;
                        //     $codigos->_id=$prefijo."".$traercodigo;
                        //     $codigos->idlibro=$idlibro;
                        //     $codigos->detalle=$nombre;
                        //     $codigos->url=$nombre;
                        //     $codigos->idcreate=$libros->_id;
                        //     $codigos->nombre=$nombre;
                        //     $codigos->save(); 
                        //     $cont=$cont+1;                                     
                        // }else{
                        //     return "ya existe el id en la tabla codigos genero de nuevo";
                        // }
                }
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
            }
        }
        return $libros;
    }

    public function import(Request $request){
        $datos =
        [
         'select_file'  => 'required|mimes:xls,xlsx'
         ];
        $this->validate($request, $datos);

        $path = $request->file('select_file'); 
        Excel::import(new LibrosImport,$path);
        return back()->withStatus('Se agrego los datos correctamente');
    }
    public function show($id)
    {
        //
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
}

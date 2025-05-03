<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Planificacion;
use Illuminate\Http\Request;
use DB;

class PlanificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plan = Planificacion::get();
        return $plan;
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

    public function store(Request $request)
    { 
          $max_size = (int)ini_get('upload_max_filesize')*10240;
        
         $files = $request->file('archivo');
        if($request->nombre){
            
           $planificacion =  Planificacion::create([
                "nombre" => $request->nombre,
                "detalle" => $request->detalle,
                "asignatura_idasignatura" => $request->idlibro,
                "url" => $request->archivo,
            ]);

            if($planificacion){
                return "Se guardo correctamente";
            }else{
                return "No se pudo guardar";
            }

         
        }else{
            $traercodigo = $this->makeid();
            $image = $request->file('file');
            $imageName = uniqid().'.'.$image->getClientOriginalExtension();
            $cadena =str_replace(' ', '', $imageName);
            
    
            // echo $imageName;
            $path = "/planificacion/";
              $image->move(public_path().$path,$cadena);
              return $cadena;
    
        }
    
        
       
    }
    //api:post>>/eliminarPlanificacion

    public function eliminarPlanificacion(Request $request){
        $archivo = Planificacion::findOrFail($request->id_archivo);
        $filename = $archivo->url;
    
       
    
          if(file_exists('planificacion/'.$filename) ){
          unlink('planificacion/'.$filename);
            $archivo->delete();
    
          }
        if($archivo){
            return "Se elimino correctamente";
        }else{
            return "No se pudo eliminar";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Planificacion  $planificacion
     * @return \Illuminate\Http\Response
     */
    public function show(Planificacion $planificacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Planificacion  $planificacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Planificacion $planificacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Planificacion  $planificacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Planificacion $planificacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Planificacion  $planificacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Planificacion $planificacion)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use \Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Roles;
use App\Models\Bancos;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carbon = new \Carbon\Carbon();
// return $carbon->format('Y');

        $users = DB::table('users as u')
        -> select('u.id','u.cedula','u.nombre','u.apellido','u.telefono','u.cuenta','u.tipocuenta','u.fnacimiento','u.email','u.estado','u.rol_id','u.banco_id','r.nombre as rol', 'bancos.nombre as banco')
        ->leftjoin('roles as r','u.rol_id','=','r.id')
        ->leftjoin('bancos','u.banco_id','=','bancos.id')
        ->get();

        // $multas = DB::SELECT("SELECT * FROM `multas` WHERE `user_id` = ?",[$value->id]);
        foreach ($users as $key => $value) {
            $multasP = DB::table('multas')
            ->where('user_id','=',[$value->id])
            ->where('estado','=',0)
            ->where('year','=',$carbon->format('Y'))
            ->count();

            $multasC = DB::table('multas')
            ->where('user_id','=',[$value->id])
            ->where('estado','=',1)
            ->where('year','=',$carbon->format('Y'))
            ->count();
            
            $aportes = DB::table('aportes')
            ->where('user_id','=',[$value->id])
            ->where('year','=',$carbon->format('Y'))
            ->count();

            
            $data['items'][$key] = [
                'usuario' => $value,
                'multaspendientes' => $multasP,
                'multaspagadas' => $multasC,
                'aportes' => $aportes,
            ];            
  
        }
        return $data;
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
        if ($request->id > 0) {
            $dato = User::find($request->id);
        }else{
            $dato = new User();
        }
        $dato->cedula = $request->cedula;
        $dato->nombre = $request->nombre;
        $dato->apellido = $request->apellido;
        $dato->telefono = $request->telefono;
        $dato->cuenta = $request->cuenta;
        $dato->tipocuenta = $request->tipocuenta;
        $dato->fnacimiento = $request->fnacimiento;
        $dato->email = $request->email;
        $dato->password = Hash::make($request->cedula); 
        $dato->estado = $request->estado;
        $dato->rol_id = $request->rol_id;
        $dato->banco_id = $request->banco_id;
        $dato->save();
        return $dato;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $dato = User::find($id);
        $dato->delete();
        return $dato;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'telefono' => 'required|string|min:10'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'telefono' => $validatedData['telefono'],
            'id_perfil' => 2,
            'last_name' => $request->last_name,
            'password' => Hash::make($validatedData['password'])
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }


   public function login(Request $request){
        $input = $request->all();

        $this->validate($request,[
            // "email" => 'string',
           "email" => 'required|string',
            'password' => 'required |string'
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL)? 'email':'cedula';
        if(auth()->attempt(array($fieldType => $input['email'],'password' => $input['password'])))
        {

                // $user[0] = User::where('email', $request['email'])->orWhere('cedula', $request['email'])->firstOrFail();
            $user[] = User::where('email', $request['email'])->orWhere('cedula', $request['email'])->firstOrFail();
            $token = $user[0]->createToken('auth_token')->plainTextToken;
            $institucion = $user[0]->institucion_idinstitucion;

             $filtroInstitucion = DB::SELECT("SELECT * FROM  institucion WHERE idinstitucion  ='$institucion'");
             $EncontrarRegion  = $filtroInstitucion[0]->region;
             $EncontrarGrupo  = $user[0]->grupo_idgrupo;
             if($EncontrarGrupo > 1){
                if ($EncontrarRegion ==  2) {
                    //validar que el periodo aun no termine la fecha limite
                    $todate  = date('Y-m-d');
                    $filtrarPeriodo = DB::SELECT("SELECT * FROM  periodoescolar p
                    WHERE p.region_idregion = '2'
                    AND p.estado = '1'
                    ORDER BY p.idperiodoescolar DESC
                    ");
                    if(count($filtrarPeriodo)>0){
                        $f_final = $filtrarPeriodo[0]->fecha_final;
                        if($todate > $f_final){
                            return ["status" => "2","message"=>"El periodo ya caduco comuniquese con sistemas"];
                        }

                    }else{
                        return ["status" => "2","message"=>"No existe periodo para la region costa"];
                    }
                } else {
                    //validar que el periodo aun no termine la fecha limite
                    $todate  = date('Y-m-d');
                    $filtrarPeriodo = DB::SELECT("SELECT * FROM  periodoescolar p
                    WHERE p.region_idregion = '1'
                    AND p.estado = '1'
                    ORDER BY p.idperiodoescolar DESC
                    ");
                    if(count($filtrarPeriodo)>0){
                        $f_final = $filtrarPeriodo[0]->fecha_final;
                        if($todate >= $f_final){
                            return ["status" => "2","message"=>"El periodo ya caduco comuniquese con sistemas"];
                        }
                    }else{
                        return ["status" => "2","message"=>"No existe periodo para la region sierra"];
                    }
                }
             }


            // return response()->json([
            //     'access_token' => $token,
            //     'token_type' => 'Bearer',
            //     'usuario' =>$user,
            // ]);

                return response()->json([
             'datos' => $user,
             'status' => 'ok',
             'token' => $token,
         ]);
        }else{
            return response()->json([
                    'message' => 'Credenciales incorrectas',
                    "status" => "0"
                ]);
            }

        // if(!Auth::attempt($request->only('email', 'password'))){
        //     return response()->json([
        //         'message' => 'Invalid login details',
        //     ]);
        // }

        // $user = User::where('email', $request['email'])->firstOrFail();

        // $token = $user->createToken('auth_token')->plainTextToken;

        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'Bearer'
        // ]);
    }
    public function username(){
        return 'cedula';
    }


    public function infouser(Request $request){
        return $request->user();
    }

}

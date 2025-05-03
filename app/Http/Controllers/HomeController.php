<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use Illuminate\Provider\Image;

use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getEventos(){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_evento?tabla=EVENTOS&tipo=T&usr=1&ip=10.10.10.1&id=&datos=');
        $data = $response->json();
        return $data;
    }

    
    public function getClientes(){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_cliente?tabla=CLIENTES&tipo=T&usr=1&ip=10.10.10.1&id=&datos=');
        $data = $response->json();
        return $data;
    }

    public function getEstados(){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_sel_tablasgen?tabla=');
        $data = $response->json();
        return $data;
    }
    
    
    public function guardarEvento(Request $request){
        $ruta = public_path('img/img_eventos');

        $fileName = 'fondo_tablero_default.jpg';
        if( $request->file('img_tablero') !== null && $request->file('img_tablero') !== '' ){
            $file = $request->file('img_tablero');
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($ruta, $fileName);
        }
        
        $fileNameC = 'fondo_cartilla_default.jpg';
        if( $request->file('img_casillero') !== null && $request->file('img_casillero') !== '' ){
            $fileC = $request->file('img_casillero');
            $fileNameC = uniqid().$fileC->getClientOriginalName();
            $fileC->move($ruta, $fileNameC);
        }

        $fileNameB = 'banner_default.jpg';
        if( $request->file('img_banner') !== null && $request->file('img_banner') !== '' ){
            $fileB = $request->file('img_banner');
            $fileNameB = uniqid().$fileB->getClientOriginalName();
            $fileB->move($ruta, $fileNameB);
        }

        $nombre_img_tablero = 'green';//cambiar
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_evento?tabla=EVENTOS&tipo=A&usr=1&ip=10.10.10.1&id=&datos={"Cliente": "'.$request->cliente.'","Nombreevento": "'.$request->nombre_evento.'","Fecha": "'.$request->date.'","Hora": "'.$request->time.'","Estado": "'.$request->estado.'","numerosbingo": "'.$request->cant_num_bingo.'","letrasbingo": "'.$request->palabra.'","img_tablero": "'.$fileName.'", "img_casillero": "'.$fileNameC.'", "img_banner": "'.$fileNameB.'", "colorfondo_tablero": "'.$nombre_img_tablero.'"}');

        $data = $response->json();
        return $data;

    }

    
    public function editarEvento(Request $request){

        $ruta = public_path('img/img_eventos');
        
        $fileName = $request->img_tablero_old;
        if( $request->file('img_tablero') !== null && $request->file('img_tablero') !== '' ){
            $file = $request->file('img_tablero');
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($ruta, $fileName);
        }

        $fileNameC = $request->img_casillero_old;
        if( $request->file('img_casillero') !== null && $request->file('img_casillero') !== '' ){
            $fileC = $request->file('img_casillero');
            $fileNameC = uniqid().$fileC->getClientOriginalName();
            $fileC->move($ruta, $fileNameC);
        }

        $fileNameB = $request->img_banner_old;
        if( $request->file('img_banner') !== null && $request->file('img_banner') !== '' ){
            $fileB = $request->file('img_banner');
            $fileNameB = uniqid().$fileB->getClientOriginalName();
            $fileB->move($ruta, $fileNameB);
        }

        $nombre_img_tablero = 'green';//cambiar

        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_evento?tabla=EVENTOS&tipo=M&usr=1&ip=10.10.10.1&id='.$request->id_evento.'&datos={"Cliente": "'.$request->cliente.'","Nombreevento": "'.$request->nombre_evento.'","Fecha": "'.$request->date.'","Hora": "'.$request->time.'","Estado": "'.$request->estado.'","numerosbingo": "'.$request->cant_num_bingo.'","letrasbingo": "'.$request->palabra.'","img_tablero": "'.$fileName.'", "img_casillero": "'.$fileNameC.'", "img_banner": "'.$fileNameB.'", "colorfondo_tablero": "'.$nombre_img_tablero.'"}');

        $data = $response->json();
        return $data;

    }

    public function getJugadores($id_evento){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_jugador?tabla=JUGADORES&tipo=V&usr=1&ip=10.10.10.1&id='.$id_evento.'&datos=');

        $data = $response->json();
        return $data;

    }


    
    public function guardarJugador(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_jugador?tabla=JUGADORES&tipo=A&usr=1&ip=10.10.10.1&id=&datos={"Nombres":"'.$request->nombres_jugador.'","Telefono":"'.$request->telefono_jugador.'","Correo":"'.$request->correo_jugador.'","Ciudad":"'.$request->ciudad_jugador.'","Numerocartillas":"'.$request->num_cartillas_jugador.'","evento":"'.$request->id_evento.'"}');

        $data = $response->json();
        return $data;
    }



    public function editarJugador(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_jugador?tabla=JUGADORES&tipo=M&usr=1&ip=10.10.10.1&id='.$request->id_jugador.'&datos={"Nombres":"'.$request->nombres_jugador.'","Telefono":"'.$request->telefono_jugador.'","Correo":"'.$request->correo_jugador.'","Ciudad":"'.$request->ciudad_jugador.'","Numerocartillas":"'.$request->num_cartillas_jugador.'","evento":"'.$request->id_evento.'"}');

        $data = $response->json();
        return $data;
    }


    public function getCombos(){
        
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_sel_tablasgen?tabla=');

        $data = $response->json();
        return $data;
    }



    public function guardarCliente(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_cliente?tabla=CLIENTES&tipo=A&usr=1&ip=10.10.10.1&id=&datos= {"nombre":"'.$request->nombre_cliente.'","identificacion":"'.$request->documento.'","estado":"'.$request->estado_cliente.'","imagen":"img.jpg"}');

        $data = $response->json();
        return $data;
    }

    public function editarCliente(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_cliente?tabla=CLIENTES&tipo=M&usr=1&ip=10.10.10.1&id='.$request->id_cliente.'&datos= {"nombre":"'.$request->nombre_cliente.'","identificacion":"'.$request->documento.'","estado":"'.$request->estado_cliente.'","imagen":"img.jpg"}');

        $data = $response->json();
        return $data;
    }


    public function enviarBola(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_tablero?tabla=TABLERO&tipo=A&usr=1&ip=10.10.10.1&id=&datos= {"evento": "'.$request->evento.'","numero": "'.$request->numero.'"}');

        $data = $response->json();
        return $data;
    }


    public function getNumerosJugados(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://www.alimec.app:8181/bingo/api_crea_tablero?tabla=TABLERO&tipo=C&usr=1&ip=10.10.10.1&id='.$request->id_evento.'&datos=');

        $data = $response->json();
        return $data;
    }

    public function getNumerosCartilla($id){
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://alimec.app:8181/bingo/consultacardstotal?clave='.$id);

        $data = $response->json();
        return $data;
    }
    

}

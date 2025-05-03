<template>

    <div class="row">
        
        <v-app>
            <v-snackbar transition="fab-transition" v-model="snackbar" rounded top :timeout="timeout" >
                {{ text_snackbar }}
                <template v-slot:action="{ attrs }">
                    <v-btn color="pink" dense small text v-bind="attrs" @click="snackbar = false" > x </v-btn>
                </template>
            </v-snackbar>
        </v-app>
    <div class="col-sm-3 p-5 text-center" style="color: white;" id="div_botones">
        <button class="btn_acciones btn-volver" @click="regresar()"> {{$t('back')}} </button>

        <v-btn fab dark dense small color="dark" v-if="audioActivo===false" @click="text_snackbar='Audio activado/ON'; snackbar=true; audioActivo=true"><v-icon dark> mdi-headphones-off </v-icon></v-btn>
        <v-btn fab dark dense small style="background-color: blue;" v-else @click="text_snackbar='Audio desactivado/OFF'; snackbar=true; audioActivo=false"><v-icon dark> mdi-headphones </v-icon></v-btn>

        <br>

        <button class="btn_acciones btn-bingo" id="btn_manual" @click="manualAcivo=true; inicioManual()"><b> Manual </b></button>

        <button class="btn_acciones btn-bingo" id="btn_auto" @click="manualAcivo=false; inicioAutomatico()"><b>{{$t('Automatic')}}</b></button>
        
        
                
        <br><br>

        <div style="color: black; font-size: 21px;" class="mb-6">
            {{$t('Last')}}: <span style="font-size: 28px;">{{numero_anterior}}</span><br>
            {{$t('Amount_played')}}: <span style="font-size: 28px;">{{numeros_marcados.filter(Boolean).length}}</span><br><br>
            <div v-if="data_tablero.ganador" style="color: white;">
                <b>{{$t('game_over1')}} {{data_tablero.ganador.split('(')[0] }} {{ (((data_tablero.ganador.split('('))[1]).split('*'))[2].replace(")", "") }} {{$t('game_over2')}}</b>
            </div>
            <div v-else>
                <b>{{$t('No_wins')}}</b>
            </div>
        </div>
        <br><br>
        <Language />
    </div>

    <div class="col-sm-5 text-center">
    <table style="width: 100%;" class="tabla_bingo">
    <tbody>
    
    <tr>
        <td colspan="2" style="vertical-align: top;">
            <div class="tubo" style="width: 95px; height: 500px;" id="tubo_vertical">
                <div class="stage">
                    <span class="bolas img-responsive" id="animate2" :style="'background-color: '+color_rand+';'"><span style="width: 100%; padding: 0px; margin: 0px;"><b>{{numero}}</b></span> <!-- / <span style="width: 100%; font-size: 15px; padding: 0px; margin: 0px;">{{numero_anterior}}</span> --></span>
                </div>
            </div>
            <div class="tubo" id="tubo_horizontal" style="height: 110px; width: 100%; min-width: 60px; border-bottom-left-radius: 100px; z-index: -1;"> <!-- tubo de salida de la bola --> </div>
        </td>
        <td colspan="10">
            <div id="div_tablero" :style="img_tablero">
                <div style="width: 60px; display: inline-block;" class="div_pos">
                    <b style="color: white;">{{letras_bingo[0]}}</b><br>
                    <div class="porta_bola_tablero" v-for="(item, index) in filas" :id="'bolaN'+((index+1))" :key="index" fab dark :style="'color: white;'">{{((index+1))}}</div>
                </div>

                <div style="width: 60px; display: inline-block;" class="div_pos">
                    <b style="color: white;">{{letras_bingo[1]}}</b><br>
                    <div class="porta_bola_tablero" v-for="(item, index) in filas" :id="'bolaN'+((index+1)+(filas))" :key="index" fab dark :style="'color: white;'">{{((index+1)+(filas))}}</div>
                </div>

                <div style="width: 60px; display: inline-block;" class="div_pos">
                    <b style="color: white;">{{letras_bingo[2]}}</b><br>
                    <div class="porta_bola_tablero" v-for="(item, index) in filas" :id="'bolaN'+((index+1)+(filas*2))" :key="index" fab dark :style="'color: white;'">{{((index+1)+(filas*2))}}</div>
                </div>

                <div style="width: 60px; display: inline-block;" class="div_pos">
                    <b style="color: white;">{{letras_bingo[3]}}</b><br>
                    <div class="porta_bola_tablero" v-for="(item, index) in filas" :id="'bolaN'+((index+1)+(filas*3))" :key="index" fab dark :style="'color: white;'">{{((index+1)+(filas*3))}}</div>
                </div>

                <div style="width: 60px; display: inline-block;" class="div_pos">
                    <b style="color: white;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{letras_bingo[4]}}</b><br>
                    <div class="porta_bola_tablero" v-for="(item, index) in filas" :id="'bolaN'+((index+1)+(filas*4))" :key="index" fab dark :style="'color: white;'">{{((index+1)+(filas*4))}}</div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="12">
            <div style="color: white; display: none;" id="div_botones_responsive">
                <button class="btn_acciones btn-volver-resp" @click="regresar()">{{$t('back')}}</button>
                
                <button class="btn_acciones btn-bingo-resp" id="btn_manual" @click="manualAcivo=true; inicioManual()"><b>Manual</b></button>

                <button class="btn_acciones btn-bingo-resp" id="btn_auto" @click="manualAcivo=false; inicioAutomatico()"><b>{{$t('Automatic')}}</b></button>

                <div style="color: black; font-size: 21px;" class="mb-3">
                    {{$t('Last')}}: <span style="font-size: 28px;">{{numero_anterior}}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{$t('Amount_played')}}: <span style="font-size: 28px;">{{numeros_marcados.length}}</span><br>

                    <div v-if="data_tablero.ganador" style="color: white;">
                        <b>{{$t('game_over1')}} {{data_tablero.ganador.split('(')[0] }} {{ (((data_tablero.ganador.split('('))[1]).split('*'))[2].replace(")", "") }} {{$t('game_over2')}}</b>
                    </div>
                    <div v-else>
                        <b>{{$t('No_wins')}}</b>
                    </div>
                </div>

                <Language />
            </div>
        </td>
    </tr>
    </tbody>
    </table>
    </div>

    
    <div class="col-sm-4 text-right img-responsive" id="banner_rv">
        <img :src="img_banner" style="max-width: 250px; min-heigth: 500px;">
    </div>

    </div>
</template>

<script>
    import Language from './LanguajesComponent.vue'
    export default {
        components: {
            Language
        },
        data(){
            return {
                bola_nueva: '',
                numero: 0,
                colores: ['#943126', '#7D3C98', '#2874A6', '#16A085', '#F1C40F', '#E67E22', '#2C3E50'],
                color_rand: '',
                num_rand: 0,
                data_tablero: [],
                letras_bingo: [],
                numeros_marcados: [],
                filas: 0,
                numero_anterior: 0,
                numeros_aleatorios: [],
                manualAcivo: false,
                ganadorActivo: false,
                img_tablero: '',
                img_banner: '',
                audioActivo: false,
                snackbar: false,
                text_snackbar: '',
                timeout: 500,
            }
        },
        created(){
            this.img_tablero = 'background-image: url("img/img_eventos/'+localStorage.img_tablero+'"); background-position: center; background-size: cover; border-radius: 10px; border: 2px solid black; position: relative; min-width: 320px; max-width: 390px; padding-bottom: 10px; padding-top: 3px; left: -10px;'

            this.img_banner = 'img/img_eventos/' + localStorage.img_banner
        },
        mounted() {
            this.letras_bingo = (localStorage.letrasbingo).split('')
            this.filas = (localStorage.nombrenumerosbingo/5)
            this.getNumerosJugados()
        },
        methods: {
            getNumerosJugados(){
                let me = this
                for( let i=1; i<=localStorage.nombrenumerosbingo; i++ ){
                    me.numeros_aleatorios.push(i)
                }
            },
            removeRepetidos(){
                let me = this
                for( let i=0; i<me.numeros_marcados.filter(Boolean).length; i++ ){
                    for( let j=0; j<me.numeros_aleatorios.length; j++ ){
                        if( parseInt(me.numeros_marcados.filter(Boolean)[i]) === parseInt(me.numeros_aleatorios[j]) ){
                            me.numeros_aleatorios.splice(j, 1)
                        }
                    }
                }
            },
            inicioManual(){
                let me = this
                $('.btn_acciones').prop("disabled", true);
                let pos_color = Math.floor(Math.random() * (me.colores.length - 0)) + 0;
                
                me.num_rand = Math.floor(Math.random() * ((parseInt(me.numeros_aleatorios.length)) - 0)) + 0
                if( me.numeros_aleatorios.length < 1 ){
                    console.log('Ya se han jugado todos los números')
                    return
                }
                
                let token = $('meta[name="csrf-token"]').attr('content')
                let formData = '_token='+token+'&evento='+localStorage.id_evento+'&numero='+me.numeros_aleatorios[me.num_rand]

                let url = '/enviarBola';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data){
                        me.data_tablero = data.api_crea_tablero.response
                        me.numeros_marcados = (me.data_tablero.numero).split('²')
                        me.iniciar(me.numeros_aleatorios[me.num_rand], pos_color)

                        me.numero_anterior = me.data_tablero.ultimo
                        if( me.data_tablero.ganador != '' && me.data_tablero.ganador != null ){
                            console.log('!Fin del juego, el jugador '+ me.data_tablero.ganador.split('(')[0] +' '+ (((me.data_tablero.ganador.split('('))[1]).split('*'))[2].replace(")", "") + ' ha ganado!')
                            return
                        }
                    }
                });
                
            },
            inicioAutomatico(){
                let me = this
                $('.btn_acciones').prop("disabled", true);
                let iteraciones =  setInterval(function(){
                    let pos_color = Math.floor(Math.random() * (me.colores.length - 0)) + 0;
                    
                    me.num_rand = Math.floor(Math.random() * ((parseInt(me.numeros_aleatorios.length)) - 0)) + 0
                    if( me.numeros_aleatorios.length < 1 ){
                        if(localStorage.idioma_bingo === 'es'){
                            me.text_snackbar = 'Ya se han jugado todos los números'
                        }else{
                            me.text_snackbar = 'All the numbers have already been played'
                        }
                        
                        me.snackbar = true
                        clearInterval(iteraciones)
                        return
                    }
                    

                    let token = $('meta[name="csrf-token"]').attr('content')
                    let formData = '_token='+token+'&evento='+localStorage.id_evento+'&numero='+me.numeros_aleatorios[me.num_rand]

                    let url = '/enviarBola';
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        success: function(data){
                            me.data_tablero = data.api_crea_tablero.response
                            me.numeros_marcados = (me.data_tablero.numero).split('²')
                            me.iniciar(me.numeros_aleatorios[me.num_rand], pos_color)
                            me.numero_anterior = me.data_tablero.ultimo
                            if( me.data_tablero.ganador != '' && me.data_tablero.ganador != null ){
                                console.log('!Fin del juego, el jugador '+ me.data_tablero.ganador.split('(')[0] +' '+ (((me.data_tablero.ganador.split('('))[1]).split('*'))[2].replace(")", "") + ' ha ganado!')
                                clearInterval(iteraciones)
                                return
                            }

                            if( me.manualAcivo === true ){
                                console.log('Modo manual activo')
                                clearInterval(iteraciones)
                                return
                            }
                        }
                    });
                    
                }, 5000);
            },
            iniciar(num, pos_color) {
                let me = this
                if( me.data_tablero.ganador != '' && me.data_tablero.ganador != null ){
                    return
                }
                me.numero = num
                if( me.audioActivo === true ){
                    var audio = new Audio("audio/"+num+".wav");
                    audio.play();
                }
                
                var id = null;
                var elem2 = document.getElementById("animate2");
                $('#animate2').css('background-color', me.colores[pos_color])
                $('#animate2').removeClass('rotar_bola')
                $('#animate2').addClass('rotar_bola')
                $('#animate2').finish().css('top', '0').css('left', '0');
                elem2.style.left = "20px";
                elem2.style.top = "-90px";
                var pos2 = -90;
                clearInterval(id);
                id = setInterval(frame, 5);
                function frame() {
                    if (pos2 == 600) {
                        me.pintarCasilleros()
                        $('.btn_acciones').prop("disabled", false);
                        clearInterval(id);
                    } else {
                        pos2++;
                        elem2.style.top = pos2 + "px";
                        if (pos2 > 500) {
                            elem2.style.top = "510px";
                                $('#animate2').animate({ left: '300px' }, "slowly");
                        }else{
                            elem2.style.top = pos2 + "px";
                        }
                    }
                    
                }
            },
            pintarCasilleros(){
                let me = this
                //filter(Boolean) no toma en cuenta espacios en vacios
                for( let i=0; i<me.numeros_marcados.filter(Boolean).length; i++ ){
                    $('#bolaN'+me.numeros_marcados.filter(Boolean)[i]).addClass('casillero_marcado');
                }
                me.removeRepetidos()
            },
            regresar(){
                window.location.href = '/eventos'
            }
        },
    }
</script>

<style>
        .bolas{
            width: 90px; height: 90px; padding: 15px 10px 0px 10px; font-size: 35px; border-radius: 100%; color: black;
            -webkit-box-shadow: 0px 5px 14px 0px rgb(56, 56, 56);
            -moz-box-shadow: 0px 5px 14px 0px rgb(54, 54, 54);
            box-shadow: 0px 5px 14px 0px rgb(59, 59, 59);
            -ms-border-radius: 150px;
            -moz-border-radius: 150px;
            -webkit-border-radius: 150px;
            -o-border-radius: 150px;
            border-radius: 150px;
            margin: 0px auto;
            position: absolute;
            left: 12px;
            z-index: -1;
            top: -90px;
            color: white;
        }
        .porta_bola_tablero{
            cursor: pointer;
            margin-left: 10px !important;
            width: 35px;
            height: 35px;
            text-align: center;
            margin: 4px;
            border-radius: 100%;
            padding-top: 4px;
            font-size: 19px;
            background-color: #5e4dfa67;
            -webkit-box-shadow: -4px 4px 25px -6px rgba(0,0,0,0.75);
-moz-box-shadow: -4px 4px 25px -6px rgba(0,0,0,0.75);
box-shadow: -4px 4px 25px -6px rgba(0,0,0,0.75);
        }
        .porta_bola_tablero:hover{
            background-color: #2915dd67;
        }
        .casillero_marcado{
            background: rgb(34,175,133);
background: radial-gradient(circle, rgba(34,175,133,0.865983893557423) 73%, rgba(123,31,162,0.4598214285714286) 86%, rgba(249,22,99,0.4542191876750701) 100%);
        }
        .sombra_bola_pintada{
            -webkit-box-shadow: inset 0px 0px 20px 0px rgba(0,0,0,0.75);
            -moz-box-shadow: inset 0px 0px 20px 0px rgba(0,0,0,0.75);
            box-shadow: inset 0px 0px 20px 0px rgba(0,0,0,0.75);
        }
        .tubo{
            -webkit-box-shadow: 6px 9px 22px 0px rgb(24, 24, 24);
            -moz-box-shadow: 6px 9px 22px 0px rgb(44, 44, 44);
            box-shadow: 6px 9px 22px 0px rgb(3, 3, 3);

            background: rgb(33,33,33);
background: linear-gradient(90deg, rgba(33,33,33,0.48783263305322133) 0%, rgba(67,138,117,0.09007352941176472) 22%, rgba(3,9,8,0.7819502801120448) 89%);
        }
        .btn-start{
            font-size: 30px;
            background-color: orange;
        }

        .stage {
            display: flex;
            height: 330px;
            width: 100%;
        }
        .box {
            align-self: flex-end;
            animation-duration: 2s;
            animation-iteration-count: infinite;
            background-color: #F44336;
            height: 90px;
            margin: 0 auto 0 auto;
            transform-origin: bottom;
            width: 90px;
        }
        .bola2 {
            animation-name: bola2;
            animation-timing-function: ease;
        }
        @keyframes bola2 {
            0%   { transform: translateY(0); }
            50%  { transform: translateY(-100px); }
            100% { transform: translateY(0); }
        }

        .rotar_bola {
                animation: rotation 2s infinite linear;
        }

        @keyframes rotation {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(359deg);
                }
        }
    .btn-volver{
        border: 2px solid black; padding: 0%; border-radius: 100%; height: 50px; width: 50px;
        -webkit-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
        -moz-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
        box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
    }
    .btn-volver:hover{
        background-color: rgba(20, 20, 20, 0.568);
        color: white;
    }
    .btn-bingo{
        border: 2px solid black; padding: 0%; border-radius: 100%; height: 90px; width: 90px;
        -webkit-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
    }

    .btn-bingo:hover{
        background-color: rgba(20, 20, 20, 0.568);
        color: white;
    }
    

    .btn-bingo-resp{
        border: 2px solid black; padding: 0%; border-radius: 10px; height: 40px; width: 100px;
        -webkit-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
        -moz-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
        box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
    }

    .btn-volver-resp{
        border: 2px solid black; padding: 0%; border-radius: 100%; height: 50px; width: 50px;
        -webkit-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
        -moz-box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
        box-shadow: 0px 4px 14px 1px rgba(0,0,0,0.75);
        display: inline-block;
    }
    @media(max-width:900px){
        #banner_rv{
            display: none;
        }
    }

    @media(max-width:500px){
        .tabla_bingo{
             position: relative; top: -30px;
        }
        #animate2{
            width: 48px !important;
            height: 48px !important;
            font-size: 24px !important;
            padding-top: 5px !important;
        }

        #tubo_vertical{
            width: 50px !important;
        }

        #tubo_horizontal{
            height: 60px !important;
        }
        #div_tablero{
            transform: scaleY(0.9);
        }
        #div_botones_responsive{
            display: block !important;
            width: 100%;
            position: relative;
            z-index: 2;
            top: -20px;
        }
        #div_botones{
            display: none;
        }

        .div_pos{
            width: 45px !important;
        }
    }
    </style>
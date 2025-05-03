<template>
    <div>
        
    <v-app style="border-radius: 20px; background-color: rgba(0, 0, 0, 0);">
        <v-snackbar transition="fab-transition" v-model="snackbar" rounded top :timeout="timeout" >
            {{ text_snackbar }}
            <template v-slot:action="{ attrs }">
                <v-btn color="pink" dense small text v-bind="attrs" @click="snackbar = false" > x </v-btn>
            </template>
        </v-snackbar>

        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64" ></v-progress-circular>
        </v-overlay>


        <v-dialog v-model="dialogInicio" persistent >
        <v-card style="background: rgb(32,191,186);
background: radial-gradient(circle, rgba(32,191,186,1) 0%, rgba(0,106,92,1) 3%, rgba(130,46,66,1) 100%);">
            <v-card-text class="text-center"><br><br>
                <v-btn fab dark dense large color="dark" @click="dialogInicio=false; getNumerosJugados(); getNumerosCartilla()"> {{ $t('Start') }} </v-btn><br><br>
            </v-card-text>
        </v-card>
        </v-dialog>


        <v-dialog v-model="dialog_ganador" max-width="500px" >
            <v-card>
                <v-card-text>
                <v-container>
                    <v-row class="pt-5">
                    <v-col cols="12" sm="12" md="12" class="mb-0" >
                        
                        
                        <h1>{{ text_ganador }}</h1>


                    </v-col>
                    </v-row>
                </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-card-title>
            <div class="col-sm-12" align="center">
                <Language style="position: fixed;"/>

                <v-btn fab dark dense small color="dark" v-if="audioActivo===false" @click="text_snackbar='Audio activado/ON'; snackbar=true; audioActivo=true"><v-icon dark> mdi-headphones-off </v-icon></v-btn>
                <v-btn fab dark dense small color="indigo" v-else @click="text_snackbar='Audio desactivado/OFF'; snackbar=true; audioActivo=false"><v-icon dark> mdi-headphones </v-icon></v-btn>

                <span style="color: white">( {{$t('Last')}}: {{ultimo_num}} )</span><br>
                {{$t('Played_numbers')}}
                <div class="col-sm-12 text-center mt-3 p-0">
                    <button class="porta_bola_cartilla" v-for="(item, index) in num_jugados.filter(Boolean).length" :key="item.id" :index="index">
                        <span class="int_bola" v-if="num_jugados.filter(Boolean)[index].split('').length === 1">&nbsp;{{num_jugados.filter(Boolean)[index]}}&nbsp;</span>
                        <span class="int_bola" v-else>{{num_jugados.filter(Boolean)[index]}}</span>
                    </button>
                </div>


                
                <div v-if="data_cartillas.length > 0">
                <div class="col-sm-12 content_cartilla" :style="img_cartilla + 'width: 360px; padding: 0px 10px 10px 10px; background-color: white; border-radius: 20px; -webkit-box-shadow: 5px 5px 15px 0px rgba(0,0,0,0.75); -moz-box-shadow: 5px 5px 15px 0px rgba(0,0,0,0.75); box-shadow: 5px 5px 15px 0px rgba(0,0,0,0.75); display: inline-block; margin: 20px;'" v-for="(itemT, indexT) in data_cartillas.length" :key="itemT.id" :index="indexT">
                    <span style="color: white; font-size: 17px;">{{$t('Card')}} {{indexT+1}}</span> 
                    <table id="table">
                    <thead>
                        <tr>
                            <th style="text-shadow: 0.1em 0.1em 0.2em white"> {{ palabra[0] }} </th>
                            <th style="text-shadow: 0.1em 0.1em 0.2em white"> {{ palabra[1] }} </th>
                            <th style="text-shadow: 0.1em 0.1em 0.2em white"> {{ palabra[2] }} </th>
                            <th style="text-shadow: 0.1em 0.1em 0.2em white"> {{ palabra[3] }} </th>
                            <th style="text-shadow: 0.1em 0.1em 0.2em white"> {{ palabra[4] }} </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <button :id="'cas_'+indexT+'0'" v-if="data_cartillas[indexT].numeros.split(',')[0] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[0], 0, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[0]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[0]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'1'" v-if="data_cartillas[indexT].numeros.split(',')[1] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[1], 1, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[1]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[1]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'2'" v-if="data_cartillas[indexT].numeros.split(',')[2] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[2], 2, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[2]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[2]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'3'" v-if="data_cartillas[indexT].numeros.split(',')[3] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[3], 3, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[3]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[3]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'4'" v-if="data_cartillas[indexT].numeros.split(',')[4] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[4], 4, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[4]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[4]) * -1}}</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button :id="'cas_'+indexT+'5'" v-if="data_cartillas[indexT].numeros.split(',')[5] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[5], 5, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[5]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[5]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'6'" v-if="data_cartillas[indexT].numeros.split(',')[6] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[6], 6, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[6]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[6]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'7'" v-if="data_cartillas[indexT].numeros.split(',')[7] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[7], 7, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[7]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[7]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'8'" v-if="data_cartillas[indexT].numeros.split(',')[8] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[8], 8, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[8]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[8]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'9'" v-if="data_cartillas[indexT].numeros.split(',')[9] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[9], 9, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[9]}}</button>
                                <button v-else class="cuadro_num_marc">{{(data_cartillas[indexT].numeros.split(',')[9]) * -1}}</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button :id="'cas_'+indexT+'10'" v-if="data_cartillas[indexT].numeros.split(',')[10] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[10], 10, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[10]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[10]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'11'" v-if="data_cartillas[indexT].numeros.split(',')[11] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[11], 11, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[11]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[11]) * -1}}</button>
                            </td>
                            <td> <!-- FREE -->
                                {{ data_cartillas[indexT].numeros.split(',')[12] }}
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'13'" v-if="data_cartillas[indexT].numeros.split(',')[13] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[13], 13, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[13]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[13]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'14'" v-if="data_cartillas[indexT].numeros.split(',')[14] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[14], 14, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[14]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[14]) * -1}}</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button :id="'cas_'+indexT+'15'" v-if="data_cartillas[indexT].numeros.split(',')[15] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[15], 15, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[15]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[15]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'16'" v-if="data_cartillas[indexT].numeros.split(',')[16] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[16], 16, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[16]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[16]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'17'" v-if="data_cartillas[indexT].numeros.split(',')[17] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[17], 17, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[17]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[17]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'18'" v-if="data_cartillas[indexT].numeros.split(',')[18] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[18], 18, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[18]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[18]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'19'" v-if="data_cartillas[indexT].numeros.split(',')[19] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[19], 19, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[19]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[19]) * -1}}</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button :id="'cas_'+indexT+'20'" v-if="data_cartillas[indexT].numeros.split(',')[20] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[20], 20, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[20]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[20]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'21'" v-if="data_cartillas[indexT].numeros.split(',')[21] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[21], 21, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[21]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[21]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'22'" v-if="data_cartillas[indexT].numeros.split(',')[22] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[22], 22, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[22]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[22]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'23'" v-if="data_cartillas[indexT].numeros.split(',')[23] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[23], 23, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[23]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[23]) * -1}}</button>
                            </td>
                            <td>
                                <button :id="'cas_'+indexT+'24'" v-if="data_cartillas[indexT].numeros.split(',')[24] > 0" @click="marcar_manual(data_cartillas[indexT].numeros.split(',')[24], 24, indexT)" class="cuadro_num">{{data_cartillas[indexT].numeros.split(',')[24]}}</button>
                                <button v-else class="cuadro_num_marc" >{{(data_cartillas[indexT].numeros.split(',')[24]) * -1}}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
                </div>
                <div v-else>
                    {{$t('No_cards')}}
                </div>
                
            </div>
        </v-card-title>

    </v-app>
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
                num_jugados: [],
                dialogEvento: false,
                id_evento: '',
                id_jugador: '',
                palabra: [],
                ganadorActivo: false,
                ganador: '',
                data_cartillas: [],
                data_gen_cartillas: [],
                overlay: false,
                num_marcado: false,
                snackbar: false,
                text_snackbar: '',
                timeout: 500,
                text_ganador: '',
                dialog_ganador: false,
                ultimo_num: 0,
                audioActivo: false,
                anterior: '',
                dialogInicio: true,
                img_cartilla: '',
            }
        },
        watch: {
            overlay (val) {
                val && setTimeout(() => {
                this.overlay = false
                }, 7000)
            },
        },
        created(){
            
        },
        mounted() {
            this.id_evento = this.getUrlParam('id_evento','BINGO').split('*')[0]
            this.id_jugador = this.getUrlParam('id_evento','BINGO').split('*')[1]
            // this.palabra = (this.getUrlParam('palabra','BINGO')).split('')
            // this.getNumerosJugados()
            // this.getNumerosCartilla()
        },
        methods: {
            getNumerosJugados(){
                let me = this
                
                me.overlay = !this.overlay;
                let iteraciones =  setInterval(function(){
                    let token = $('meta[name="csrf-token"]').attr('content')
                    let formData = '_token='+token+'&id_evento='+me.id_evento

                    $.ajax({
                        url: '/getNumerosJugados',
                        type: 'POST',
                        data: formData,
                        success: function(data){
                            me.palabra = data.api_crea_tablero.response.letrasbingo
                            me.img_cartilla = "margin-top: 30px; border-radius: 10px; background-image: url('img/img_eventos/"+data.api_crea_tablero.response.img_casillero+"'); background-position: center; background-size: cover;"
                            me.ultimo_num = (data.api_crea_tablero.response).ultimo
                            me.num_jugados = (data.api_crea_tablero.response).numero.split('²')
                            me.ganador = (data.api_crea_tablero.response).ganador
                            me.reproducirAudio(me.ultimo_num)
                            if( me.ganador != '' && me.ganador != null ){
                                me.ganadorActivo = true
                                if(localStorage.idioma_bingo === 'es'){
                                    me.text_ganador = '!Fin de juego, el jugador '+ me.ganador.split('(')[0] +' '+ (((me.ganador.split('('))[1]).split('*'))[2].replace(")", "") + ' ha ganado!'
                                }else{
                                    me.text_ganador = '!Game over, player '+ me.ganador.split('(')[0] +' '+ (((me.ganador.split('('))[1]).split('*'))[2].replace(")", "") + ' wins!'
                                }
                                
                                me.dialog_ganador = true
                                clearInterval(iteraciones)
                                return
                            }
                        }
                    });
                    // fetch('/getNumerosJugados/'+me.id_evento)
                    // .then(response => response.json())
                    // .then( data => {
                    //     me.ultimo_num = (data.api_crea_tablero.response).ultimo
                    //     me.num_jugados = (data.api_crea_tablero.response).numero.split('²')
                    //     me.ganador = (data.api_crea_tablero.response).ganador
                    //     me.reproducirAudio(me.ultimo_num)
                    //     if( me.ganador != '' && me.ganador != null ){
                    //         me.ganadorActivo = true
                    //         me.text_ganador = '!Fin del juego, el jugador '+ me.ganador.split('(')[0] +' '+ (((me.ganador.split('('))[1]).split('*'))[2].replace(")", "") + ' ha ganado!'
                    //         me.dialog_ganador = true
                    //         clearInterval(iteraciones)
                    //         return
                    //     }

                    // })
                }, 5000);
            },
            reproducirAudio(ultimo_num){
                let me = this
                if( me.anterior === ultimo_num ){
                    return
                }
                me.anterior = ultimo_num
                if( me.audioActivo === true ){
                    var audio = new Audio("audio/"+ultimo_num+".wav");
                    audio.play();
                }
            },
            getNumerosCartilla(){
                let me = this
                
                let iteraciones = setInterval(function(){

                     $.ajax({
                        url: '/getNumerosCartilla/'+me.id_evento+'*'+me.id_jugador,
                        type: 'GET',
                        data: '',
                        success: function(data){
                            me.data_cartillas = data.consultacardstotal.response.response
                        
                            if( me.ganadorActivo === true ){
                                clearInterval(iteraciones)
                                return
                            }
                        }
                    });

                    // fetch('/getNumerosCartilla/'+me.id_evento+'*'+me.id_jugador)
                    // .then(response => response.json())
                    // .then( data => {
                    //     me.data_cartillas = data.consultacardstotal.response.response
                        
                    //     if( me.ganadorActivo === true ){
                    //         clearInterval(iteraciones)
                    //         return
                    //     }
                    // })


                }, 5000);
            },
            getUrlParam(parameter, defaultvalue){
                let me = this
                var urlparameter = defaultvalue;
                if(window.location.href.indexOf(parameter) > -1){
                    urlparameter = me.getUrlVars()[parameter];
                    }
                return urlparameter;
            },
            getUrlVars() {
                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                    vars[key] = value;
                });
                return vars;
            },
            marcar_manual(num, id, index){
                let me = this
                me.num_marcado = num
                let numeros_jugados = me.num_jugados.filter(Boolean)
                let valid = me.checkMarcado(numeros_jugados)
                if( valid === false ){
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Este número aún no se ha jugado'
                    }
                    else{
                        me.text_snackbar = 'This number has yet to be played'
                    }
                    
                    me.snackbar = true

                    if( $('#cas'+'_'+index+''+id).hasClass('numero_incorrecto') ){
                        $('#cas'+'_'+index+''+id).removeClass('numero_incorrecto')
                    }else{
                        $('#cas'+'_'+index+''+id).addClass('numero_incorrecto')
                    }

                }else{
                    me.text_snackbar = 'Enhorabuena!, número acertado.'
                    me.snackbar = true
                }
            },
            checkMarcado(numeros_jugados) {
                let me = this
                return numeros_jugados === me.num_marcado;
            }
        },
    }
</script>

<style>
th{
    padding: 3px;
    text-align: center;
}
td{
    padding: 3px;
    text-align: center;
}
.int_bola{
    width: 20px;
    height: 20px;
    padding: 3px; border-radius: 100%; background-color: white; color: black;
}
.cuadro_num{
    -webkit-box-shadow: 0px 3px 16px -1px rgba(92,92,92,1);
    -moz-box-shadow: 0px 3px 16px -1px rgba(92,92,92,1);
    box-shadow: 0px 3px 16px -1px rgba(92,92,92,1);
    border: 1px solid rgb(243, 53, 85);
    border-radius: 5px;
    margin: 0px;
    height: 55px;
    width: 60px;
    background-color: rgba(255, 255, 255, 0.863);
}

.porta_bola_cartilla{
            width: 35px;
            height: 35px;
            margin: 2px;
            border-radius: 100%;
            padding: 0%;
            font-size: 15px;
            background-color: #ebb916;
            -webkit-box-shadow: -4px 4px 25px -6px rgba(0,0,0,0.75);
-moz-box-shadow: -4px 4px 25px -6px rgba(0,0,0,0.75);
box-shadow: -4px 4px 25px -6px rgba(0,0,0,0.75);
            color: white;
            font-weight: bolder;
            cursor: pointer
        }

    .cuadro_num_marc{
        -webkit-box-shadow: 0px 3px 16px -1px rgba(92,92,92,1);
        -moz-box-shadow: 0px 3px 16px -1px rgba(92,92,92,1);
        box-shadow: 0px 3px 16px -1px rgba(92,92,92,1);
        border: 1px solid rgb(0, 0, 0);
        border-radius: 5px;
        margin: 0px;
        height: 55px;
        width: 60px;
        color: white;
        background-color: rgba(12, 12, 12, 0.322);
    }

    .numero_incorrecto{
        background-color: #d3323fc0;
    }

    @media(max-width:450px){

        .cuadro_num_marc{
            height: 40px;
            width: 45px;
        }

        .cuadro_num{
            height: 40px;
            width: 45px;
        }

        .content_cartilla{
            width: 290px !important;
        }

    }
</style>
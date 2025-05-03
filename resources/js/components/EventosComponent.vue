<template>
<div align="right">

    <Language />

    <div align="left">
    <v-app>
        <v-snackbar v-model="snackbar" top color="indigo" :timeout="timeout" >
            {{ text_snackbar }}
            <template v-slot:action="{ attrs }">
                <v-btn color="blue" text v-bind="attrs" @click="snackbar = false" > {{ $t('close') }} </v-btn>
            </template>
        </v-snackbar>

        <v-overlay :value="overlay"  style='z-index:20001;'>
            <v-progress-circular indeterminate size="64" ></v-progress-circular>
        </v-overlay>

        <v-dialog v-model="dialogEvento" max-width="800px" >
            <v-card>
                <v-toolbar color="teal" dark > <h4>{{ $t('event') }}</h4> </v-toolbar>
                <v-card-text>
                <v-container>
                    <v-row class="pt-5">
                    <v-col cols="12" sm="6" md="6">
                        <v-select v-model="cliente" :items="clientes" item-text="nombre" item-value="id" :label="$t('Client')"  hide-detail outlined dense ></v-select>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('Event_name')" v-model="nombre_evento" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('Word')" v-model="palabra" maxlength="5" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6">
                        <v-select v-model="cant_num_bingo" :items="numerosbingo" item-text="label" item-value="id" :label="$t('Number_numbers')"  hide-details dense outlined ></v-select>
                    </v-col>

                    <v-col cols="12" sm="6" md="4" >
                    <v-menu v-model="menu" hide-details dense :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y min-width="auto" >
                        <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="date" :label="$t('Date')" hide-details dense outlined prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" ></v-text-field>
                        </template>
                        <v-date-picker v-model="date" @input="menu = false" ></v-date-picker>
                    </v-menu>
                    </v-col>

                    <v-col cols="12" sm="6" md="4" >
                    <v-menu ref="menu" v-model="menut" hide-details dense :close-on-content-click="false" :nudge-right="40" :return-value.sync="time" transition="scale-transition" offset-y max-width="290px" min-width="290px" >
                        <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="time" :label="$t('Time')" hide-details dense outlined prepend-icon="mdi-clock-time-four-outline" readonly v-bind="attrs" v-on="on" ></v-text-field>
                        </template>
                        <v-time-picker v-if="menut" v-model="time" full-width @click:minute="$refs.menu.save(time)" ></v-time-picker>
                    </v-menu>
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                        <v-select v-model="estado" :items="estados" item-text="label" item-value="id" :label="$t('Status')"  hide-details dense outlined ></v-select>
                    </v-col>

                    <v-col cols="12" sm="12" class="text-center" v-if="editarActivo===false"> Consejo: Si no guarda ninguna imagen el juego tomará las imágenes por defecto. </v-col>

                    <v-col cols="12" sm="6" md="4">
                        <v-file-input accept="image/*" :label="$t('Board_picture') + '400*700'" type="file" name="file1" id="file1" hide-details outlined dense ></v-file-input>
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                        <v-file-input accept="image/*" :label="$t('Card_picture') + '400x400'" type="file" name="file2" id="file2" hide-details outlined dense ></v-file-input>
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                        <v-file-input accept="image/*" :label="$t('Banner_picture') + '250x700'" type="file" name="file3" id="file3" hide-details outlined dense ></v-file-input>
                    </v-col>

                    <v-col cols="12" sm="12" md="12" class="mb-0" >
                        <v-btn color="indigo" dark dense outlined @click="dialogEvento = false" > {{ $t('Close') }} </v-btn>
                        <v-btn color="teal" v-if="editarActivo===false" dark dense @click="guardarEvento();" > {{ $t('Save') }} </v-btn>
                        <v-btn color="primary" v-else dark dense @click="editarEvento();" > {{ $t('Edit') }} </v-btn>
                    </v-col>
                    </v-row>

                    
                </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>


        <v-dialog v-model="dialogListaJugadores" max-width="800px" >
            <v-card>
                <v-toolbar color="teal" dark >{{ $t('Players') }}</v-toolbar>
                <v-card-text>
                <v-container>
                     <v-btn class="mx-2" color="indigo" dark small @click="editarJugadorActivo=false; limpiarFormJugador(); dialogAgregarJugadores = true;">
                        {{ $t('Add_player') }} <v-icon dark>mdi-plus</v-icon>
                    </v-btn>
                    <v-row class="col-sm-12">
                    <v-data-table :headers="headers_jugadores" :items="jugadores" style="width: 100%;">
                    <template v-slot:item="row">
                        <tr>
                            <td>{{row.item.id}} - {{row.item.Nombres}}</td>
                            <td>{{row.item.Telefono}}</td>
                            <td>{{row.item.Correo}}</td>
                            <td>{{row.item.Ciudad}}</td>
                            <td>{{row.item.Numerocartillas}}</td>
                            <td>
                                <v-btn style="display: inline-block;" fab dark x-small color="warning" @click="editarJugadorActivo=true; formularioEditJugador(row.item)"> <v-icon dark>mdi-pencil</v-icon> </v-btn>

                                <v-btn style="display: inline-block;" fab dark x-small color="indigo" @click="copiarLink(row.item)"> <v-icon dark>mdi-link</v-icon> </v-btn>
                            </td>
                        </tr>
                    </template>
                    </v-data-table>
                    
                    </v-row>
                </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>


        <v-dialog v-model="dialogAgregarJugadores" max-width="600px" >
            <v-card>
                <v-toolbar color="teal" dark >{{ $t('Player') }}</v-toolbar>
                <v-card-text>
                <v-container>
                    <v-row class="pt-5">
                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('Name')" v-model="nombres_jugador" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('Phone')" type="number" v-model="telefono_jugador" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('Email')" type="email" v-model="correo_jugador" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('City')" v-model="ciudad_jugador" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('Num_cards')" type="number" v-model="num_cartillas_jugador" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="12" md="12" class="mb-0" >
                        <v-btn color="indigo" dark dense outlined @click="dialogAgregarJugadores = false" > {{ $t('Close') }} </v-btn>
                        <v-btn v-if="editarJugadorActivo===false" color="teal" dark dense @click="guardarJugador();" > {{ $t('Save') }} </v-btn>
                        <v-btn v-if="editarJugadorActivo===true" color="indigo" dark dense @click="editarJugador();" > {{ $t('Edit') }} </v-btn>
                    </v-col>
                    </v-row>
                </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>


        <!-- LISTADO EVENTOS -->
        <v-card-title>
            <v-btn class="mx-2" color="teal" dark small @click="limpiarFormulario(); editarActivo=false; dialogEvento = true">
                {{ $t('Create_event') }} <v-icon dark>mdi-plus</v-icon>
            </v-btn>
            <!-- {{ $t('hello') }} -->

        <!-- <v-col cols="12" sm="4" md="4" class="ml-auto" >
            <v-text-field v-model="search" append-icon="mdi-magnify" label="Buscar" outlined dense hide-details ></v-text-field>
        </v-col> -->
        </v-card-title>
        <v-layout v-if="eventos != 0" v-resize="onResize" column>
        <v-data-table :headers="headers" :items="eventos" :class="{mobile: isMobile}">
        <template v-slot:item="row">
            <tr>
                <td>{{row.item.nombrecliente}}</td>
                <td>{{row.item.id}} - {{row.item.Nombreevento}}</td>
                <td>{{row.item.letrasbingo}}</td>
                <td>{{row.item.nombrenumerosbingo}}</td>
                <td>{{row.item.Fecha}} {{row.item.Hora}}</td>
                <td>{{row.item.nombreestado}}</td>
                <td>
                    <v-btn class="mx-2" fab dark x-small color="teal" @click="irTablero(row.item)"> <v-icon dark>mdi-play</v-icon> </v-btn>

                    <v-btn class="mx-2" fab dark x-small color="pink" @click="editarActivo=true; id_evento = row.item.id; dialogEvento=true; editarFormulario(row.item)"> <v-icon dark>mdi-pencil</v-icon> </v-btn>

                    <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn class="mx-2" fab dark x-small v-bind="attrs" v-on="on" style="background-color: #512CD7;" @click="data_evento = row.item; id_evento = row.item.id; getJugadores()"> <v-icon dark>mdi-account-multiple</v-icon> </v-btn>
                    </template>
                    <span>{{ $t('See_players') }}</span>
                    </v-tooltip>

                    <!-- <v-btn class="mx-2" fab dark x-small style="background-color: #F1083D;" @click="eliminarEvento(row.item, row.index)"> <v-icon dark>mdi-minus</v-icon> </v-btn> -->
                </td>
            </tr>

        </template>

        <v-alert slot="no-results" :value="true" color="error" icon="warning">
            {{ $t('No_results') }} "{{ search }}".
        </v-alert>

        </v-data-table>
        </v-layout>

        <div v-else class="mt-8 text-center"><h5>{{ $t('No_events') }}</h5></div>

    </v-app>
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
                clientes: [],
                eventos: [],
                search: '',
                selected: [],
                isMobile: false,
                headers: [
                    { text: 'Client', value: '' },
                    { text: 'Event', value: '' },
                    { text: 'Word', value: '' },
                    { text: 'Numbers', value: '' },
                    { text: 'Date', value: '' },
                    { text: 'Status', value: '' },
                    { text: 'Actions', value: '' },
                ],
                headers_jugadores: [
                    { text: 'Names', value: '' },
                    { text: 'Phone', value: '' },
                    { text: 'Email', value: '' },
                    { text: 'City', value: '' },
                    { text: 'N° Cards', value: '' },
                    { text: 'Actions', value: '' },
                ],
                dialogEvento: false,
                date: '',
                cliente: '',
                nombre_evento: '',
                menu: false,
                time: null,
                menut: false,
                estado: '',
                estado_evento: '',
                cant_num_bingo: 0,
                jugadores: [],
                estados: [],
                estados_evento: [],
                numerosbingo: [],
                palabra: '',
                editarActivo: false,
                id_evento: '',
                dialogListaJugadores: false,
                dialogAgregarJugadores: false,
                nombres_jugador: '',
                telefono_jugador: '',
                correo_jugador: '',
                ciudad_jugador: '',
                num_cartillas_jugador: '',
                id_jugador: '',
                editarJugadorActivo: false,
                data_evento: [],
                snackbar: false,
                text_snackbar: '',
                timeout: 3000,
                overlay: false,
                fileTablero: '',
                fileCartilla: '',
                fileBanner: '',
                colorfondo_tablero: '',
            }
        },
        watch: {
            overlay (val) {
                val && setTimeout(() => {
                this.overlay = false
                }, 15000)
            },
        },
        created(){
            
        },
        mounted() {
            this.getClientes()
            this.getEventos()
        },
        methods: {
            changeLanguage(locale) {
                i18n.locale = locale
            },
            onResize() {
            /* if (window.innerWidth < 769)
                this.isMobile = true;
            else
                this.isMobile = false; */
            },
            getClientes(){
                let me = this

                fetch('/getEstados')
                .then(response => response.json())
                .then( data => {
                    let t_estados = data.api_sel_tablasgen.response

                    //cargar vector estados
                    let estados = (t_estados.estados.split('²'))
                    let estado_split = []
                    for( let i=0; i<estados.length; i++ ){
                        estado_split = estados[i].split(',')
                        me.estados.push({id: estado_split[0], label: estado_split[1]})
                    }

                    //cargar vector estados evento
                    let estadosevento = (t_estados.estadosevento.split('²'))
                    let estadosevento_split = []
                    for( let i=0; i<estadosevento.length; i++ ){
                        estadosevento_split = estadosevento[i].split(',')
                        me.estados_evento.push({id: estadosevento_split[0], label: estadosevento_split[1]})
                    }

                    //cargar vector estados evento
                    let numerosbingo = (t_estados.numerosbingo.split('²'))
                    let numerosbingo_split = []
                    for( let i=0; i<numerosbingo.length; i++ ){
                        numerosbingo_split = numerosbingo[i].split(',')
                        me.numerosbingo.push({id: numerosbingo_split[0], label: numerosbingo_split[1]})
                    }
                })

                fetch('/getClientes')
                .then(response => response.json())
                .then(data => me.clientes = data.api_crea_cliente.response.response);

            },
            getEventos(){
                let me = this
                fetch('/getEventos')
                .then(response => response.json())
                .then(data => me.eventos = data.api_crea_evento.response.response);
            },
            guardarEvento(){
                let me = this
                
                if( me.cliente === '' || me.nombre_evento === '' || me.palabra === '' || me.cant_num_bingo === '' || me.date === '' || me.time === '' || me.estado === '' ){
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Complete todos los campos antes de guardar'
                    }else{
                        me.text_snackbar = 'Complete all fields before saving.'
                    }
                    me.snackbar = true
                    return
                }
                me.overlay = !me.overlay;
                let fileImgTablero = document.getElementById("file1").files[0];
                let fileImgCartilla = document.getElementById("file2").files[0];
                let fileImgBanner = document.getElementById("file3").files[0];
                let token = $('meta[name="csrf-token"]').attr('content')
                var formData = new FormData();
                formData.append('_token', token);
                formData.append('cliente',  me.cliente);
                formData.append('nombre_evento',  me.nombre_evento);
                formData.append('date', me.date);
                formData.append('time', me.time);
                formData.append('estado', me.estado);
                formData.append('cant_num_bingo', me.cant_num_bingo);
                formData.append('palabra', me.palabra);
                formData.append('img_tablero', fileImgTablero);
                formData.append('img_casillero', fileImgCartilla);
                formData.append('img_banner', fileImgBanner);
                formData.append('colorfondo_tablero', me.colorfondo_tablero);

                let url = '/guardarEvento';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    enctype: 'multipart/form-data',
                    processData: false,
                    success: function(data){
                        me.getEventos()
                        me.dialogEvento = false
                        me.overlay = false
                        
                        if(localStorage.idioma_bingo === 'es'){
                            me.text_snackbar = 'Evento guardado correctamente'
                        }else{
                            me.text_snackbar = 'Event successfully saved'
                        }
                        me.snackbar = true
                    }
                });

            },
            limpiarFormulario(){
                let me = this
                me.cliente = ''; me.nombre_evento = ''; me.palabra = ''; me.cant_num_bingo = ''; me.date = ''; me.time = ''; me.estado = ''; me.fileTablero = ''; me.fileCartilla = ''; me.colorfondo_tablero = ''; me.fileBanner = '';

                $('#file1').val(null)
                $('#file2').val(null)
                $('#file3').val(null)
            },
            editarFormulario(item){
                let me = this
                me.limpiarFormulario()
                me.cliente = item.Cliente
                me.nombre_evento = item.Nombreevento
                me.palabra = item.letrasbingo
                me.cant_num_bingo = item.numerosbingo
                me.date = item.Fecha
                me.time = item.Hora
                me.estado = item.Estado
                me.fileTablero = item.img_tablero
                me.fileCartilla = item.img_casillero
                me.fileBanner = item.img_banner
            },
            editarEvento(){
                let me = this
                if( me.cliente === '' || me.nombre_evento === '' || me.palabra === '' || me.cant_num_bingo === '' || me.date === '' || me.time === '' || me.estado === '' ){
                    
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Complete todos los campos antes de guardar'
                    }else{
                        me.text_snackbar = 'Complete all fields before saving'
                    }
                    me.snackbar = true
                    return
                }
                me.overlay = !me.overlay;
                let fileImgTablero = document.getElementById("file1").files[0];
                let fileImgCartilla = document.getElementById("file2").files[0];
                let fileImgBanner = document.getElementById("file3").files[0];
                let token = $('meta[name="csrf-token"]').attr('content')
                var formData = new FormData();
                formData.append('_token', token);
                formData.append('id_evento',  me.id_evento);
                formData.append('cliente',  me.cliente);
                formData.append('nombre_evento',  me.nombre_evento);
                formData.append('date', me.date);
                formData.append('time', me.time);
                formData.append('estado', me.estado);
                formData.append('cant_num_bingo', me.cant_num_bingo);
                formData.append('palabra', me.palabra);
                formData.append('img_tablero', fileImgTablero);
                formData.append('img_casillero', fileImgCartilla);
                formData.append('img_banner', fileImgBanner);
                formData.append('colorfondo_tablero', fileImgCartilla);
                formData.append('img_tablero_old', me.fileTablero);
                formData.append('img_casillero_old', me.fileCartilla);
                formData.append('img_banner_old', me.fileBanner);

                let url = '/editarEvento';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    enctype: 'multipart/form-data',
                    processData: false,
                    success: function(data){
                        me.getEventos()
                        me.dialogEvento = false
                        me.overlay = false
                        
                        if(localStorage.idioma_bingo === 'es'){
                            me.text_snackbar = 'Evento editado correctamente'
                        }else{
                            me.text_snackbar = 'Event successfully edited'
                        }
                        me.snackbar = true
                    }
                });
            },
            irTablero(item){
                localStorage.img_tablero = item.img_tablero
                localStorage.img_banner = item.img_banner
                localStorage.id_evento = item.id
                localStorage.nombrenumerosbingo = item.nombrenumerosbingo
                localStorage.letrasbingo = item.letrasbingo
                let url = location.href.replace("eventos", "tablero");
                window.location.href = url
            },
            getJugadores(){
                let me = this
                fetch('/getJugadores/'+me.id_evento)
                .then(response => response.json())
                .then(data => {
                    me.jugadores = data.api_crea_jugador.response.response
                    me.dialogListaJugadores = true
                })
            },
            formularioEditJugador(item){
                let me = this
                me.limpiarFormJugador()
                
                me.id_jugador = item.id
                me.nombres_jugador = item.Nombres
                me.telefono_jugador = item.Telefono
                me.correo_jugador = item.Correo
                me.ciudad_jugador = item.Ciudad
                me.num_cartillas_jugador = item.Numerocartillas

                me.dialogAgregarJugadores = true
            },
            limpiarFormJugador(){
                let me = this
                me.nombres_jugador = ''
                me.telefono_jugador = ''
                me.correo_jugador = ''
                me.ciudad_jugador = ''
                me.num_cartillas_jugador = ''
            },
            guardarJugador(){
                let me = this

                if( me.nombres_jugador === '' || me.telefono_jugador === '' || me.correo_jugador === '' || me.ciudad_jugador === '' || me.num_cartillas_jugador === '' || me.id_evento === '' ){
                    
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Complete todos los campos antes de guardar'
                    }else{
                        me.text_snackbar = 'Complete all fields before saving'
                    }
                    me.snackbar = true
                    return
                }

                let re=/^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
                if(!re.exec(me.correo_jugador)){
                    
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Ingrese un correo electrónico válido'
                    }else{
                        me.text_snackbar = 'Enter a valid email address.'
                    }
                    me.snackbar = true
                    return
                }

                me.overlay = !me.overlay;
                let token = $('meta[name="csrf-token"]').attr('content')
                let formData = '_token='+token+'&nombres_jugador='+me.nombres_jugador+'&telefono_jugador='+me.telefono_jugador+'&correo_jugador='+me.correo_jugador+'&ciudad_jugador='+me.ciudad_jugador+'&num_cartillas_jugador='+me.num_cartillas_jugador+'&id_evento='+me.id_evento

                let url = '/guardarJugador';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data){
                        me.getJugadores(me.id_evento)
                        me.enviar_invitacion()
                    }
                });
            },
            editarJugador(){
                let me = this

                if( me.nombres_jugador === '' || me.telefono_jugador === '' || me.correo_jugador === '' || me.ciudad_jugador === '' || me.num_cartillas_jugador === '' || me.id_evento === '' ){
                    
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Complete todos los campos antes de guardar'
                    }else{
                        me.text_snackbar = 'Complete all fields before saving'
                    }
                    me.snackbar = true
                    return
                }

                let re=/^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
                if(!re.exec(me.correo_jugador)){
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Ingrese un correo electrónico válido'
                    }else{
                        me.text_snackbar = 'Enter a valid email address.'
                    }
                    me.snackbar = true
                    return
                }
                
                me.overlay = !me.overlay;
                let token = $('meta[name="csrf-token"]').attr('content')
                let formData = '_token='+token+'&id_jugador='+me.id_jugador+'&nombres_jugador='+me.nombres_jugador+'&telefono_jugador='+me.telefono_jugador+'&correo_jugador='+me.correo_jugador+'&ciudad_jugador='+me.ciudad_jugador+'&num_cartillas_jugador='+me.num_cartillas_jugador+'&id_evento='+me.id_evento

                let url = '/editarJugador';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data){
                        me.getJugadores(me.id_evento)
                        me.enviar_invitacion()
                    }
                });
            },
            enviar_invitacion(){
                let me = this
                let token = $('meta[name="csrf-token"]').attr('content')
                let formData = '_token='+token+'&id_evento='+me.id_evento+'&id_jugador='+me.id_jugador+'&correo_jugador='+me.correo_jugador+'&nombres_jugador='+me.nombres_jugador+'&num_cartillas_jugador='+me.num_cartillas_jugador+'&Nombreevento='+me.data_evento.Nombreevento+'&letrasbingo='+me.data_evento.letrasbingo+'&nombrenumerosbingo='+me.data_evento.nombrenumerosbingo+'&Fecha='+me.data_evento.Fecha+'&Hora='+me.data_evento.Hora

                let url = '/enviar_invitacion';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data){
                        
                        if(localStorage.idioma_bingo === 'es'){
                            me.text_snackbar = 'Datos del jugador guardados correctamente. CORREO DE CONFIRMACIÓN ENVIADO'
                        }else{
                            me.text_snackbar = 'Player data saved correctly. CONFIRMATION MAIL SENT'
                        }
                        me.snackbar = true
                        me.dialogAgregarJugadores = false
                        me.overlay = false
                    }
                });

            },
            copiarLink(item){
                let me = this
                let link = 'https://www.bingofun.ml/cartilla_jugador?id_evento=' + me.id_evento + '*' + item.id

                try{
                    navigator.clipboard.writeText(link);
                    
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Link de las cartillas de este jugador copiado en el portapapeles'
                    }else{
                        me.text_snackbar = 'Link of the cards of this player copied to the clipboard'
                    }
                    me.snackbar = true
                }
                catch (err) {
                    alert('Error al copiar link del juego')
                }

            }
        },
    }
</script>

<style>
/* fieldset{
    border-color: white !important;
}
input{

    color: white !important;
} 

v-app{
    style="background-color: #1C1421; color: white;"
}*/
</style>
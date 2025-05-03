<template>
    <div class="p-3">
        
    <v-app>
        <v-dialog v-model="dialogJugador" persistent max-width="600px" >
            <v-card>
                <v-toolbar color="teal" dark >Jugador</v-toolbar>
                <v-card-text>
                <v-container>
                    <v-row class="pt-5">
                    <v-col cols="12" sm="6" md="6">
                        <v-text-field label="Nombre cliente" v-model="nombre_cliente" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field label="Documento" v-model="documento" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-select v-model="estado_cliente" :items="estados" item-text="label" item-value="id" label="Estado"  hide-details dense outlined ></v-select>
                    </v-col>

                    <v-col cols="12" sm="12" md="12" class="mb-0" >
                        <v-btn color="indigo" dark dense outlined @click="dialogJugador = false" > Cerrar </v-btn>
                        <v-btn color="teal" v-if="editarActivo===false" dark dense @click="guardarJugador(); dialogJugador = false" > Guardar </v-btn>
                        <v-btn color="primary" v-else dark dense @click="editarJugador(); dialogJugador = false" > Editar </v-btn>
                    </v-col>
                    </v-row>

                    
                </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>


        <!-- LISTADO JUGADORES -->
        <v-card-title>
            <v-btn class="mx-2" color="teal" dark small @click="limpiarFormulario(); dialogJugador = true">
            Crear jugador <v-icon dark>mdi-plus</v-icon>
        </v-btn>

        <v-col cols="12" sm="4" md="4" class="ml-auto" >
            <v-text-field v-model="search" append-icon="mdi-magnify" label="Buscar" outlined dense hide-details ></v-text-field>
        </v-col>
        </v-card-title>
        <v-layout v-if="jugadores != 0" v-resize="onResize" column>
        <v-data-table :headers="headers" :items="jugadores" :search="search" :class="{mobile: isMobile}">
        <template v-slot:item="row">
            <tr v-if="!isMobile">
                <td>ID: {{row.item.id}} - {{row.item.nombre}}</td>
                <td>{{row.item.identificacion}}</td>
                <td>{{row.item.estado}}</td>
                <td>
                    <v-avatar color="indigo"> <v-icon dark> mdi-account-circle </v-icon> </v-avatar>
                </td>
                <td>
                    <v-btn class="mx-2" fab dark x-small style="background-color: #512CD7;" @click="editarActivo=true; id_jugador = row.item.id; dialogJugador=true; editarFormulario(row.item)"> <v-icon dark>mdi-pencil</v-icon> </v-btn>

                    <v-btn class="mx-2" fab dark x-small style="background-color: #F1083D;" @click="eliminarJugador(row.item, row.index)"> <v-icon dark>mdi-minus</v-icon> </v-btn>
                </td>
            </tr>

            <tr v-else>
                  <td>
                    <ul class="flex-content">
                      <li class="flex-item" data-label="Jugador">{{ row.item.nombre }}</li>
                      <li class="flex-item" data-label="Nombreevento">{{ row.item.identificacion }}</li>
                      <li class="flex-item" data-label="Date">{{ row.item.estado }}</li>
                    </ul>
                  </td>
                  <td>
                    <v-btn class="mx-2" fab dark x-small style="background-color: #512CD7;" @click="editarActivo=true; id_evento = row.item.id; dialogJugador=true; editarFormulario(row.item)"> <v-icon dark>mdi-pencil</v-icon> </v-btn>

                    <v-btn class="mx-2" fab dark x-small style="background-color: #F1083D;" @click="eliminarJugador(row.item, row.index)"> <v-icon dark>mdi-minus</v-icon> </v-btn>
                </td>
            </tr>

        </template>

        <v-alert slot="no-results" :value="true" color="error" icon="warning">
                No se encontraron resultados para "{{ search }}".
              </v-alert>

        </v-data-table>
        </v-layout>

        <div v-else class="mt-8 text-center"><h5>No existen jugadores creados</h5></div>

    </v-app>
    </div>

</template>

<script>
    export default {
        data(){
            return {
                combos: [],
                jugadores: [],
                search: '',
                selected: [],
                search: '',
                isMobile: false,
                headers: [
                    { text: 'Nombre', value: '' },
                    { text: 'Identificación', value: '' },
                    { text: 'Estado', value: '' },
                    { text: 'Imagen', value: '' },
                    { text: 'Acciones', value: '' },
                ],
                dialogJugador: false,
                date: new Date().toISOString().substr(0, 10),
                jugador: '',
                nombre_evento: '',
                menu: '',
                time: null,
                menut: false,
                estado: '',
                estado_evento: '',
                cant_num_bingo: '',
                estados: [],
                numerosbingo: [],
                palabra: '',
                editarActivo: false,
                id_jugador: '',
                nombre_jugador: '',
                documento: '',
                estado_jugador: '',
            }
        },
        created(){
            
        },
        mounted() {
            this.getCombos()
            this.getJugadores()
        },
        methods: {
            onResize() {
            /* if (window.innerWidth < 769)
                this.isMobile = true;
            else
                this.isMobile = false; */
            },
            getCombos(){
                let me = this
                
                fetch('https://www.alimec.app:8181/bingo/api_sel_tablasgen?tabla=')
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
                })
                
            },
            getJugadores(){
                let me = this
                fetch('https://www.alimec.app:8181/bingo/api_crea_jugador?tabla=JUGADORES&tipo=T&usr=1&ip=10.10.10.1&id=&datos=')
                .then(response => response.json())
                .then(data => me.jugadores = data.api_crea_jugador.response);
            },
            guardarJugador(){
                let me = this
                fetch('https://www.alimec.app:8181/bingo/api_crea_cliente?tabla=CLIENTES&tipo=A&usr=1&ip=10.10.10.1&id=&datos= {"nombre":"'+me.nombre_cliente+'","identificacion":"'+me.documento+'","estado":"'+me.estado_cliente+'","imagen":"img.jpg"}')
                .then(response => response.json())
                .then(data => {
                    me.getJugadores()
                });

            },
            limpiarFormulario(){
                let me = this
                me.nombre_cliente = ''; me.documento = ''; me.estado_cliente = '';
            },
            editarFormulario(item){
                let me = this
                me.limpiarFormulario()
                me.nombre_cliente = item.nombre
                me.documento = item.identificacion
                me.estado_cliente = item.codestado
            },
            editarJugador(){
                let me = this
                fetch('https://www.alimec.app:8181/bingo/api_crea_cliente?tabla=CLIENTES&tipo=M&usr=1&ip=10.10.10.1&id='+me.id_jugador+'&datos= {"nombre":"'+me.nombre_cliente+'","identificacion":"'+me.documento+'","estado":"'+me.estado_cliente+'","imagen":"img.jpg"}')
                .then(response => response.json())
                .then(data => {
                    me.getJugadores()
                });
            },
        },
    }
</script>

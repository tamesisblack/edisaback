<template>
    <div align="right">

    <Language />

    <div align="left">
    <v-app>
        <v-snackbar v-model="snackbar" top color="indigo" :timeout="timeout" >
            {{ text_snackbar }}
            <template v-slot:action="{ attrs }">
                <v-btn color="blue" text v-bind="attrs" @click="snackbar = false" > Close </v-btn>
            </template>
        </v-snackbar>
        <v-dialog v-model="dialogCliente" max-width="600px" >
            <v-card>
                <v-toolbar color="teal" dark > {{$t('Client')}} </v-toolbar>
                <v-card-text>
                <v-container>
                    <v-row class="pt-5">
                    <v-col cols="12" sm="6" md="6">
                        <v-text-field :label="$t('Client_name')" v-model="nombre_cliente" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-text-field :label="$t('Document')" v-model="documento" hide-details dense outlined></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="6" >
                        <v-select v-model="estado_cliente" :items="estados" item-text="label" item-value="id" :label="$t('Status')"  hide-details dense outlined ></v-select>
                    </v-col>

                    <v-col cols="12" sm="12" md="12" class="mb-0" >
                        <v-btn color="indigo" dark dense outlined @click="dialogCliente = false" > {{$t('Close')}} </v-btn>
                        <v-btn color="teal" v-if="editarActivo===false" dark dense @click="guardarCliente();" > {{$t('Save')}} </v-btn>
                        <v-btn color="primary" v-else dark dense @click="editarCliente();" > {{$t('Edit')}} </v-btn>
                    </v-col>
                    </v-row>

                    
                </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>


        <!-- LISTADO CLIENTES -->
        <v-card-title>
            <v-btn class="mx-2" color="teal" dark small @click="limpiarFormulario(); dialogCliente = true">
            {{$t('create_client')}} <v-icon dark>mdi-plus</v-icon>
        </v-btn>

  
        </v-card-title>
        <v-layout v-if="clientes != 0" v-resize="onResize" column>
        <v-data-table :headers="headers" :items="clientes" :class="{mobile: isMobile}">
        <template v-slot:item="row">
            <tr>
                <td>ID: {{row.item.id}} - {{row.item.nombre}}</td>
                <td>{{row.item.identificacion}}</td>
                <td>{{row.item.estado}}</td>
                <td>
                    <v-avatar color="indigo"> <v-icon dark> mdi-account-circle </v-icon> </v-avatar>
                </td>
                <td>
                    <v-btn class="mx-2" fab dark x-small color="teal" @click="editarActivo=true; id_cliente = row.item.id; dialogCliente=true; editarFormulario(row.item)"> <v-icon dark>mdi-pencil</v-icon> </v-btn>

                    <!-- <v-btn class="mx-2" fab dark x-small style="background-color: #F1083D;" @click="eliminarCliente(row.item, row.index)"> <v-icon dark>mdi-minus</v-icon> </v-btn> -->
                </td>
            </tr>

        </template>

        <v-alert slot="no-results" :value="true" color="error" icon="warning">
                {{$t('No_results')}} "{{ search }}".
              </v-alert>

        </v-data-table>
        </v-layout>

        <div v-else class="mt-8 text-center"><h5> {{$t('No_clients')}} </h5></div>

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
                combos: [],
                clientes: [],
                search: '',
                selected: [],
                search: '',
                isMobile: false,
                headers: [
                    { text: 'Name', value: '' },
                    { text: 'Identification', value: '' },
                    { text: 'Status', value: '' },
                    { text: 'Image', value: '' },
                    { text: 'Actions', value: '' },
                ],
                dialogCliente: false,
                date: new Date().toISOString().substr(0, 10),
                cliente: '',
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
                id_evento: '',
                nombre_cliente: '',
                documento: '',
                estado_cliente: '',
                snackbar: false,
                text_snackbar: '',
                timeout: 2000,
            }
        },
        created(){
            
        },
        mounted() {
            this.getCombos()
            this.getClientes()
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
                
                fetch('/getCombos')
                .then(response => response.json())
                .then( data => {
                    let t_estados = data.api_sel_tablasgen.response

                    //cargar vector estados con formato para v-select
                    let estados = (t_estados.estados.split('²'))
                    let estado_split = []
                    for( let i=0; i<estados.length; i++ ){
                        estado_split = estados[i].split(',')
                        me.estados.push({id: estado_split[0], label: estado_split[1]})
                    }
                })

            },
            getClientes(){
                let me = this
                fetch('/getClientes')
                .then(response => response.json())
                .then(data => me.clientes = data.api_crea_cliente.response.response);
            },
            guardarCliente(){
                let me = this

                if( me.nombre_cliente === '' || me.documento === '' || me.estado_cliente === '' ){
                    
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Complete todos los campos antes de guardar'
                    }else{
                        me.text_snackbar = 'Complete all fields before saving.'
                    }
                    me.snackbar = true
                    return
                }
                let token = $('meta[name="csrf-token"]').attr('content')
                let formData = '_token='+token+'&nombre_cliente='+me.nombre_cliente+'&documento='+me.documento+'&estado_cliente='+me.estado_cliente

                let url = '/guardarCliente';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data){
                        me.dialogCliente = false
                        
                        if(localStorage.idioma_bingo === 'es'){
                            me.text_snackbar = 'Cliente guardado correctamente'
                        }else{
                            me.text_snackbar = 'Client successfully saved'
                        }
                        me.snackbar = true
                        me.getClientes()
                    }
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
            editarCliente(){
                let me = this

                if( me.nombre_cliente === '' || me.documento === '' || me.estado_cliente === '' ){
                    
                    if(localStorage.idioma_bingo === 'es'){
                        me.text_snackbar = 'Complete todos los campos antes de guardar'
                    }else{
                        me.text_snackbar = 'Complete all fields before saving.'
                    }
                    me.snackbar = true
                    return
                }

                let token = $('meta[name="csrf-token"]').attr('content')
                let formData = '_token='+token+'&id_cliente='+me.id_cliente+'&nombre_cliente='+me.nombre_cliente+'&documento='+me.documento+'&estado_cliente='+me.estado_cliente

                let url = '/editarCliente';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data){
                        me.dialogCliente = false
                        
                        if(localStorage.idioma_bingo === 'es'){
                            me.text_snackbar = 'Cliente guardado correctamente'
                        }else{
                            me.text_snackbar = 'Client successfully saved'
                        }
                        me.snackbar = true
                        me.getClientes()
                    }
                });
            },
        },
    }
</script>

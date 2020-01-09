<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><strong><a style="color:#FFFFFF;" href="/">Home</a></strong></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card scroll-box">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Inventario
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Registrar inventario
                        </button>
                        <!---->
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <select class="form-control" v-model="sucursal_id"  v-if="rolId == 1">
                                        <option value="">Seleccione</option>
                                        <option v-for="sucursal in arraySucursales" :key="sucursal.id" :value="sucursal.id" v-text="sucursal.pv + ' | ' + sucursal.cadena "></option>
                                    </select>
                                </div>
                                <div class="input-group" v-if="rolId == 1">
                                    <button type="submit" @click="listarInventario(1,sucursal_id)" class="btn btn-primary"><i class="fa fa-refresh"></i> Actualizar</button>
                                    <a v-if="rolId == 1" :href="'/excel/inventario?buscar=' + sucursal_id"  class="btn btn-success"><i class="fa fa-file-text"></i> Excel</a>
                                    <a v-if="rolId == 1" :href="'/excel/wos?buscar=' + sucursal_id"  class="btn btn-success"><i class="fa fa-file-text"></i> Excel Wos</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive"> 
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Sucursal</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Total Premium</th>
                                        <th>Total Smart</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="inventario in arrayInventario" :key="inventario.id">
                                        <td v-text="inventario.pv + ' | '+inventario.cadena"></td>
                                        <td v-text="inventario.fecha"></td>
                                        <td v-text="inventario.total"></td>
                                        <td v-text="inventario.total_premium"></td>
                                        <td v-text="inventario.total_smart"></td>
                                    </tr>                               
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home3" role="tab" aria-controls="home">
                                <i class="icon-calculator"></i> Inventario</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="home3" role="tabpanel">
                                <div class="table-responsive"> 
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Modelo</th>
                                                <th>Categoria</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                                <td v-text="detalle.modelo"></td>
                                                <td>
                                                    <span v-if="detalle.tipo == 0" class="badge2 badge-success"> Smart </span>
                                                    <span v-else class="badge2 badge-dark"> Premium </span>
                                                </td>
                                                <td v-text="detalle.cantidad"></td>
                                            </tr>                               
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal animated fadeIn" tabindex="-1" :class="{'mostrar': modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label" for="text-input">Fecha</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-6">
                                        <input readonly type="date" v-model="fecha" class="form-control" placeholder="Fecha de reporte">
                                    </div>
                                </div>
                                <div v-if="arrayEquipos.length">
                                    <div class="form-group row"  v-for="equipo in arrayEquipos" :key="equipo.id">
                                        <label class="col-md-2 form-control-label" for="text-input" v-text="equipo.modelo"></label>
                                        <div class="col-md-3">
                                            <input type="text" pattern="\d*" v-on:keypress="isNumber($event)" v-model="equipo.cant" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Div para mostrar los errores que mande validerNotaria -->
                                <div v-show="errorVenta" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjVenta" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarVenta()">Guardar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->

            
        </main>
</template>

<!-- ************************************************************************************************************************************  -->
<!-- *********************************************************** CODIGO JAVASCRIPT *************************************************************************  -->
<!-- ************************************************************************************************************************************  -->

<script>
    export default {
        props:{
            rolId:{type: String}
        },
        data(){
            return{
                proceso:false,
                id:0,
                fecha:'',
                total:0,
                total_premium: 0,
                total_smart : 0,
                arrayInventario  : [],
                arrayEquipos : [],
                arrayDetalle : [],
                arraySucursales : [],
                sucursal_id : '',
                modal : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorVenta : 0,
                errorMostrarMsjVenta : [],
                offset : 3,
                buscar : '',
            }
        },
        computed:{
            
        },
        methods : {
            /**Metodo para mostrar los registros */
            listarInventario(page, buscar){
                let me = this;
                var url = '/inventarios?page=' + page + '&buscar=' + buscar;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayInventario = respuesta.inventarios;
                    me.fecha = respuesta.hoy;
                    me.indexDetalle(me.arrayInventario[0].id);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            /**Metodo para registrar  */
            registrarVenta(){
                if(this.validarVenta()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }
                let me = this;
                //Con axios se llama el metodo store de FraccionaminetoController
                axios.post('/inventarios/registrar',{
                    'fecha': this.fecha,
                    'data':this.arrayEquipos,
                }).then(function (response){
                    me.listarInventario(1,''); //se enlistan nuevamente los registros
                    me.cerrarModal();
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Inventario agregado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },
            getEquipos(){
                let me = this;
                me.arrayEquipos=[];
                var url = '/equipos/activos';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayEquipos = respuesta.equipos;
                })
                .catch(function (error) {
                    console.log(error);
                });
              
            },
            selectSucursal(){
                let me = this;
                me.arraySucursales=[];
                var url = '/pv/select';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arraySucursales = respuesta.sucursales;
                })
                .catch(function (error) {
                    console.log(error);
                });
              
            },

            indexDetalle(id){
                let me = this;
                me.arrayDetalle=[];
                var url = '/inventarios/indexDetalle?id=' + id;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayDetalle = respuesta.ventas;
                })
                .catch(function (error) {
                    console.log(error);
                });
              
            },
            formatNumber(value) {
                let val = (value/1).toFixed(2)
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },
            limpiarBusqueda(){
                let me=this;
                me.buscar= "";
            },
            validarVenta(){
                this.errorVenta=0;
                this.errorMostrarMsjVenta=[];

                if(!this.fecha) //Si la variable Fraccionamiento esta vacia
                    this.errorMostrarMsjVenta.push("Seleccionar fecha de reporte");

                if(this.errorMostrarMsjVenta.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorVenta = 1;

                return this.errorVenta;
            },
            cerrarModal(){
                this.modal = 0;
                this.modal2 = 0;
                this.tituloModal = '';
                this.id = '';
                this.errorVenta = 0;
                this.errorMostrarMsjVenta = [];
                this.arrayEquipos = [];

            },
            
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(accion, data =[]){
                switch(accion){
                    case 'registrar':
                    {
                        this.getEquipos();
                        this.modal = 1;
                        this.tituloModal = 'Registrar Inventario';
                        this.tipoAccion = 1;
                        this.tipo = 0;
                        break;
                    }
                }
            }
        },
        mounted() {
            this.listarInventario(1,this.buscar);
            this.selectSucursal();
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: fixed !important;
        background-color: #3c29297a !important;
        overflow-y: auto;
    }
    .div-error{
        display:flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
    .badge2 {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 90%;
        font-weight: bold;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
    }
</style>

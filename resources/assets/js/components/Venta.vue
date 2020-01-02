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
                        <i class="fa fa-align-justify"></i> Ventas
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Registrar ventas del dia
                        </button>
                        <!---->
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="date"  v-model="buscar" @keyup.enter="listarVentas(1,buscar)" class="form-control" placeholder="Fecha a buscar">
                                    <button type="submit" @click="listarVentas(1,buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive"> 
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Sucursal</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Total Premium</th>
                                        <th>Total Smart</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="venta in arrayVentas" :key="venta.id">
                                        <td>
                                            <button type="button" @click="abrirModal('ver',venta)" class="btn btn-primary btn-sm">
                                                <i class="icon-eye"></i>
                                            </button>
                                        </td>
                                        <td v-text="venta.pv + ' | '+venta.cadena"></td>
                                        <td v-text="venta.fecha"></td>
                                        <td v-text="'$'+formatNumber(venta.total)"></td>
                                        <td v-text="'$'+formatNumber(venta.total_premium)"></td>
                                        <td v-text="'$'+formatNumber(venta.total_smart)"></td>
                                    </tr>                               
                                </tbody>
                            </table>
                        </div>
                        <nav>
                            <!--Botones de paginacion -->
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar)">Sig</a>
                                </li>
                            </ul>
                        </nav>
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
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label" for="text-input">Promoción</label>
                                    <div class="col-md-7">
                                        <textarea rows="3" cols="30" v-model="promocion" class="form-control" placeholder="¿Qué promoción es la que más te esta pegando y que vigencia tiene?"></textarea>
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

            <!--Inicio del modal agregar/actualizar-->
            <div class="modal animated fadeIn" tabindex="-1" :class="{'mostrar': modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
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
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label" for="text-input">Promoción</label>
                                    <div class="col-md-7">
                                        <textarea rows="3" readonly cols="30" v-model="promocion" class="form-control" placeholder="¿Qué promoción es la que más te esta pegando y que vigencia tiene?"></textarea>
                                    </div>
                                </div>

                                 <div class="form-group row">
                                    <label class="col-md-2 form-control-label" for="text-input">Descripción</label>
                                    <div class="col-md-8">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Modelo</th>
                                                    <th>Categoria</th>
                                                    <th>Cantidad</th>
                                                    <th>Total $</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="venta in arrayDetalle" :key="venta.id">
                                                    <td v-text="venta.modelo"></td>
                                                    <td>
                                                        <span v-if="venta.tipo == 0" class="badge2 badge-success"> Smart </span>
                                                        <span v-else class="badge2 badge-dark"> Premium </span>
                                                    </td>
                                                    <td v-text="venta.cantidad"></td>
                                                    <td v-text="'$'+formatNumber(venta.total)"></td>
                                                </tr>                               
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label" for="text-input">Ventas Smart </label>
                                    <div class="col-md-7">
                                        <h6 v-text="'$'+formatNumber(total_smart)"></h6>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label" for="text-input">Ventas Premium </label>
                                    <div class="col-md-7">
                                        <h6 v-text="'$'+formatNumber(total_premium)"></h6>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <strong><label class="col-md-2 form-control-label" for="text-input">Ventas Totales </label></strong>
                                    <div class="col-md-6">
                                        <strong><h6 v-text="'$'+formatNumber(total)"></h6></strong>
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
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
        data(){
            return{
                proceso:false,
                id:0,
                fecha:'',
                promocion:'',
                total:0,
                total_premium: 0,
                total_smart : 0,
                arrayVentas  : [],
                arrayEquipos : [],
                arrayDetalle : [],
                modal : 0,
                modal2 : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorVenta : 0,
                errorMostrarMsjVenta : [],
                pagination : {
                    'total' : 0,         
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                buscar : '',
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber:function(){
                if(!this.pagination.to){
                    return [];
                }

                var from = this.pagination.current_page - this.offset;
                if(from < 1){
                    from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }

                var pagesArray = [];
                while(from <= to){
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        },
        methods : {
            /**Metodo para mostrar los registros */
            listarVentas(page, buscar){
                let me = this;
                var url = '/ventas?page=' + page + '&buscar=' + buscar;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayVentas = respuesta.ventas.data;
                    me.pagination = respuesta.pagination;
                    me.fecha = respuesta.hoy;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            
            cambiarPagina(page, buscar){
                let me = this;
                //Actualiza la pagina actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esta pagina
                me.listarVentas(page,buscar);
            },
            /**Metodo para registrar  */
            registrarVenta(){
                if(this.validarVenta()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }
                let me = this;
                //Con axios se llama el metodo store de FraccionaminetoController
                axios.post('/ventas/registrar',{
                    'fecha': this.fecha,
                    'promocion': this.promocion,
                    'data':this.arrayEquipos,
                }).then(function (response){
                    me.listarVentas(1,''); //se enlistan nuevamente los registros
                    me.cerrarModal();
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Ventas agregadas correctamente',
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
                me.arraySucursales=[];
                var url = '/equipos/activos';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayEquipos = respuesta.equipos;
                })
                .catch(function (error) {
                    console.log(error);
                });
              
            },

            indexDetalle(id){
                let me = this;
                me.arraySucursales=[];
                var url = '/ventas/indexDetalle?id=' + id;
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

                if(!this.promocion) //Si la variable Fraccionamiento esta vacia
                    this.errorMostrarMsjVenta.push("Escribir la promocion que mas te ha funcionado");

                if(this.errorMostrarMsjVenta.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorVenta = 1;

                return this.errorVenta;
            },
            cerrarModal(){
                this.modal = 0;
                this.modal2 = 0;
                this.tituloModal = '';
                this.promocion = '';
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
                        this.tituloModal = 'Registrar Ventas';
                        this.prmocion = '';
                        this.tipoAccion = 1;
                        this.tipo = 0;
                        break;
                    }
                    case 'ver':{
                        this.indexDetalle(data['id']);
                        this.modal2 = 1;
                        this.tituloModal = 'Ventas en ' + data['pv'] + ' | ' +data['cadena'];
                        this.fecha = data['fecha'];
                        this.promocion = data['promocion'];
                        this.total = data['total'];
                        this.total_premium = data['total_premium'];
                        this.total_smart = data['total_smart'];
                        break;
                    }
                }
            }
        },
        mounted() {
            this.listarVentas(1,this.buscar);
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

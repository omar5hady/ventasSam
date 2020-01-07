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
                        <i class="fa fa-align-justify"></i> Cuota
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <!---->
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control" @keyup.enter="listarCuota(1,buscar)" v-model="buscar">
                                        <option value="">Mes</option>
                                        <option value=1>Enero</option>
                                        <option value=2>Febrero</option>
                                        <option value=3>Marzo</option>
                                        <option value=4>Abril</option>
                                        <option value=5>Mayo</option>
                                        <option value=6>Junio</option>
                                        <option value=7>Julio</option>
                                        <option value=8>Agosto</option>
                                        <option value=9>Septiembre</option>
                                        <option value=10>Octubre</option>
                                        <option value=11>Noviembre</option>
                                        <option value=12>Diciembre</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <select class="form-control" v-model="vendedor_id"  v-if="rolId == 1">
                                        <option value="">Seleccione</option>
                                        <option v-for="vendedor in arrayVendedores" :key="vendedor.id" :value="vendedor.id" v-text="vendedor.nombre + ' ' + vendedor.apellidos "></option>
                                    </select>
                                    <button type="submit" @click="listarCuota(1,buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive"> 
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th>Smart</th>
                                        <th>Qty Smart</th>
                                        <th>Premium</th>
                                        <th>Qty Premium</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="cuota in arrayCuota" :key="cuota.id">
                                        <template>
                                            <td v-if="cuota.month == 1">Enero</td>
                                            <td v-if="cuota.month == 2">Febrero</td>
                                            <td v-if="cuota.month == 3">Marzo</td>
                                            <td v-if="cuota.month == 4">Abril</td>
                                            <td v-if="cuota.month == 5">Mayo</td>
                                            <td v-if="cuota.month == 6">Junio</td>
                                            <td v-if="cuota.month == 7">Julio</td>
                                            <td v-if="cuota.month == 8">Agosto</td>
                                            <td v-if="cuota.month == 9">Septiembre</td>
                                            <td v-if="cuota.month == 10">Octubre</td>
                                            <td v-if="cuota.month == 11">Noviembre</td>
                                            <td v-if="cuota.month == 12">Diciembre</td>
                                        </template>
                                        <td v-text="'$ '+formatNumber(cuota.smart)"></td>
                                        <td>
                                            <span class="badge2 badge-success" v-text="cuota.qty_smart"></span>
                                        </td>
                                        <td v-text="'$ '+formatNumber(cuota.premium)"></td>
                                        <td>
                                            <span class="badge2 badge-dark" v-text="cuota.qty_premium"></span>
                                        </td>
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
                                    <label class="col-md-3 form-control-label" for="text-input">Cuota premium</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-6">
                                        <input type="text" v-model="premium" class="form-control" placeholder="Cuota premium">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Qty premium</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-6">
                                        <input type="text" v-model="qty_premium" class="form-control" placeholder="Piezas premium">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Cuota smart</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-6">
                                        <input type="text" v-model="smart" class="form-control" placeholder="Cuota smart">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Qty smart</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-6">
                                        <input type="text" v-model="qty_smart" class="form-control" placeholder="Piezas smart">
                                    </div>
                                </div>
                                
                                <!-- Div para mostrar los errores que mande validerNotaria -->
                                <div v-show="errorCuota" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjCuota" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarCuota()">Guardar</button>
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
                premium : 0,
                smart: 0,
                qty_premium:0,
                qty_smart : 0,
                vendedor_id:'',
                arrayCuota : [],
                arrayVendedores : [],
                modal : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorCuota : 0,
                errorMostrarMsjCuota : [],
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
            listarCuota(page, buscar){
                let me = this;
                var url = '/cuota?page=' + page + '&month=' + buscar  + '&user_id=' + me.vendedor_id;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayCuota = respuesta.cuotas.data;
                    me.mes = respuesta.mes;
                    me.pagination = respuesta.pagination;
                })
                .catch(function (error) {
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
            formatNumber(value) {
                let val = (value/1).toFixed(2)
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },
            cambiarPagina(page, buscar){
                let me = this;
                //Actualiza la pagina actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esta pagina
                me.listarCuota(page,buscar);
            },
            selectVendedor(){
                let me = this;
                me.arrayVendedores=[];
                var url = '/selectVendedor';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayVendedores = respuesta.personas;
                })
                .catch(function (error) {
                    console.log(error);
                });
              
            },
            /**Metodo para registrar  */
            registrarCuota(){
                if(this.proceso==true){
                    return;
                }
                if(this.validarCuota()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                this.proceso=true;
                let me = this;
                //Con axios se llama el metodo store de FraccionaminetoController
                axios.post('/cuota/registrar',{
                    'premium': this.premium,
                    'smart': this.smart,
                    'qty_premium' :  this.qty_premium,
                    'qty_smart' :  this.qty_smart,
                }).then(function (response){
                    me.proceso=false;
                    me.cerrarModal(); //al guardar el registro se cierra el modal
                    me.listarCuota(1,''); //se enlistan nuevamente los registros
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Cuota agregada correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            limpiarBusqueda(){
                let me=this;
                me.buscar= "";
            },
            validarCuota(){
                this.errorCuota=0;
                this.errorMostrarMsjCuota=[];

                if(!this.premium || this.premium == 0) //Si la variable Fraccionamiento esta vacia
                    this.errorMostrarMsjCuota.push("Ingresar la cuota para equipos premium $.");

                    if(!this.smart || this.smart == 0) //Si la variable Fraccionamiento esta vacia
                    this.errorMostrarMsjCuota.push("Ingresar la cuota para equipos smart $.");

                if(this.errorMostrarMsjCuota.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorCuota = 1;

                return this.errorCuota;
            },
            cerrarModal(){
                this.modal = 0;
                this.tituloModal = '';
                this.premium = 0;
                this.smart = 0;
                this.qty_premium = 0;
                this.qty_smart = 0;
                this.id = '';
                this.errorCuota = 0;
                this.errorMostrarMsjCuota = [];

            },
            
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(accion, data =[]){
                switch(accion){
                    case 'registrar':
                    {
                        this.modal = 1;
                        this.tituloModal = 'Registrar Cuota';
                        this.premium = 0;
                        this.qty_premium = 0;
                        this.smart = 0;
                        this.qty_smart = 0;
                        this.tipoAccion = 1;
                        this.tipo = 0;
                        break;
                    }
                }
            }
        },
        mounted() {
            this.listarCuota(1,this.buscar);
            this.selectVendedor();
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
    }
    .div-error{
        display:flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
</style>

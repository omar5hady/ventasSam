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
                        <i class="fa fa-align-justify"></i> Cortes
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Registrar corte del dia
                        </button>
                        <!---->
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="date"  v-model="buscar" @keyup.enter="listarCorte(1,buscar)" class="form-control" placeholder="Fecha a buscar">
                                    <input type="date"  v-model="buscar2" @keyup.enter="listarCorte(1,buscar)" class="form-control" placeholder="Fecha a buscar">
                                </div>
                                <div class="input-group">
                                    <select class="form-control" v-model="sucursal_id"  v-if="rolId == 1">
                                        <option value="">Seleccione</option>
                                        <option v-for="sucursal in arraySucursales" :key="sucursal.id" :value="sucursal.id" v-text="sucursal.pv + ' | ' + sucursal.cadena "></option>
                                    </select>
                                    <button type="submit" @click="listarCorte(1,buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="venta in arrayCortes" :key="venta.id">
                                        <td>
                                            <button type="button" @click="abrirModal('ver',venta)" class="btn btn-primary btn-sm">
                                                <i class="icon-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" @click="eliminar(venta.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </td>
                                        <td v-text="venta.pv + ' | '+venta.cadena"></td>
                                        <td v-text="venta.fecha"></td>
                                        <td v-text="'$'+formatNumber(venta.total)"></td>
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
                                <div class="form-group row" v-if="rolId == 1">
                                    <label class="col-md-2 form-control-label" for="text-input">Sucursal</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-4">
                                        <select class="form-control" v-model="sucursal_id" v-if="rolId == 1">
                                            <option value="">Seleccione</option>
                                            <option v-for="sucursal in arraySucursales" :key="sucursal.id" :value="sucursal.id" v-text="sucursal.pv + ' | ' + sucursal.cadena "></option>
                                        </select>
                                    </div>
                                </div>
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
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarCorte()">Guardar</button>
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
                                                <tr v-for="corte in arrayDetalle" :key="corte.id">
                                                    <td v-text="corte.modelo"></td>
                                                    <td>
                                                        <span v-if="corte.tipo == 0" class="badge2 badge-success"> Smart </span>
                                                        <span v-else class="badge2 badge-dark"> Premium </span>
                                                    </td>
                                                    <td v-text="corte.cantidad"></td>
                                                    <td v-text="'$'+formatNumber(corte.total)"></td>
                                                </tr>                               
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <strong><label class="col-md-2 form-control-label" for="text-input">Corte Total </label></strong>
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
        props:{
            rolId:{type: String}
        },
        data(){
            return{
                proceso:false,
                id:0,
                fecha:'',
                total:0,
                arrayCortes  : [],
                arrayEquipos : [],
                arrayDetalle : [],
                arraySucursales : [],
                modal : 0,
                modal2 : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorVenta : 0,
                sucursal_id: '',
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
                buscar2 : '',
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
            listarCorte(page, buscar){
                let me = this;
                var url = '/cortes?page=' + page + '&buscar=' + buscar + '&buscar2=' + me.buscar2 + '&sucursal_id=' + me.sucursal_id;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayCortes = respuesta.cortes.data;
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
                me.listarCorte(page,buscar);
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
            /**Metodo para registrar  */
            registrarCorte(){
                if(this.validarCorte()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }
                let me = this;
                //Con axios se llama el metodo store de FraccionaminetoController
                axios.post('/cortes/registrar',{
                    'fecha': this.fecha,
                    'sucursal_id' : this.sucursal_id,
                    'data':this.arrayEquipos,
                }).then(function (response){
                    me.listarCorte(1,''); //se enlistan nuevamente los registros
                    me.cerrarModal();
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Corte agregado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            eliminar(id){
                //console.log(this.fraccionamiento_id);
                swal({
                title: '¿Desea eliminar?',
                text: "Esta acción no se puede revertir!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, eliminar!'
                }).then((result) => {
                if (result.value) {
                    let me = this;

                axios.delete('/corte/eliminar', 
                        {params: {'id': id}}).then(function (response){
                        swal(
                        'Borrado!',
                        'Corte borrado correctamente.',
                        'success'
                        )
                        me.listarCorte(1,'');
                    }).catch(function (error){
                        console.log(error);
                    });
                }
                })
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

            indexDetalle(id){
                let me = this;
                me.arrayDetalle=[];
                var url = '/cortes/indexDetalle?id=' + id;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayDetalle = respuesta.cortes;
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
            validarCorte(){
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
                this.sucursal_id = '';

            },
            
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(accion, data =[]){
                switch(accion){
                    case 'registrar':
                    {
                        this.getEquipos();
                        this.modal = 1;
                        this.tituloModal = 'Registrar Corte';
                        this.tipoAccion = 1;
                        this.tipo = 0;
                        this.sucursal_id = '';
                        break;
                    }
                    case 'ver':{
                        this.indexDetalle(data['id']);
                        this.modal2 = 1;
                        this.tituloModal = 'Corte en ' + data['pv'] + ' | ' +data['cadena'];
                        this.fecha = data['fecha'];
                        this.total = data['total'];
                    }
                }
            }
        },
        mounted() {
            this.listarCorte(1,this.buscar);
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

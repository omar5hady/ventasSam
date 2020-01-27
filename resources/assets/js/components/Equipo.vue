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
                        <i class="fa fa-align-justify"></i> Equipos
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
                                    <input type="text"  v-model="buscar" @keyup.enter="listarEquipos(1,buscar)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarEquipos(1,buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive"> 
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Modelo</th>
                                        <th>Precio $</th>
                                        <th>Tipo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="equipo in arrayEquipos" :key="equipo.id">
                                        <td>
                                            <button type="button" @click="abrirModal('actualizar',equipo)" class="btn btn-warning btn-sm">
                                                <i class="icon-pencil"></i>
                                            </button>
                                            <button title="Desactivar" v-if="equipo.condicion == 1" type="button" class="btn btn-danger btn-sm" @click="desactivarEquipo(equipo)">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button title="Activar" v-else type="button" class="btn btn-success btn-sm" @click="activarEquipo(equipo)">
                                                <i class="icon-check"></i>
                                            </button>
                                        </td>
                                        <td v-text="equipo.modelo"></td>
                                        <td v-text="'$'+formatNumber(equipo.precio)"></td>
                                        <template>
                                            <td v-if="equipo.tipo==0" v-text="'Smart'"></td>
                                                
                                            <td v-else v-text="'Premium'"></td>
                                        </template>
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
                                    <label class="col-md-3 form-control-label" for="text-input">Equipo</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-6">
                                        <input type="text" v-model="modelo" class="form-control" placeholder="Modelo del equipo">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Precio $</label>
                                    <div class="col-md-4">
                                        <input type="text" pattern="\d*" v-on:keypress="isNumber($event)" v-model="precio" class="form-control" placeholder="Precio del modelo">
                                    </div>
                                    <div class="col-md-4">
                                        <label v-text="'$'+formatNumber(precio)"></label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Tipo</label>
                                    <!--Criterios para el listado de busqueda -->
                                    <div class="col-md-6">
                                        <select class="form-control" v-model="tipo">
                                            <option value="0">Smart</option>
                                            <option value="1">Premium</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Div para mostrar los errores que mande validerNotaria -->
                                <div v-show="errorEquipo" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjEquipo" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarEquipo()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarEquipo()">Actualizar</button>
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
                modelo : '',
                precio: 0,
                tipo:0,
                arrayEquipos : [],
                modal : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorEquipo : 0,
                errorMostrarMsjEquipo : [],
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
            listarEquipos(page, buscar){
                let me = this;
                var url = '/equipos?page=' + page + '&buscar=' + buscar;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayEquipos = respuesta.equipos.data;
                    me.pagination = respuesta.pagination;
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
                me.listarEquipos(page,buscar);
            },
            /**Metodo para registrar  */
            registrarEquipo(){
                if(this.proceso==true){
                    return;
                }
                if(this.validarEquipo()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                this.proceso=true;
                let me = this;
                //Con axios se llama el metodo store de FraccionaminetoController
                axios.post('/equipos/registrar',{
                    'modelo': this.modelo,
                    'precio': this.precio,
                    'tipo' :  this.tipo,
                }).then(function (response){
                    me.proceso=false;
                    me.cerrarModal(); //al guardar el registro se cierra el modal
                    me.listarEquipos(1,''); //se enlistan nuevamente los registros
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Equipo agregado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            actualizarEquipo(){
              
                if(this.validarEquipo()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }
               
                let me = this;
                //Con axios se llama el metodo update de FraccionaminetoController
                axios.put('/equipos/actualizar',{
                    'modelo': this.modelo,
                    'precio': this.precio,
                    'tipo' :  this.tipo,
                    'id' : this.id
                }).then(function (response){
                    
                    me.cerrarModal();
                    me.listarEquipos(1,'');
                    //window.alert("Cambios guardados correctamente");
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Cambios guardados correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            desactivarEquipo(data =[]){
                this.id=data['id'];
                
                //console.log(this.fraccionamiento_id);
                swal({
                title: '¿Desea continuar?',
                text: "Este equipo quedara desactivado!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, desactivar!'
                }).then((result) => {
                if (result.value) {
                    let me = this;

               axios.put('/equipos/desactivar',{
                    'modelo': this.modelo,
                    'precio': this.precio,
                    'id' : this.id
                })  .then(function (response){
                        swal(
                        'Desactivado!',
                        'Equipo desactivado correctamente.',
                        'success'
                        )
                        me.listarEquipos(1,'');
                    }).catch(function (error){
                        console.log(error);
                    });
                }
                })
            },
            activarEquipo(data =[]){
                this.id=data['id'];
                
                //console.log(this.fraccionamiento_id);
                swal({
                title: '¿Desea continuar?',
                text: "Este equipo quedara activado!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, activar!'
                }).then((result) => {
                if (result.value) {
                    let me = this;

               axios.put('/equipos/activar',{
                    'modelo': this.modelo,
                    'precio': this.precio,
                    'id' : this.id
                })  .then(function (response){
                        swal(
                        'Activado!',
                        'Equipo activado correctamente.',
                        'success'
                        )
                        me.listarEquipos(1,'');
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
            formatNumber(value) {
                let val = (value/1).toFixed(2)
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },
            limpiarBusqueda(){
                let me=this;
                me.buscar= "";
            },
            validarEquipo(){
                this.errorEquipo=0;
                this.errorMostrarMsjEquipo=[];

                if(!this.modelo) //Si la variable Fraccionamiento esta vacia
                    this.errorMostrarMsjEquipo.push("El modelo del equipo no puede ir vacio.");

                    if(!this.precio) //Si la variable Fraccionamiento esta vacia
                    this.errorMostrarMsjEquipo.push("El precio no puede ir vacio.");

                if(this.errorMostrarMsjEquipo.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorEquipo = 1;

                return this.errorEquipo;
            },
            cerrarModal(){
                this.modal = 0;
                this.tituloModal = '';
                this.modelo = '';
                this.precio = 0;
                this.id = '';
                this.errorEquipo = 0;
                this.errorMostrarMsjEquipo = [];

            },
            
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(accion, data =[]){
                switch(accion){
                    case 'registrar':
                    {
                        this.modal = 1;
                        this.tituloModal = 'Registrar Equipo';
                        this.modelo ='';
                        this.precio = 0;
                        this.tipoAccion = 1;
                        this.tipo = 0;
                        break;
                    }
                    case 'actualizar':
                    {
                        //console.log(data);
                        this.modal =1;
                        this.tituloModal='Actualizar Equipo';
                        this.tipoAccion=2;
                        this.id=data['id'];
                        this.modelo=data['modelo'];
                        this.tipo = data['tipo'];
                        this.precio=data['precio'];
                        break;
                    }
                }
            }
        },
        mounted() {
            this.listarEquipos(1,this.buscar);
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

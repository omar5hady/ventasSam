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
                        <i class="fa fa-align-justify"></i> Usuarios
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('Personal','registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <!---->
                    </div>
                    
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <!--Criterios para el listado de busqueda -->
                                    <select class="form-control col-md-5" @click="selectDepartamento(),limpiarBusqueda()"  v-model="criterio">
                                        <option value="personas.nombre">Nombre</option>
                                        <option value="users.usuario">Usuario</option>
                                    </select>
                                    
                                    <input type="text" v-model="buscar" @keyup.enter="listarPersonal(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarPersonal(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Nombre</th>
                                        <th>Usuario</th>
                                        <th>Pv</th>
                                        <th>Rol</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="Personal in arrayPersonal" :key="Personal.id">
                                        <td width="10%">
                                            <button type="button" @click="abrirModal('Personal','actualizar',Personal)" class="btn btn-warning btn-sm">
                                            <i class="icon-pencil"></i>
                                            </button>
                                            <template v-if="Personal.condicion">
                                                <button type="button" @click="desactivarPersonal(Personal.id)" class="btn btn-danger btn-sm">
                                                <i class="fa fa-user-times"></i>
                                                </button>
                                            </template>
                                            <template v-else>
                                                <button type="button" @click="activarPersonal(Personal.id)" class="btn btn-success btn-sm">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </template>
                                    
                                        </td>
                                        <td v-text="Personal.nombre + ' ' + Personal.apellidos" ></td>
                                        
                                        <td v-text="Personal.usuario"></td>
                                        <td v-text="Personal.pv + ' | '+Personal.cadena "></td>
                                        <td v-text="Personal.rol"></td>
                                        <td>
                                            <span v-if = "Personal.condicion==1" class="badge badge-success">Activo</span>
                                            <span v-if = "Personal.condicion==0" class="badge badge-danger">Inactivo</span>
                                        </td>
                                        <td>
                                            <button type="button" title="Enviar aviso" @click="abrirModal('Personal','aviso',Personal)" class="btn btn-primary btn-sm">
                                                <i class="fa fa-comment-o"></i>
                                            </button>
                                        </td>
                                                        
                                    
                                    </tr>                               
                                </tbody>
                            </table>
                        </div>
                        <nav>
                            <!--Botones de paginacion -->
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Sig</a>
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

                                    <!--Criterios para el listado de busqueda -->
                                  <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Apellidos</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="apellidos" class="form-control" placeholder="Apellidos" >
                                    </div>
                                </div>
                                   <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                    <div class="col-md-9">
                                        <input type="text" maxlength="25" v-model="nombre" class="form-control" placeholder="Nombre" >
                                    </div>
                                </div>
                            
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Celular</label>
                                    <div class="col-md-5">
                                        <input type="text" pattern="\d*" maxlength="10" v-on:keypress="isNumber($event)" v-model="celular" class="form-control" placeholder="Celular" >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Usuario</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="usuario" class="form-control" placeholder="Usuario" >
                                    </div>
                                </div>

                                
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Contraseña</label>
                                    <div class="col-md-9">
                                        <input type="password" v-model="password" class="form-control" placeholder="Contraseña" >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Rol</label>
                                    <div class="col-md-6">
                                       <select class="form-control" v-model="rol_id" >
                                            <option value="0">Seleccione</option>
                                            <option value="1">Administrador</option>
                                            <option value="2">Vendedor</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">PV</label>
                                    <div class="col-md-6">
                                       <select class="form-control" v-model="sucursal_id" >
                                            <option value="0">Seleccione</option>
                                            <option v-for="sucursal in arraySucursales" :key="sucursal.id" :value="sucursal.id" v-text="sucursal.pv + ' | ' + sucursal.cadena "></option>
                                        </select>
                                    </div>
                                </div>
                               
                                <!-- Div para mostrar los errores que mande validerPersonal -->
                                <div v-show="errorPersonal" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjPersonal" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" v-if="tipoAccion==3" class="btn btn-primary" @click="registrarPersonal()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersonal()">Actualizar</button>
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
                            <h4 class="modal-title" v-text="'Aviso'"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Comentario</label>
                                <div class="col-md-9">
                                    <textarea v-model="aviso" id="" cols="50" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" class="btn btn-primary" @click="enviarAviso()">Enviar</button>
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
                usuario: '',
                password: '',
                condicion: 1,
                rol_id: 0,
                sucursal_id:0,
                nombre : '',
                apellidos : '',
                celular: 0,
                activo: 1, 
                arrayPersonal:[],
                arraySucursales:[],
                modal : 0,
                modal2 : 0,
                aviso : '',
                tituloModal : '',
                tipoAccion: 0,
                errorPersonal : 0,
                errorMostrarMsjPersonal : [],

                pagination : {
                    'total' : 0,         
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'personas.nombre', 
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
            listarPersonal(page, buscar, criterio){
                let me = this;
                var url = '/user?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayPersonal = respuesta.personas.data;
                    me.pagination = respuesta.pagination;
                    me.cerrarModal();
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

            limpiarBusqueda(){
                let me=this;
                me.buscar= "";
            },
            
            cambiarPagina(page, buscar, criterio){
                let me = this;
                //Actualiza la pagina actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esta pagina
                me.listarPersonal(page,buscar,criterio);
            },

            /**Metodo para registrar  */
            registrarPersonal(){
                if(this.validarPersonal() || this.proceso==true) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                this.proceso=true;

                let me = this;
                //Con axios se llama el metodo store de PersonalController
                axios.post('/user/registrar',{
                    'nombre': this.nombre,
                    'apellidos': this.apellidos,
                    'celular': this.celular,

                    'usuario': this.usuario,
                    'password': this.password,
                    'sucursal_id' : this.sucursal_id,
                    'rol_id' : this.rol_id,
                    
                }).then(function (response){
                    me.proceso=false;
                    me.cerrarModal(); //al guardar el registro se cierra el modal
                    me.listarPersonal(1,'','personas.nombre'); //se enlistan nuevamente los registros
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Usuario agregado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            enviarAviso(){
                let me = this;
                //Con axios se llama el metodo store de PersonalController
                axios.post('/aviso/store',{
                    'id': this.id,
                    'aviso': this.aviso,
                    
                }).then(function (response){
                    me.proceso=false;
                    me.cerrarModal(); //al guardar el registro se cierra el modal
                    me.listarPersonal(1,'','personas.nombre'); //se enlistan nuevamente los registros
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Aviso enviado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            actualizarPersonal(){
                if(this.validarPersonal() || this.proceso==true) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                this.proceso=true;

                let me = this;
                //Con axios se llama el metodo update de PersonalController
                axios.put('/user/actualizar',{
                    'nombre': this.nombre,
                    'apellidos': this.apellidos,
                    'celular': this.celular,
                    'email': this.email,
                    
                    'id' : this.id,
                    'usuario': this.usuario,
                    'password': this.password,
                    'rol_id' : this.rol_id,
                    'sucursal_id' : this.sucursal_id,
                    
                }).then(function (response){
                    me.proceso=false;
                    me.cerrarModal();
                    me.listarPersonal(1,'','personas.nombre');
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
           
            desactivarPersonal(id){
               swal({
                title: 'Esta seguro de desactivar a este usuario?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put('/user/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarPersonal(1,'','personas.nombre');
                        swal(
                        'Desactivado!',
                        'El registro ha sido desactivado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
            activarPersonal(id){
               swal({
                title: 'Esta seguro de activar a este usuario?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put('/user/activar',{
                        'id': id
                    }).then(function (response) {
                        me.listarPersonal(1,'','personas.nombre');
                        swal(
                        'Activado!',
                        'El registro ha sido activado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
            
            validarPersonal(){
                this.errorPersonal=0;
                this.errorMostrarMsjPersonal=[];

                if(!this.nombre || !this.apellidos) //Si la variable Personal esta vacia
                    this.errorMostrarMsjPersonal.push("El nombre de la Persona no puede ir vacio.");

                if(!this.usuario)
                    this.errorMostrarMsjPersonal.push("Ingresar nombre de usuario");
                
                if(!this.password)
                    this.errorMostrarMsjPersonal.push("Ingresar contraseña");

                if(!this.rol_id)
                    this.errorMostrarMsjPersonal.push("Seleccionar rol");

                if(this.errorMostrarMsjPersonal.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorPersonal = 1;

                return this.errorPersonal;
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
           
            cerrarModal(){
                this.modal = 0;
                this.modal2 = 0;
                this.nombre='';
                this.apellidos='';
               
                this.celular='';
               
                this.id = 0;
                this.password = '';
                this.usuario = '';
                this.condicion = 1;
                this.errorPersonal = 0;
                this.errorMostrarMsjPersonal = [];
                this.inmobiliaria='',
                this.tipo_vendedor=0
            

            },
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(modelo, accion,data =[]){
                switch(modelo){
                    case "Personal":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Personal';
                                this.departamento_id = '0',
                                this.empresa_id = 1,
                                this.nombre='';
                                this.apellidos='';
                                this.f_nacimiento='';
                                this.rfc='';
                                this.homoclave='';
                                this.colonia='0';
                                this.direccion='';
                                this.cp='';
                                this.telefono='';
                                this.ext='';
                                this.celular='';
                                this.email='';
                                this.usuario='';
                                this.password='';
                                this.condicion=1;
                                this.rol_id=0;
                                this.sucursal_id=0;
                                this.inmobiliaria='';
                                this.tipo_vendedor=0;

                                this.activo='1';
                                this.tipoAccion = 3;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal =1;
                                this.tituloModal='Actualizar Personal';
                                this.tipoAccion=2;
                                this.id=data['id'];
                                this.departamento_id=data['departamento_id'];
                                this.empresa_id=data['empresa_id'];
                                this.nombre=data['nombre'];
                                this.apellidos=data['apellidos'];
                                this.f_nacimiento=data['f_nacimiento'];
                                this.rfc=data['rfc'];
                                this.homoclave=data['homoclave'];
                                this.colonia=data['colonia'];
                                this.direccion=data['direccion'];
                                this.cp=data['cp'];
                                this.telefono=data['telefono'];
                                this.ext=data['ext'];
                                this.celular=data['celular'];
                                this.email=data['email'];
                                this.activo=data['activo'];
                                this.usuario=data['usuario'];
                                this.rol_id=data['rol_id'];
                                this.password=data['password'];
                                this.condicion=data['condicion'];
                                this.sucursal_id=data['sucursal_id'];
                                this.tipoAccion = 2;
                                this.inmobiliaria='';
                                this.tipo_vendedor=0;
                                break;
                            }
                            case 'aviso':
                            {
                                //console.log(data);
                                this.modal2 =1;
                                this.id=data['id'];
                                this.aviso='';
                                break;
                            }
                           
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarPersonal(1,this.buscar,this.criterio);
            this.selectSucursal();
        }
    }
</script>
<style>
    .row2 {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -5px;
    }
    .form-control:disabled, .form-control[readonly] {
        background-color: rgba(0, 0, 0, 0.06);
        opacity: 1;
        font-size: 0.85rem;
        color: #27417b;
    }
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

input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
   margin: 0;  
} 


</style>

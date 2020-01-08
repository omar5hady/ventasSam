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
                        <i class="fa fa-align-justify"></i> Compartidos
                        <!--   Boton Nuevo    -->
                        <button type="button" v-if="rolId == 1" @click="modal=1" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <!---->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Nombre</th>
                                        <th>Tamaño</th>
                                        <th>Extension</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="file in arrayFiles" :key="file.id">
                                        <td style="width:15%;">
                                            <a title="Descargar" type="button" 
                                                class="btn btn-primary" :href="'/files/'+file.name+'/download'">
                                                <i class="fa fa-download"></i>
                                            </a> 
                                            <button v-if="rolId == 1" title="Eliminar" type="button" class="btn btn-danger btn-sm" @click="eliminarArchivo(file.name,file.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </td>
                                        <td v-text="file.name"></td>
                                        <td v-text="(file.size/1000) + ' KB'"></td>
                                        <td v-text="file.extension"></td>
                                    </tr>                               
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal para diversos llenados de tabla en historial -->
                <div class="modal animated fadeIn" tabindex="-1" :class="{'mostrar': modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-primary modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" v-text="tituloModal"></h5>
                                <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                                <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                
                                <div class="form-group row">
                                    <div class="col-md-8">
                                        <form class="form-horizontal" @submit="dropboxSubmit" method="POST" enctype="multipart/form-data">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <i class="fa fa-file"></i>
                                                </span>
                                                <input type="file" v-on:change="dropboxFile" name="file" required>    
                                                <button class="btn btn-primary" type="submit">Guardar archivo</button>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
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
                arrayFiles : [],
                modal : 0,
                tituloModal : '',
                tipoAccion: 0,
                file: '',
            }
        },
        computed:{
            
        },
        methods : {
            dropboxFile(e){

                console.log(e.target);

                this.file = e.target.files[0];

            },

            dropboxSubmit(e) {

                e.preventDefault();
                let me = this;

                let formData = new FormData();
           
                formData.append('file', this.file);
                axios.post('/files/store', formData)
                .then(function (response) {
                   
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Archivo subido correctamente',
                        showConfirmButton: false,
                        timer: 2000
                        })
                    me.cerrarModal();
                    me.listarArchivos();
                })
                .catch(function (error) {
                    console.log(error);

                });

            },

            eliminarArchivo(archivo,id){
                swal({
                title: '¿Desea eliminar este archivo?',
                text: "El archivo se borrara completamente",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, borrar!'
                }).then((result) => {
                if (result.value) {
                    let me = this;

                //Con axios se llama el metodo update de LoteController
                    axios.delete('/files/delete',{
                        params: {
                        'file' : archivo,
                        'id' : id,
                        }
                    }).then(function (response){
                        //window.alert("Cambios guardados correctamente");
                        me.listarArchivos();
                        const toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                            });
                            toast({
                            type: 'success',
                            title: 'Archivo borrado correctamente'
                        })
                    }).catch(function (error){
                        console.log(error);
                    });
                }
                })

            },
            /**Metodo para mostrar los registros */
            listarArchivos(){
                let me = this;
                var url = '/file/index';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayFiles = respuesta.files;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            
            cerrarModal(){
                this.modal = 0;
            }
            
        },
        mounted() {
            this.listarArchivos();
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

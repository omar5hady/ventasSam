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
                        <i class="fa fa-align-justify"></i> Dashboard
                    </div>
                    <div class="card-body">
                        <div class="form-group row" v-if="rolId == 1">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <select class="form-control" v-model="vendedor_id">
                                        <option value="">Seleccione</option>
                                        <option v-for="vendedor in arrayVendedores" :key="vendedor.id" :value="vendedor.id" v-text="vendedor.nombre + ' ' + vendedor.apellidos "></option>
                                    </select>
                                    <button type="submit" @click="getPeso(vendedor_id),ventaDia(vendedor_id)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            
                            <!-- Alcance al Dia -->
                            <div class="col-sm-12 col-lg-5">
                                <div class="card">
                                    <div class="card-body p-4 d-flex align-items-center">
                                        <div>
                                            <div class="text-value-sm text-primary"><h5 style="font-weight: bold;">Alcance al día: {{this.diaAct}}</h5></div>
                                            <div class="text-value-sm">

                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead>
                                                        <tr><th></th>
                                                            <th>Total</th>
                                                            <th>Smart</th>
                                                            <th>Premium</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td></td>
                                                            <td>{{this.alcance.toFixed(2)}} %</td>
                                                            <td>{{this.alcanceSmart.toFixed(2)}} %</td>
                                                            <td>{{this.alcancePremium.toFixed(2)}} %</td>
                                                        </tr>   
                                                        <tr>
                                                            <td><strong>IDEAL</strong></td>
                                                            <td>{{this.alcanceIdeal.toFixed(2)}}%</td>
                                                            <td>{{this.alcanceIdealSmart.toFixed(2)}} %</td>
                                                            <td>{{this.alcanceIdealPrem.toFixed(2)}} %</td>
                                                        </tr> 

                                                    </tbody>
                                                </table>

                                                <div class="card text-white bg-dark">
                                                    <!-- <div class="h1 text-muted text-right mb-4">
                                                        <i class="fa fa-calculator"></i>
                                                    </div> -->
                                                    <div class="text-value text-left"><h6>{{this.forecast.toFixed(2)}}%</h6></div>
                                                    <h5 class="text-muted text-uppercase font-weight-bold text-left">Forecast</h5>
                                                    <div class="progress progress-white progress-xs mt-3">
                                                        <div class="progress-bar" role="progressbar" style="width: 100%" :aria-valuenow="forecast" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>



                                                <div>
                                                    <div class="card-body p-0 d-flex align-items-center">
                                                        <i class="bg-dark p-2" style="color: white;">Cuota del mes</i>
                                                        <div>
                                                            <div class="text-value-sm text-dark font-weight-bold">&nbsp; ${{this.formatNumber(this.pesoTotal)}}</div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-0 d-flex align-items-center">
                                                        <i class="bg-success p-2" style="color: white;">Venta del mes</i>
                                                        <div>
                                                            <div class="text-value-sm text-dark font-weight-bold">&nbsp; ${{this.formatNumber(this.pesoTotalReal)}}</div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-0 d-flex align-items-center">
                                                        <i class="bg-primary p-2">Faltante para cerrar al 100%</i>
                                                    </div>
                                                    <div>
                                                        <div class="text-value-sm text-primary font-weight-bold">
                                                            <h6>&nbsp; ${{this.formatNumber(this.faltanteCierre)}}</h6>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>

                            <!-- Ventas del Dia -->
                            <div class="col-sm-6 col-lg-5">
                                <div class="card">
                                    <div class="card-body p-4 d-flex align-items-center">
                                        <div>
                                            <div class="text-value-sm text-primary"><h5 style="font-weight: bold;">Ventas del día</h5></div>
                                            <div class="text-value-sm">

                                                <div>
                                                    <div class="row">
                                                        <div class="card-body p-0 d-flex align-items-center">
                                                            <i class="bg-primary p-2">Cuota del día</i>
                                                            <div>
                                                                <div class="text-value-sm text-primary font-weight-bold">&nbsp; ${{this.formatNumber(this.cuotaDia)}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="card-body p-0 d-flex align-items-center">
                                                            <i class="bg-dark p-2" style="color: white;">Ventas corte</i>
                                                            <div>
                                                                <div class="text-value-sm text-dark font-weight-bold">&nbsp; ${{this.formatNumber(this.corte)}}</div>
                                                                <div class="text-value-sm text-dark font-weight-bold">&nbsp;&nbsp;{{this.alcanceCorte.toFixed(2)}}%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="card-body p-2 d-flex align-items-center col-md-12 bg-primary">
                                                            <i class="bg-primary p-0" style="color: white;">Faltante</i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-value-sm text-dark font-weight-bold">&nbsp; ${{this.formatNumber(this.faltante)}}</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="card-body p-0 d-flex align-items-center">
                                                            <i class="bg-success p-2" style="color: white;">Cierre</i>
                                                            <div>
                                                                <div class="text-value-sm text-dark font-weight-bold">&nbsp; ${{this.formatNumber(this.cierre)}}</div>
                                                                <div class="text-value-sm text-dark font-weight-bold">&nbsp;&nbsp;{{this.alcanceCierre.toFixed(2)}}%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="card-body p-2 d-flex align-items-center col-md-12 bg-primary">
                                                            <i class="bg-primary p-0" style="color: white;">Faltante</i>
                                                        </div>
                                                        <div>
                                                            <div class="text-value-sm text-dark font-weight-bold">&nbsp; ${{this.formatNumber(this.faltante2)}}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>


                        </div>
                        
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            
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
                arrayCuota:[],
                arrayVendedores:[],
                diaAct: 0,
                vendedor_id:'',
                 ///Peso
                    pesoTotal:0,
                    pesoPremium:0,
                    pesoSmart:0,
                    pesoPremiumReal:0,
                    pesoSmartReal:0,
                    pesoTotalReal:0,
                //Alcance
                    alcance:0,
                    alcancePremium:0,
                    alcanceSmart:0,
                    alcanceIdeal:0,
                    alcanceIdealSmart:0,
                    alcanceIdealPrem:0,
                    faltanteCierre:0,
                    cuota:0,
                    cuotaDia:0,
                    corte:0,
                    alcanceCorte:0,
                    alcanceCierre:0,
                    faltante:0,
                    faltante2:0,
                    cierre:0,

                //Forecast
                    forecast:0,
            }
        },
        computed:{
            
        },
        methods : {
            formatNumber(value) {
                let val = (value/1).toFixed(2)
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },
            getPeso(vendedor_id){
                let me = this;
                me.arrayCuota=[];
                var url = '/dashboard/alcance?buscar='+vendedor_id;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayCuota = respuesta.cuota;

                    me.pesoPremium = me.arrayCuota[0].premium;
                    me.pesoSmart = me.arrayCuota[0].smart;
                    me.pesoTotal = me.pesoPremium + me.pesoSmart;

                    me.pesoPremiumReal = me.arrayCuota[0].premium_real;
                    me.pesoSmartReal = me.arrayCuota[0].smart_real;
                    me.pesoTotalReal = me.pesoPremiumReal + me.pesoSmartReal;

                    me.alcancePremium = (me.pesoPremiumReal/me.pesoPremium)*100;
                    me.alcanceSmart = (me.pesoSmartReal/me.pesoSmart)*100;
                    me.alcance = (me.pesoTotalReal/me.pesoTotal)*100;

                    me.diaAct = respuesta.hoy;

                    me.faltanteCierre = me.pesoTotal - me.pesoTotalReal;

                    me.cuotaDia = (me.pesoTotal-me.pesoTotalReal)/(respuesta.diasMes-me.diaAct);

                    me.forecast = (((me.pesoTotalReal/me.diaAct)*respuesta.diasMes)/me.pesoTotal)*100;

                    me.alcanceIdeal = (((me.pesoTotal/respuesta.diasMes)*me.diaAct)/me.pesoTotal)*100;
                    me.alcanceIdealSmart = (((me.pesoSmart/respuesta.diasMes)*me.diaAct)/me.pesoSmart)*100;
                    me.alcanceIdealPrem = (((me.pesoPremium/respuesta.diasMes)*me.diaAct)/me.pesoPremium)*100;

                })
                .catch(function (error) {
                    console.log(error);
                });
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

            ventaDia(vendedor_id){
                let me = this;
                var url = '/dashboard/ventaDia?buscar='+vendedor_id;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;

                    me.corte = respuesta.corte;
                    if(me.corte!=0){
                        me.alcanceCorte = me.cuotaDia/me.corte;
                    }
                    me.faltante = me.cuotaDia-me.corte;

                    me.cierre = respuesta.venta - me.corte;
                    me.faltante2 = me.cuotaDia - respuesta.venta;

                    if(me.cierre > 0){
                        me.alcanceCierre = me.cuotaDia/me.cierre;
                    }



                })
                .catch(function (error) {
                    console.log(error);
                });
                

            }
            
        },
        mounted() {
            this.getPeso(this.vendedor_id);
            this.ventaDia(this.vendedor_id);
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

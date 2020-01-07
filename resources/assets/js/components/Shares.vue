<template >
    <main class="main" >
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><strong><a style="color:#FFFFFF;" href="/">Home</a></strong></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card scroll-box">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Shares
                    </div>

                <template>
                    <div class="card-body"> 
                        <h5 v-text="'SHARES'"></h5>
                            <div class="form-group row border" id="accordion" role="tablist">
                                <div class="col-md-12">
                                </div>

                                <!--- PESO POR SEMANA --->
                                <div class="col-md-4">
                                   <div class="card border-primary">
                                          <div class="card-header">
                                            <strong><h5><center>
                                                <a data-toggle="collapse" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                                    PESO POR SEMANA </a>
                                            </center></h5></strong>
                                        </div>
                                    </div>
                                    <div class="form-group row collapse" id="collapseOne" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="fa fa-check"></i>1er Semana 25% &nbsp;&nbsp;
                                                <strong>${{this.formatNumber(this.pesoTotal * 0.25)}}</strong> &nbsp;&nbsp;
                                                <strong style="color: blue;" v-if="pesoTotalReal > pesoTotal*0.25" v-text="'Real: $'+formatNumber(pesoTotal*0.25)"></strong>
                                                <strong style="color: blue;" v-else v-text="'Real: $'+formatNumber(pesoTotalReal)"></strong>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="fa fa-check"></i>2da Semana 30% &nbsp;&nbsp;
                                                <strong>${{this.formatNumber(this.pesoTotal * 0.30)}}</strong> &nbsp;&nbsp;
                                                <strong style="color: blue;" v-if="pesoTotalReal > pesoTotal*0.55" v-text="'Real: $'+formatNumber(pesoTotal*0.30)"></strong>
                                                <strong style="color: blue;" v-else v-text="'Real: $'+formatNumber(pesoTotalReal - (pesoTotal*0.25))"></strong>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="fa fa-check"></i>3er Semana 30% &nbsp;&nbsp;
                                                <strong>${{this.formatNumber(this.pesoTotal * 0.30)}}</strong> &nbsp;&nbsp;
                                                <strong style="color: blue;" v-if="pesoTotalReal > pesoTotal*0.85" v-text="'Real: $'+formatNumber(pesoTotal*0.30)"></strong>
                                                <strong style="color: blue;" v-else v-text="'Real: $'+formatNumber(pesoTotalReal - (pesoTotal*0.55))"></strong>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="fa fa-check"></i>4ta Semana 15% &nbsp;&nbsp;
                                                <strong>${{this.formatNumber(this.pesoTotal * 0.15)}}</strong> 
                                                &nbsp;&nbsp;
                                                <strong style="color: blue;" v-if="pesoTotalReal > pesoTotal" v-text="'Real: $'+formatNumber(pesoTotal*0.15)"></strong>
                                                <strong style="color: blue;" v-else v-text="'Real: $'+formatNumber(pesoTotalReal - (pesoTotal*0.85))"></strong>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div> 

                                <!--- Share --->
                                <div class="col-md-8">
                                    <div class="card border-primary">
                                          <div class="card-header">
                                            <strong><h5><center>
                                                <a data-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" class="collapsed">
                                                    SHARE </a>
                                            </center></h5></strong>
                                        </div>
                                    </div>
                                    <div class="form-group row collapse" id="collapseTwo" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">                                      
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                Participación que tiene la marca en relación con la venta de la tienda.
                                                <center>
                                                    <strong>
                                                        <br>
                                                        <h6>Venta Samsung</h6>
                                                        <h6>______________________________  X 100</h6>
                                                        <h6>Venta total de la tienda</h6>
                                                    </strong>
                                                </center>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--- TICKET PROMEDIO --->
                                <div class="col-md-4">
                                    <div class="card border-primary">
                                          <div class="card-header">
                                            <strong><h5><center>
                                                <a data-toggle="collapse" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree" class="collapsed">
                                                    TICKET PROMEDIO </a>
                                            </center></h5></strong>
                                        </div>
                                    </div>
                                    <div class="form-group row collapse" id="collapseThree" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                Indicador de venta, promedio de costo de los equipos que se han vendido.
                                                <center>
                                                    <strong>
                                                        <br>
                                                        <h6>$ VENDIDO  /  PIEZAS VENDIDAS</h6><br>
                                                        <h6 style="color: blue;">Ideal</h6>
                                                        <h6>${{this.formatNumber(this.idealVendido)}}  /  {{this.idealPiezas}} = ${{this.formatNumber(this.ticketIdeal)}}</h6><br>
                                                        <h6 style="color: red;">Real</h6>
                                                        <h6>${{this.formatNumber(this.vendidoProm)}}  /  {{this.piezasProm}} = ${{this.formatNumber(this.ticketPromedio)}}</h6><br>
                                                    </strong>
                                                </center><br>
                                                Si es mas bajo del ideal significa que se esta vendiendo mas equipos baratos, <br>
                                                Al contratio si es mas alto del ideal siginifica que se esta vendiendo mas caro.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--- FORECAST --->
                                <div class="col-md-4">
                                    <div class="card border-primary">
                                          <div class="card-header">
                                            <strong><h5><center>
                                                <a data-toggle="collapse" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour" class="collapsed">
                                                    FORECAST </a>
                                            </center></h5></strong>
                                        </div>
                                    </div>
                                    <div class="form-group row collapse" id="collapseFour" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                Proyección de cierre, al ritmo que estoy vendiendo a cuanto voy a cerrar.
                                                <center>
                                                    <strong>
                                                        <br>
                                                        <h6>($ VENDIDO / DIA ACTUAL)  X  DIAS DEL MES</h6><br>
                                                        <h6>($ {{this.formatNumber(this.vendidoMes)}} / {{this.diaActual}})  X  {{this.diasMes}}</h6>
                                                        <h6 style="color: blue;"> = ${{ this.formatNumber(this.forecast) }}</h6>
                                                    </strong>
                                                </center><br>
                                                Este resultado nos dara una cantidad de cierre en pesos, si queremos sacar un porcentaje se divide
                                                esa cantidad entre la cuota y multiplicar X 100. <br>
                                                <center>
                                                    <strong>
                                                        <br>
                                                        <h6>( {{this.formatNumber(this.forecast)}} / {{this.formatNumber(this.pesoTotal)}})  X  100</h6>
                                                        <h6 style="color: blue;"> = {{ this.formatNumber(this.porcentajeForecast) }} %</h6>
                                                    </strong>
                                                </center>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--- ALCANCE --->
                                <div class="col-md-4">
                                    <div class="card border-primary">
                                          <div class="card-header">
                                            <strong><h5><center>
                                                <a data-toggle="collapse" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive" class="collapsed">
                                                    Alcance </a>
                                            </center></h5></strong>
                                        </div>
                                    </div>
                                    <div class="form-group row collapse" id="collapseFive" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                Venta total al dia en relación con la cuota
                                                <center>
                                                    <strong>
                                                        <br>
                                                        <h6>($ VENDIDO / CUOTA)  X  100</h6><br>

                                                        <h6>( $ {{this.formatNumber(this.pesoTotalReal)}} / $ {{this.formatNumber(this.pesoTotal)}} )  X  100</h6>
                                                        <h6 style="color: blue;"> = {{ this.alcance.toFixed(2)}}%</h6>


                                                    </strong>
                                                </center><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--- WOS --->
                                <div class="col-md-12">
                                    <div class="card border-primary">
                                          <div class="card-header">
                                            <strong><h5><center>
                                                <a data-toggle="collapse" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix" class="collapsed">
                                                    WOS </a>
                                            </center></h5></strong>
                                        </div>
                                    </div>
                                    <div class="form-group row collapse" id="collapseSix" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                <center>
                                                    Weeks of Stock ó Semanas de Inventario, Al ritmo que estoy vendiendo para cuantas semanas de 
                                                    venta me alcanza mi inventario actual. <br>
                                                    Para sacar esta informacón se debe tener las piezas que se han vendido promedio en las ultimas 4 semanas y el inventario actual. <br><br>

                                                    Ejemplo:&nbsp; Inventario Actual: 350 piezas. <br>
                                                    <li>Semana 1: 90 piezas</li>
                                                    <li>Semana 2: 87 piezas</li>
                                                    <li>Semana 3: 75 piezas</li>
                                                    <li>Semana 4: 105 piezas</li>
                                                    <strong>
                                                        <br>
                                                        <h6>PROMEDIO: Se suman todas las ventas y se divide entre 4 = 89 piezas</h6>
                                                    </strong>
                                                    <br> Teniendo esta información se realiza de la siguiente manera <br>
                                                    INVENTARIO ACTUAL EN PIEZAS <br>
                                                    PROMEDIO DE VENTAS <br> <br>
                                                    <h6 style="color: blue;">Ejemplo Real: {{this.inventario}}/{{this.ventas4weeks}} = {{this.wos.toFixed(2)}} WOS   </h6> <br>
                                                    Se considera un inventario sano abajo de 4 WOS, esto se puede sacar en general o 
                                                    por modelo y asi pueden ver que equipos se estan rezagando y enfocarse en desplazarlos. <br><br>

                                                    <div>
                                                        <button type="button" class="btn btn-success" @click="verWodInventario()">Ver Wod por equipo</button>
                                                    </div>
                                                </center><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                
                            </div>

                            <div class="form-group row">
                            </div>

                    </div>
                </template>
                    
                <!-------------------  Fin Div para Contratos que tienen paquete o promoción  --------------------->


                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>

            <!--- Modal Equipos --->
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal animated fadeIn" tabindex="-1" :class="{'mostrar': modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="'Wos Inventario'"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <div class="form-group row" v-for="equipo in arrayInventario" :key="equipo.id">
                                    <label class="col-md-2 form-control-label" for="text-input"></label>
                                    <div class="card-body">
                                        <li><strong>{{equipo.modelo}} : </strong>{{equipo.cantidad}}/{{equipo.venta.toFixed(2)}} = {{equipo.wos.toFixed(2)}} WOS </li>
                                    </div>
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
import { timeout } from 'q';
    export default {
        data(){
            return{
                arrayPeso:[],
                arrayProm:[],
                arrayInventario:[],
                ///Peso
                    pesoTotal:0,
                    pesoPremium:0,
                    pesoSmart:0,
                    pesoPremiumReal:0,
                    pesoSmartReal:0,
                    pesoTotalReal:0,

                //ticketProm
                    idealPiezas:0,
                    idealVendido:0,
                    ticketIdeal:0,

                    piezasProm:0,
                    vendidoProm:0,
                    ticketPromedio:0,

                //Alcance
                    alcance:0,

                //forecast
                    vendidoMes:0,
                    diaActual:0,
                    diasMes:0,
                    forecast:0,
                    porcentajeForecast:0,

                //Wos
                    inventario:0,
                    ventas4weeks:0,
                    wos:0,

                modal : 0

            }
        },
        computed:{
            
        },

        
        methods : {
            formatNumber(value) {
                let val = (value/1).toFixed(2)
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },
            getPeso(){
                let me = this;
                me.arrayPeso=[];
                var url = '/share/peso';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayPeso = respuesta.total;

                    me.pesoPremium = me.arrayPeso[0].premium;
                    me.pesoSmart = me.arrayPeso[0].smart;
                    me.pesoTotal = me.pesoPremium + me.pesoSmart;

                    me.idealVendido = me.pesoTotal;
                    me.idealPiezas = me.arrayPeso[0].qty_premium + me.arrayPeso[0].qty_smart;
                    me.ticketIdeal = me.idealVendido/me.idealPiezas;

                    me.pesoPremiumReal = me.arrayPeso[0].premium_real;
                    me.pesoSmartReal = me.arrayPeso[0].smart_real;
                    me.pesoTotalReal = me.pesoPremiumReal + me.pesoSmartReal;

                    me.alcance = (me.pesoTotalReal/me.pesoTotal)*100;

                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            getTicketPromedio(){
                let me = this;
                me.arrayProm=[];
                var url = '/share/ticketPromedio';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.vendidoProm = respuesta.ventas;
                    me.piezasProm = respuesta.cantidades;

                    me.ticketPromedio = me.vendidoProm/me.piezasProm;
                   
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            getForecast(){
                let me = this;
                var url = '/share/forecast';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.vendidoMes = respuesta.ventas;
                    me.diaActual = respuesta.hoy;
                    me.diasMes = respuesta.diasMes;

                    me.forecast = (me.vendidoMes/me.diaActual)*me.diasMes;

                    me.inventario = respuesta.inventario;
                    me.ventas4weeks = respuesta.ventas_30/4;
                    me.wos = me.inventario/me.ventas4weeks;

                    me.porcentajeForecast = (me.forecast/me.pesoTotal)*100;
                   
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            getWos(){
                let me = this;
                me.arrayProm=[];
                var url = '/share/wosDetallado';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayInventario = respuesta.detalle_inventario;
                   
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            verWodInventario(){
                this.modal = 1;
            },

            cerrarModal(){
                this.modal = 0;
            }
            
        },
       
        mounted() {
            this.getPeso();
            this.getTicketPromedio();
            this.getForecast();
            this.getWos();
            
        }
    }
</script>
<style>
    .line-separator{
        height:1px;
        background:#717171;
        border-bottom:1px solid #c2cfd6;
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
    .table2 {
    margin: auto;
    border-collapse: collapse;
    overflow-x: auto;
    display: block;
    width: fit-content;
    max-width: 100%;
    box-shadow: 0 0 1px 1px rgba(0, 0, 0, .1);
    }

    .td2, .th2 {
    border: solid rgb(200, 200, 200) 1px;
    padding: .5rem;
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

    .td2 {
    white-space: nowrap;
    border-bottom: none;
    color: rgb(20, 20, 20);
    }

    .td2:first-of-type, th:first-of-type {
    border-left: none;
    }

    .td2:last-of-type, th:last-of-type {
    border-right: none;
    } 
</style>

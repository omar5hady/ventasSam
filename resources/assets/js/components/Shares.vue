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
                                                <i class="fa fa-check"></i>1er Semana 25%
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="fa fa-check"></i>2da Semana  30%
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="fa fa-check"></i>3er Semana 30%
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="fa fa-check"></i>4ta Semana 15%
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
                                                        <h6>$ VENDIDO  /  PIEZAS VENDIDAS</h6>
                                                    </strong>
                                                </center><br>
                                                Si es mas bajo del ideal significa que se esta vendiendo mas equipos baratos, <br>
                                                Al contratio si es mas alto del ideal siginifica que se esta vendiendo mar caro.
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
                                                        <h6>($ VENDIDO / DIA ACTUAL)  X  DIAS DEL MES</h6>
                                                    </strong>
                                                </center><br>
                                                Este resultado nos dara una cantidad de cierre en pesos, si queremos sacar un porcentaje se divide
                                                 esa cantidad entre la cuota y multiplicar X 100
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
                                                        <h6>($ VENDIDO / CUOTA)  X  100</h6>
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
                                                    <strong>Ejemplo: 350/89 = 3.9 WOS   </strong> <br>
                                                    Se considera un inventario sano abajo de 4 WOS, esto se puede sacar en general o 
                                                    por modelo y asi pueden ver que equipos se estan rezagando y enfocarse en desplazarlos.
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

            
         
     </main>
</template>

<!-- ************************************************************************************************************************************  -->
<!-- *********************************************************** CODIGO JAVASCRIPT *************************************************************************  -->
<!-- ************************************************************************************************************************************  -->

<script>
    export default {
        data(){
            return{
               
            }
        },
        computed:{
            
        },

        
        methods : {
            
            
        },
       
        mounted() {
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

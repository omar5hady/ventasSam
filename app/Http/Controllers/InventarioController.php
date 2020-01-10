<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inventario;
use App\Detalle_inventario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Equipo;
use Auth;
use Excel;
use App\Venta;
use App\Desc_venta;

class InventarioController extends Controller
{
    public function index( Request $request ){

        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;

        if(Auth::user()->rol_id == 1){
            if($buscar == ''){
                $inventarios = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
                ->select('inventarios.id','inventarios.fecha','inventarios.total','inventarios.total_premium','inventarios.total_smart',
                        'sucursales.pv','sucursales.cadena')
                ->whereMonth('inventarios.fecha',Carbon::now()->month)
                ->whereYear('inventarios.fecha',Carbon::now()->year)
                ->orderBy('inventarios.fecha','desc')->take(1)->get();
            }
            else{
                $inventarios = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
                ->select('inventarios.id','inventarios.fecha','inventarios.total','inventarios.total_premium','inventarios.total_smart',
                        'sucursales.pv','sucursales.cadena')
                ->whereMonth('inventarios.fecha',Carbon::now()->month)
                ->whereYear('inventarios.fecha',Carbon::now()->year)
                ->where('inventarios.sucursal_id','=',$buscar)
                ->orderBy('inventarios.fecha','desc')->take(1)->get();
            }
            
        }
        else{
            if($buscar == ''){
                $inventarios = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
                ->select('inventarios.id','inventarios.fecha','inventarios.total','inventarios.total_premium','inventarios.total_smart',
                        'sucursales.pv','sucursales.cadena')
                ->whereMonth('inventarios.fecha',Carbon::now()->month)
                ->whereYear('inventarios.fecha',Carbon::now()->year)
                ->where('inventarios.sucursal_id','=',Auth::user()->sucursal_id)
                ->orderBy('inventarios.fecha','desc')->take(1)->get();
            }
            else{
                $inventarios = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
                ->select('inventarios.id','inventarios.fecha','inventarios.total','inventarios.total_premium','inventarios.total_smart',
                        'sucursales.pv','sucursales.cadena')
                ->where('inventarios.fecha','=',$buscar)
                ->where('inventarios.sucursal_id','=',Auth::user()->sucursal_id)
                ->orderBy('inventarios.fecha','desc')->take(1)->get();
            }
            
        }
        

        return [ 
            
            'inventarios'=> $inventarios , 'hoy'=>$hoy,'hora'=>$fecha->toTimeString()
        ];
    }

    public function store(Request $request)
    {
        $fecha = Carbon::now();
        $hora =  $fecha->toTimeString();
        if( !$request->ajax() )return redirect('/');
        
        $user_id = Auth::user()->id;
        $sucursal_id = Auth::user()->sucursal_id;

        $premium = 0;
        $smart = 0;
        
        try{
            DB::beginTransaction(); 
            
            $des = Inventario::where('sucursal_id','=',$sucursal_id)->get();
            if(sizeOf($des))
                foreach($des as $ep=>$det)
                {
                    $det->activo = 0;
                    $det->save();
                }


            $inventario = new Inventario();
            $inventario->sucursal_id = $sucursal_id;
            $inventario->fecha = $request->fecha;
            $inventario->hora = $hora;
            $inventario->vendedor = Auth::user()->usuario;
            $inventario->save();
 
            $equipos = $request->data;//Array de detalles
            //Recorro todos los elementos
 
            foreach($equipos as $ep=>$det)
            {
                if($det['cant'] != 0 || $det['cant']!= '0'){

                    $desc = new Detalle_inventario();
                    $desc->inventario_id = $inventario->id;
                    $desc->equipo_id = $det['id'];
                    $desc->cantidad = $det['cant'];
    
                    if($det['tipo'] == 0){
                        $smart+=$desc->cantidad;
                    }                    
                    else{
                        $premium+=$desc->cantidad;
                    }
                    $desc->save();

                }
            }
            
            $inventario->total_premium = $premium;
            $inventario->total_smart = $smart;
            $inventario->total = $premium + $smart;
            $inventario->save();
 
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function indexDetalle( Request $request ){

        $inventarios = Detalle_inventario::join('equipos','detalle_inventarios.equipo_id','=','equipos.id')
                ->select( 'detalle_inventarios.cantidad','equipos.modelo','equipos.tipo', 'detalle_inventarios.inventario_id',
                'detalle_inventarios.id')
                ->where('detalle_inventarios.inventario_id','=',$request->id)
                ->orderBy('equipos.modelo','asc')->get();

        return [ 'ventas'=> $inventarios ];
    }

    public function excelInventario( Request $request ){
        $buscar=$request->buscar;
        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();

        if($buscar == ''){
            $inventarioGen = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
            ->select('inventarios.total','inventarios.total_premium','inventarios.total_smart','inventarios.activo',
                    'sucursales.pv','sucursales.cadena','inventarios.id','inventarios.fecha','inventarios.hora'
            )->where('inventarios.activo','=',1)
            ->orderBy('inventarios.sucursal_id','asc')
            ->get();
        }
        else{
            $inventarioGen = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
                        ->select('inventarios.total','inventarios.total_premium','inventarios.total_smart','inventarios.activo',
                                'sucursales.pv','sucursales.cadena','inventarios.id','inventarios.fecha','inventarios.hora'
                        )->where('inventarios.activo','=',1)
                        ->where('inventarios.sucursal_id','=',$buscar)
                        ->orderBy('inventarios.sucursal_id','asc')
                        ->get();
        }
        

        $equipos = Equipo::where('condicion','=',1)->orderBy('modelo','asc')->get();
        $modelos=['Piso de venta','Fecha'];
        foreach($equipos as $eq=>$equipo){
            
            $modelos[] = $equipo->modelo;
        }

        if(sizeof($inventarioGen)){
            foreach($inventarioGen as $ep=>$inv){
                foreach($equipos as $eq=>$equipo){
                    $modelo = $equipo->modelo;
                    $inv->$modelo = Detalle_inventario::where('inventario_id','=',$inv->id)->where('equipo_id','=',$equipo->id)->sum('cantidad');
                }
            }
        }


        //return['inventario'=>$inventarioGen,'equipos'=>$equipos,'modelos'=>$modelos];

        return Excel::create('Inventarios', function($excel) use ($inventarioGen, $equipos,$hoy,$modelos){
            $excel->sheet('Inventario', function($sheet) use ($inventarioGen, $equipos,$hoy,$modelos){

                $sheet->row(1, [
                    'Inventario al dia: ', $hoy, 
                ]);
                
                $sheet->row(2, $modelos);
            
                $sheet->cells('A1:B1', function ($cells) {
                    // Set font family
                    $cells->setFontFamily('Calibri');
                    $cells->setBackground('#052154');
                    $cells->setFontColor('#ffffff');

                    // Set font size
                    $cells->setFontSize(12);

                    // Set font weight to bold
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->cells('A2:AZ2', function ($cells) {
                    // Set font family
                    $cells->setFontFamily('Calibri');
                    $cells->setBackground('#052154');
                    $cells->setFontColor('#ffffff');

                    // Set font size
                    $cells->setFontSize(12);

                    // Set font weight to bold
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->cells('A3:AZ100', function ($cells) {
                    // Set font family
                    $cells->setFontFamily('Calibri');

                    // Set font size
                    $cells->setFontSize(11);

                    // Set font weight to bold
                    $cells->setAlignment('center');
                });

                $sheet->setColumnFormat(array(
                    'B' => 'dd-mm-yyyy',
                ));

                $cont=sizeof($inventarioGen)+2;;

                foreach($inventarioGen as $index => $inv) {
                    $sucursal = $inv->pv.' | '.$inv->cadena;
                    $fechaInv = $inv->fecha.' '.$inv->hora;
                    $arreglo=[$sucursal,$fechaInv];
                    foreach($equipos as $eq=>$equipo){
                        $modelo = $equipo->modelo;
                        $arreglo[]=$inv->$modelo;
                    }

                    $sheet->row($index+3, 
                        $arreglo
                    );	
                }
                $num='A3:AZ' . $cont;
                $sheet->setBorder($num, 'thin');
               
            });
        }
        
        )->download('xlsx');
    }

    public function excelWos(Request $request){
        $buscar=$request->buscar;
        $fecha = Carbon::now();
        $four_weeks = Carbon::now()->subDays(28);
        $hoy =  $fecha->toDateString();

        if($buscar == ''){
            $inventarioGen = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
            ->select('inventarios.total','inventarios.total_premium','inventarios.total_smart','inventarios.activo',
                    'sucursales.pv','sucursales.cadena','inventarios.id','inventarios.fecha','inventarios.hora','inventarios.sucursal_id'
            )->where('inventarios.activo','=',1)
            ->orderBy('inventarios.sucursal_id','asc')
            ->get();
        }
        else{
            $inventarioGen = Inventario::join('sucursales','inventarios.sucursal_id','=','sucursales.id')
                        ->select('inventarios.total','inventarios.total_premium','inventarios.total_smart','inventarios.activo',
                                'sucursales.pv','sucursales.cadena','inventarios.id','inventarios.fecha','inventarios.hora','inventarios.sucursal_id'
                        )->where('inventarios.activo','=',1)
                        ->where('inventarios.sucursal_id','=',$buscar)
                        ->orderBy('inventarios.sucursal_id','asc')
                        ->get();
        }
        

        
        $equipos = Equipo::where('condicion','=',1)->orderBy('modelo','asc')->get();
        $modelos=['Piso de venta','WOS TOTAL'];
        foreach($equipos as $eq=>$equipo){
            
            $modelos[] = $equipo->modelo;
        }

        if(sizeof($inventarioGen)){
            foreach($inventarioGen as $ep=>$inv){
                $ventas_30 = Venta::where('fecha', '>=', $four_weeks)->where('sucursal_id','=',$inv->sucursal_id)->get();
                foreach($equipos as $eq=>$equipo){
                    $modelo = $equipo->modelo;
                    $inventarioEquipo = Detalle_inventario::where('inventario_id','=',$inv->id)->where('equipo_id','=',$equipo->id)->sum('cantidad');
                    $ventaCant=0;
                    $inv->$modelo = 0;

                    foreach($ventas_30 as $es=>$venta){
                        $desc = Desc_venta::where('venta_id','=',$venta->id)->where('equipo_id','=',$equipo->id)->get();
                        if(sizeof($desc)){
                            $ventaCant+=$desc[0]->cantidad;
                        }
                    }

                    if($ventaCant!=0){
                        $inv->$modelo = $inventarioEquipo/($ventaCant/4);
                    }
                    
                }
                $inv->venta=0;
                foreach($ventas_30 as $el=>$venta2){
                    $desc = Desc_venta::where('venta_id','=',$venta2->id)->sum('cantidad');
                    
                        $inv->venta+=$desc;
                    
                }
            }
        }


        //return['inventario'=>$inventarioGen,'equipos'=>$equipos,'modelos'=>$modelos];

        return Excel::create('WOS', function($excel) use ($inventarioGen, $equipos,$hoy,$modelos){
            $excel->sheet('WOS', function($sheet) use ($inventarioGen, $equipos,$hoy,$modelos){

                $sheet->row(1, [
                    'Wos al dia: ', $hoy, 
                ]);
                
                $sheet->row(2, $modelos);
            
                $sheet->cells('A1:B1', function ($cells) {
                    // Set font family
                    $cells->setFontFamily('Calibri');
                    $cells->setBackground('#052154');
                    $cells->setFontColor('#ffffff');

                    // Set font size
                    $cells->setFontSize(12);

                    // Set font weight to bold
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->cells('A2:AZ2', function ($cells) {
                    // Set font family
                    $cells->setFontFamily('Calibri');
                    $cells->setBackground('#052154');
                    $cells->setFontColor('#ffffff');

                    // Set font size
                    $cells->setFontSize(12);

                    // Set font weight to bold
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->cells('A3:AZ100', function ($cells) {
                    // Set font family
                    $cells->setFontFamily('Calibri');

                    // Set font size
                    $cells->setFontSize(11);

                    // Set font weight to bold
                    $cells->setAlignment('center');
                });


                $cont=sizeof($inventarioGen)+1;;

                foreach($inventarioGen as $index => $inv) {
                    $sucursal = $inv->pv.' | '.$inv->cadena;
                    if($inv->venta != 0)
                        $wos = round($inv->total/($inv->venta/4)).' WOS';
                    else
                        $wos = '0'.' WOS';
                    $arreglo=[$sucursal,$wos];
                    foreach($equipos as $eq=>$equipo){
                        $modelo = $equipo->modelo;
                        $arreglo[]=round($inv->$modelo,2).' WOS';
                    }

                    $sheet->row($index+3, 
                        $arreglo
                    );	
                }
                $num='A3:AZ' . $cont;
                $sheet->setBorder($num, 'thin');
               
            });
        }
        
        )->download('xlsx');
    }
}

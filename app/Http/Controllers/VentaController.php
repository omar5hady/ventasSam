<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;
use App\Desc_venta;
use App\Cuota;
use App\Inventario;
use App\Equipo;
use App\Detalle_inventario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;
use Excel;

class VentaController extends Controller
{
    public function index( Request $request ){

        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;
        $buscar2 = $request->buscar2;
        $sucursal_id = $request->sucursal_id;

        if(Auth::user()->rol_id == 1){
            if($buscar == ''){
                if($sucursal_id == ''){
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereMonth('ventas.fecha',Carbon::now()->month)
                    ->whereYear('ventas.fecha',Carbon::now()->year)
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereMonth('ventas.fecha',Carbon::now()->month)
                    ->whereYear('ventas.fecha',Carbon::now()->year)
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
                //->whereBetween($criterio, [$buscar, $buscar3])
            }
            else{
                if($sucursal_id == ''){
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
            }
            
        }
        else{
            if($buscar == ''){
                $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                        'sucursales.pv','sucursales.cadena','ventas.promocion')
                ->whereMonth('ventas.fecha',Carbon::now()->month)
                ->whereYear('ventas.fecha',Carbon::now()->year)
                ->where('ventas.user_id','=',Auth::user()->id)
                ->orderBy('ventas.fecha','asc')->paginate(31);
            }
            else{
                $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                        'sucursales.pv','sucursales.cadena','ventas.promocion')
                ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                ->where('ventas.user_id','=',Auth::user()->id)
                ->orderBy('ventas.fecha','asc')->paginate(31);
            } 
        }
        

        return [ 
            'pagination' => [
                'total'         => $ventas->total(),
                'current_page'  => $ventas->currentPage(),
                'per_page'      => $ventas->perPage(),
                'last_page'     => $ventas->lastPage(),
                'from'          => $ventas->firstItem(),
                'to'            => $ventas->lastItem(),
            ],
            'ventas'=> $ventas , 'hoy'=>$hoy 
        ];
    }

    public function store(Request $request)
    {
        if( !$request->ajax() )return redirect('/');
        
        $user_id = Auth::user()->id;
        $sucursal_id = Auth::user()->sucursal_id;

        $premium = 0;
        $smart = 0;
        $premiumCant = 0;
        $smartCant = 0;

        $mes = new Carbon($request->fecha);

        $cuota = Cuota::where('month','=',$mes->month)->where('user_id','=',Auth::user()->id)->get();
        $inventario = Inventario::where('sucursal_id','=',Auth::user()->sucursal_id)->orderBy('fecha','desc')->get();
        
        try{
            DB::beginTransaction(); 
            $venta = new Venta();
            $venta->sucursal_id = $sucursal_id;
            $venta->fecha = $request->fecha;
            $venta->user_id = $user_id;
            $venta->promocion = $request->promocion;
            $venta->save();
 
            $equipos = $request->data;//Array de detalles
            //Recorro todos los elementos
 
            foreach($equipos as $ep=>$det)
            {
                if($det['cant'] != 0 || $det['cant']!= '0'){

                    $det_id = Detalle_inventario::where('inventario_id','=',$inventario[0]->id)->where('equipo_id','=',$det['id'])->get();

                    if(sizeof($det_id) > 0){
                        $detalle = Detalle_inventario::findOrFail($det_id[0]->id);
                        $detalle->cantidad-= $det['cant'];
                        $detalle->save();
                    }

                    $desc = new Desc_venta();
                    $desc->venta_id = $venta->id;
                    $desc->equipo_id = $det['id'];
                    $desc->cantidad = $det['cant'];
                    $desc->total = $det['cant'] * $det['precio'];
    
                    if($det['tipo'] == 0){
                        $smart+=$desc->total;
                        $smartCant+= $desc->cantidad;
                    }                    
                    else{
                        $premium+=$desc->total;
                        $premiumCant+= $desc->cantidad;
                    }
                    $desc->save();

                }
            }

            $calcCuota = Cuota::findOrFail($cuota[0]->id);
            $calcCuota->premium_real += $premium;
            $calcCuota->smart_real += $smart;
            $calcCuota->qty_premium_real += $premiumCant;
            $calcCuota->qty_smart_real += $smartCant;
            $calcCuota->save();

            $calcInventario = Inventario::findOrFail($inventario[0]->id);
            $calcInventario->total_premium -= $premiumCant;
            $calcInventario->total_smart -= $smartCant;
            $calcInventario->total -= ($premiumCant + $smartCant);
            $calcInventario->save();

            
            $venta->total_premium = $premium;
            $venta->total_smart = $smart;
            $venta->total = $premium + $smart;
            $venta->save();
 
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function indexDetalle( Request $request ){

        $ventas = Desc_venta::join('equipos','desc_ventas.equipo_id','=','equipos.id')
                ->select( 'desc_ventas.cantidad','desc_ventas.total','equipos.modelo','equipos.tipo', 'desc_ventas.venta_id')
                ->where('desc_ventas.venta_id','=',$request->id)
                ->orderBy('equipos.modelo','asc')->get();

        return [ 'ventas'=> $ventas ];
    }

    public function excelVentas( Request $request ){

        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;
        $buscar2 = $request->buscar2;
        $sucursal_id = $request->sucursal_id;

        if(Auth::user()->rol_id == 1){
            if($buscar == ''){
                if($sucursal_id == ''){
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereMonth('ventas.fecha',Carbon::now()->month)
                    ->whereYear('ventas.fecha',Carbon::now()->year)
                    ->orderBy('ventas.fecha','asc')->get();
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereMonth('ventas.fecha',Carbon::now()->month)
                    ->whereYear('ventas.fecha',Carbon::now()->year)
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->orderBy('ventas.fecha','asc')->get();
                }
            }
            else{
                if($sucursal_id == ''){
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->orderBy('ventas.fecha','asc')->get();
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->orderBy('ventas.fecha','asc')->get();
                }
            }
            
        }

        $equipos = Equipo::where('condicion','=',1)->orderBy('modelo','asc')->get();
        $modelos=['Piso de venta','Fecha','Total','Total Premium','Total Smart'];
        foreach($equipos as $eq=>$equipo){
            
            $modelos[] = $equipo->modelo;
        }

        if(sizeof($ventas)){
            foreach($ventas as $ep=>$inv){
                foreach($equipos as $eq=>$equipo){
                    $modelo = $equipo->modelo;
                    $inv->$modelo = Desc_venta::where('venta_id','=',$inv->id)->where('equipo_id','=',$equipo->id)->sum('cantidad');
                }
            }
        }

        //return['ventas'=>$ventas,'equipos'=>$equipos,'modelos'=>$modelos];
        return Excel::create('Ventas', function($excel) use ($ventas, $equipos,$modelos){
            $excel->sheet('ventas', function($sheet) use ($ventas, $equipos,$modelos){
                
                $sheet->row(1, $modelos);


                $sheet->cells('A1:AZ1', function ($cells) {
                    $cells->setBackground('#052154');
                    $cells->setFontColor('#ffffff');
                    // Set font family
                    $cells->setFontFamily('Calibri');

                    // Set font size
                    $cells->setFontSize(13);

                    // Set font weight to bold
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });

                $sheet->setColumnFormat(array(
                    'C' => '$#,##0.00',
                    'D' => '$#,##0.00',
                    'E' => '$#,##0.00',
                    'B' => 'yyyy-mm-dd',
                ));

                
                $cont=sizeof($ventas)+1;;

                foreach($ventas as $index => $inv) {
                    $sucursal = $inv->pv.' | '.$inv->cadena;
                    $fechaInv = $inv->fecha.' '.$inv->hora;
                    $total=$inv->total;
                    $total_premium=$inv->total_premium;
                    $total_smart=$inv->total_smart;
                    $arreglo=[$sucursal,$fechaInv,$total,$total_premium,$total_smart];
                    foreach($equipos as $eq=>$equipo){
                        $modelo = $equipo->modelo;
                        $arreglo[]=$inv->$modelo;
                    }

                    $sheet->row($index+3, 
                        $arreglo
                    );	
                }
                $num='A1:AZ' . $cont;
                $sheet->setBorder($num, 'thin');
            });
        }
        
        )->download('xlsx');

    }
}

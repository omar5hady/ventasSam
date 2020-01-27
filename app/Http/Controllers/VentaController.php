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
                            'sucursales.pv','sucursales.cadena','ventas.promocion','ventas.sucursal_id','ventas.user_id')
                    ->whereMonth('ventas.fecha',Carbon::now()->month)
                    ->whereYear('ventas.fecha',Carbon::now()->year)
                    ->where('sucursales.condicion','=',1)
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion','ventas.sucursal_id','ventas.user_id')
                    ->whereMonth('ventas.fecha',Carbon::now()->month)
                    ->whereYear('ventas.fecha',Carbon::now()->year)
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->where('sucursales.condicion','=',1)
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
                //->whereBetween($criterio, [$buscar, $buscar3])
            }
            else{
                if($sucursal_id == ''){
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion','ventas.sucursal_id','ventas.user_id')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->where('sucursales.condicion','=',1)
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion','ventas.sucursal_id','ventas.user_id')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->where('sucursales.condicion','=',1)
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->orderBy('ventas.fecha','asc')->paginate(31);
                }
            }
            
        }
        else{
            if($buscar == ''){
                $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                        'sucursales.pv','sucursales.cadena','ventas.promocion','ventas.sucursal_id','ventas.user_id')
                ->whereMonth('ventas.fecha',Carbon::now()->month)
                ->whereYear('ventas.fecha',Carbon::now()->year)
                ->where('ventas.user_id','=',Auth::user()->id)
                ->where('sucursales.condicion','=',1)
                ->orderBy('ventas.fecha','asc')->paginate(31);
            }
            else{
                $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                        'sucursales.pv','sucursales.cadena','ventas.promocion','ventas.sucursal_id','ventas.user_id')
                ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                ->where('ventas.user_id','=',Auth::user()->id)
                ->where('sucursales.condicion','=',1)
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

        if($request->sucursal == ''){
            $user_id = Auth::user()->id;
            $sucursal_id = Auth::user()->sucursal_id;
        }
        else{
            if($request->user_id == ''){
                $sucursal_id = $request->sucursal;
                $user_id = Auth::user()->id;
            }
            else{
                $user_id = $request->user_id;
                $sucursal_id = $request->sucursal;
            }
            
        }

        $premium = 0;
        $smart = 0;
        $premiumCant = 0;
        $smartCant = 0;

        $mes = new Carbon($request->fecha);

        $cuota = Cuota::where('month','=',$mes->month)->where('user_id','=',$user_id)->get();
        $inventario = Inventario::where('sucursal_id','=',$sucursal_id)->orderBy('fecha','desc')->get();
        
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
                        $detalle->cantidad = $detalle->cantidad - $det['cant'];
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
            $calcInventario->total_premium = $calcInventario->total_premium - $premiumCant;
            $calcInventario->total_smart = $calcInventario->total_smart - $smartCant;
            $calcInventario->total = $calcInventario->total - ($premiumCant + $smartCant);
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

    public function update(Request $request){
        if( !$request->ajax() )return redirect('/');

        $user_id = $request->user_id;
        $sucursal_id = $request->sucursal_id;

        $premiumCantOld = 0;
        $smartCantOld = 0;
        $premiumCant = 0;
        $smartCant = 0;
        $smart = 0;
        $premium = 0;

        $mes = new Carbon($request->fecha);

        $cuota = Cuota::where('month','=',$mes->month)->where('user_id','=',$user_id)->get(); //Busco cuota actual
        $inventario = Inventario::where('sucursal_id','=',$sucursal_id)->orderBy('fecha','desc')->get(); //Busco inventario actual

        $descOld = Desc_venta::join('equipos','desc_ventas.equipo_id','=','equipos.id') //Descripcion de ventas que se van a actualizar
                    ->select( 'desc_ventas.cantidad','desc_ventas.total','equipos.modelo','equipos.tipo', 
                                'desc_ventas.venta_id','desc_ventas.equipo_id')
                    ->where('desc_ventas.venta_id','=',$request->id)
                    ->orderBy('equipos.modelo','asc')->get();

        foreach($descOld as $ol=>$old){
            $det_id = Detalle_inventario::where('inventario_id','=',$inventario[0]->id)->where('equipo_id','=',$old->equipo_id)->get();

            if(sizeof($det_id) > 0){
                $detalle = Detalle_inventario::findOrFail($det_id[0]->id);
                $detalle->cantidad+= $old->cantidad;
                $detalle->save();
            }

            if($old->tipo == 0){
                $smartCantOld+= $old->cantidad;
            }                    
            else{
                $premiumCantOld+= $old->cantidad;
            }
        }

        
        try{
            DB::beginTransaction(); 
            $venta = Venta::findOrFail($request->id);
            $venta->promocion = $request->promocion;
            $venta->save();
 
            $equipos = $request->data;//Array de detalles
            //Recorro todos los elementos
 
            foreach($equipos as $ep=>$det)
            {
                if($det['cantidad'] == ''){
                    $det['cantidad'] = 0;
                }

                    $det_id = Detalle_inventario::where('inventario_id','=',$inventario[0]->id)->where('equipo_id','=',$det['equipo_id'])->get();

                    if(sizeof($det_id) > 0){
                        $detalle = Detalle_inventario::findOrFail($det_id[0]->id);
                        $detalle->cantidad= $detalle->cantidad - $det['cantidad'];
                        $detalle->save();
                    }

                    $desc = Desc_venta::findOrFail($det['id']);
                    $desc->venta_id = $venta->id;
                    $desc->equipo_id = $det['equipo_id'];
                    $desc->cantidad = $det['cantidad'];
                    $desc->total = $det['cantidad'] * $det['precio'];
    
                    if($det['tipo']  == 0){
                        $smart += $desc->total;
                        $smartCant += $desc->cantidad;
                    }                    
                    else{
                        $premium += $desc->total;
                        $premiumCant += $desc->cantidad;
                    }
                    $desc->save();
            }

            $calcCuota = Cuota::findOrFail($cuota[0]->id);
            $calcCuota->premium_real += $premium;
            $calcCuota->smart_real += $smart;
            $calcCuota->qty_premium_real += $premiumCant;
            $calcCuota->qty_smart_real += $smartCant;

            $calcCuota->premium_real = $calcCuota->premium_real - $venta->total_premium;
            $calcCuota->smart_real = $calcCuota->smart_real - $venta->total_smart;
            $calcCuota->qty_premium_real = $calcCuota->qty_premium_real - $premiumCantOld;
            $calcCuota->qty_smart_real = $calcCuota->qty_smart_real - $smartCantOld;

            $calcCuota->save();

            $calcInventario = Inventario::findOrFail($inventario[0]->id);
            $calcInventario->total_premium = $calcInventario->total_premium - $premiumCant;
            $calcInventario->total_smart = $calcInventario->total_smart - $smartCant;
            $calcInventario->total = $calcInventario->total - ($premiumCant + $smartCant);

            $calcInventario->total_premium += $premiumCantOld;
            $calcInventario->total_smart += $smartCantOld;
            $calcInventario->total += ($premiumCantOld + $smartCantOld);
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
                ->select( 'desc_ventas.id','desc_ventas.cantidad','desc_ventas.total','equipos.modelo','equipos.tipo',
                    'equipos.precio', 'desc_ventas.venta_id','desc_ventas.equipo_id')
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
                    ->where('sucursales.condicion','=',1)
                    ->orderBy('ventas.fecha','asc')->get();
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereMonth('ventas.fecha',Carbon::now()->month)
                    ->whereYear('ventas.fecha',Carbon::now()->year)
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->where('sucursales.condicion','=',1)
                    ->orderBy('ventas.fecha','asc')->get();
                }
            }
            else{
                if($sucursal_id == ''){
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->where('sucursales.condicion','=',1)
                    ->orderBy('ventas.fecha','asc')->get();
                }
                else{
                    $ventas = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                    ->select('ventas.id','ventas.fecha','ventas.total','ventas.total_premium','ventas.total_smart',
                            'sucursales.pv','sucursales.cadena','ventas.promocion')
                    ->whereBetween('ventas.fecha', [$buscar, $buscar2])
                    ->where('ventas.sucursal_id','=',$sucursal_id)
                    ->where('sucursales.condicion','=',1)
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

    public function deleteDetalle( Request $request ){
        $user_id = $request->user_id;
        $sucursal_id = $request->sucursal_id;
        $mes = new Carbon($request->fecha);

        $cuota = Cuota::where('month','=',$mes->month)->where('user_id','=',$user_id)->get(); //Busco cuota actual
        $inventario = Inventario::where('sucursal_id','=',$sucursal_id)->orderBy('fecha','desc')->get(); //Busco inventario actual


        $detalle = Desc_venta::findOrFail($request->id);

        $equipo = Equipo::findOrFail($detalle->equipo_id);

        $venta = Venta::findOrFail($detalle->venta_id);
        $venta->total = $venta->total - $detalle->total;
        if($equipo->tipo == 0){
            $venta->total_smart = $venta->total_smart - $detalle->total;
        }
        else{
            $venta->total_premium = $venta->total_premium - $detalle->total;
        }
        $venta->save();

        $det_invt = Detalle_inventario::where('inventario_id','=',$inventario[0]->id)->where('equipo_id','=',$detalle->equipo_id)->get();

        if(sizeof($det_invt) > 0){
            $d_inv = Detalle_inventario::findOrFail($det_invt[0]->id);
            $d_inv->cantidad += $detalle->cantidad;
            $d_inv->save();

            

            if($equipo->tipo == 0){
                $inventario = Inventario::findOrFail($d_inv->inventario_id);
                $inventario->total_smart = $inventario->total_smart + $detalle->cantidad;
                $inventario->total = $inventario->total + $detalle->cantidad;
                $inventario->save();
            }                    
            else{
                $inventario = Inventario::findOrFail($d_inv->inventario_id);
                $inventario->total_premium = $inventario->total_premium + $detalle->cantidad;
                $inventario->total = $inventario->total + $detalle->cantidad;
                $inventario->save();
            }
        }

        $cuota_det = Cuota::findOrFail($cuota[0]->id);
        if($equipo->tipo == 0){
            $cuota_det->smart_real = $cuota_det->smart_real - $detalle->total;
            $cuota_det->qty_smart_real = $cuota_det->qty_smart_real - $detalle->cantidad;
        }
        else{
            $cuota_det->premium_real = $cuota_det->premium_real - $detalle->total;;
            $cuota_det->qty_premium_real = $cuota_det->qty_premium_real - $detalle->cantidad;
        }
        

        $cuota_det->save();
        $detalle->delete();

        
    }

    public function addDetalle( Request $request ){

        $user_id = $request->user_id;
        $sucursal_id = $request->sucursal_id;
        $mes = new Carbon($request->fecha);

        $equipo = Equipo::findOrFail($request->equipo_id);

        $cuota = Cuota::where('month','=',$mes->month)->where('user_id','=',$user_id)->get(); //Busco cuota actual
        $inventario = Inventario::where('sucursal_id','=',$sucursal_id)->orderBy('fecha','desc')->get(); //Busco inventario actual
        $venta = Venta::findOrFail($request->id);

        $desc = new Desc_venta();
        $desc->venta_id = $request->id;
        $desc->equipo_id = $request->equipo_id;
        $desc->cantidad = $request->cantidad;
        $desc->total = $request->cantidad * $equipo->precio;

        if($equipo->tipo == 0){
            $venta->total_smart += $desc->total;
        }
        else{
            $venta->total_premium += $desc->total;
        }
        $venta->total += $desc->total;
        $venta->save();

        $det_invt = Detalle_inventario::where('inventario_id','=',$inventario[0]->id)->where('equipo_id','=',$request->equipo_id)->get();

        if(sizeof($det_invt) > 0){
            $d_inv = Detalle_inventario::findOrFail($det_invt[0]->id);
            $d_inv->cantidad = $d_inv->cantidad - $desc->cantidad;
            $d_inv->save();
            

            if($equipo->tipo == 0){
                $inventario = Inventario::findOrFail($d_inv->inventario_id);
                $inventario->total_smart = $inventario->total_smart - $desc->cantidad;
                $inventario->total = $inventario->total - $desc->cantidad;
                $inventario->save();
            }                    
            else{
                $inventario = Inventario::findOrFail($d_inv->inventario_id);
                $inventario->total_premium = $inventario->total_premium - $desc->cantidad;
                $inventario->total = $inventario->total - $desc->cantidad;
                $inventario->save();
            }
        }

        $cuota_det = Cuota::findOrFail($cuota[0]->id);
        if($equipo->tipo == 0){
            $cuota_det->smart_real = $cuota_det->smart_real + $desc->total;
            $cuota_det->qty_smart_real = $cuota_det->qty_smart_real + $desc->cantidad;
        }
        else{
            $cuota_det->premium_real = $cuota_det->premium_real + $desc->total;;
            $cuota_det->qty_premium_real = $cuota_det->qty_premium_real + $desc->cantidad;
        }
        

        $cuota_det->save();
        $desc->save();

        

    }
}

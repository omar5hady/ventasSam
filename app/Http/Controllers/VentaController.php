<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;
use App\Desc_venta;
use App\Cuota;
use App\Inventario;
use App\Detalle_inventario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

class VentaController extends Controller
{
    public function index( Request $request ){

        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;

        if(Auth::user()->rol_id == 1){
            if($buscar == ''){
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
                ->where('ventas.fecha','=',$buscar)
                ->orderBy('ventas.fecha','asc')->paginate(31);
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
                ->where('ventas.fecha','=',$buscar)
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
}

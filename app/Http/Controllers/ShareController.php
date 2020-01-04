<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Venta;
use App\Desc_venta;
use App\Cuota;
use App\Sucursal;
use App\Inventario;
use App\Detalle_inventario;
use Carbon\Carbon;

class ShareController extends Controller
{
    public function peso(Request $request){
        $total = Cuota::where('user_id','=',Auth::user()->id)
        ->where('month','=',Carbon::now()->month)->get();

        return['total'=>$total];
    }

    public function ticketPromedio(Request $request){
        $fecha = new Carbon('yesterday');
        $hoy =  $fecha->toDateString();
        $cantidades = 0;

        $ventas = Venta::where('sucursal_id','=',Auth::user()->sucursal_id)
                    ->where('fecha','=',$hoy)->get();
        
        $desc = Desc_venta::where('venta_id','=',$ventas[0]->id)->get();

        if(sizeof($desc)){
            foreach($desc as $ep=>$det)
            {
                $cantidades+=$det->cantidad;
            }
        }
        
        return['ventas'=>$ventas[0]->total,'cantidades'=>$cantidades];
    }

    public function forecast(Request $request){
        $fecha = Carbon::now();
        $four_weeks = Carbon::now()->subDays(28);
        $total=0;

        $inventario=Inventario::whereMonth('fecha',Carbon::now()->month)
        ->whereYear('fecha',Carbon::now()->year)
        ->where('sucursal_id','=',Auth::user()->sucursal_id)
        ->orderBy('fecha','desc')->get();

        $ventas = Venta::whereMonth('fecha',$fecha->month)->where('sucursal_id','=',Auth::user()->sucursal_id)->sum('total');

        $ventas_30 = Venta::where('fecha', '>=', $four_weeks)->get();

        if(sizeof($ventas_30)){
            foreach($ventas_30 as $ep=>$det)
            {
                $cant=Desc_venta::where('venta_id','=',$det->id)->sum('cantidad');
                $total+=$cant;
            }
        }

        return['ventas'=>$ventas,'hoy'=>$fecha->day, 'diasMes'=>$fecha->daysInMonth,'ventas_30'=>$cant,'inventario'=>$inventario[0]->total];
    }

    public function wosDetallado(Request $request){
        $fecha = Carbon::now();
        $four_weeks = Carbon::now()->subDays(28);

        $inventario=Inventario::whereMonth('fecha',Carbon::now()->month)
        ->whereYear('fecha',Carbon::now()->year)
        ->where('sucursal_id','=',Auth::user()->sucursal_id)
        ->orderBy('fecha','desc')->get();

        $detInventario=Detalle_inventario::join('equipos','detalle_inventarios.equipo_id','=','equipos.id')
        ->select( 'detalle_inventarios.cantidad','equipos.modelo','detalle_inventarios.inventario_id','detalle_inventarios.equipo_id')
        ->where('detalle_inventarios.inventario_id','=',$inventario[0]->id)->get();

        $ventas_30 = Venta::where('fecha', '>=', $four_weeks)->where('sucursal_id','=',Auth::user()->sucursal_id)->get();

        if(sizeof($detInventario)){
            foreach($detInventario as $ep=>$det)
            {
                $det->venta = 0;
                $det->wos = 0;
                foreach($ventas_30 as $ep=>$venta){
                    $desc = Desc_venta::where('venta_id','=',$venta->id)->where('equipo_id','=',$det->equipo_id)->get();
                    if(sizeof($desc)){
                        $det->venta+=$desc[0]->cantidad;
                    }
                }
                if($det->venta!=0){
                    $det->venta = $det->venta/4;
                    $det->wos = $det->cantidad/$det->venta;
                }
            }
        }

        return ['detalle_inventario'=>$detInventario];

    }


}

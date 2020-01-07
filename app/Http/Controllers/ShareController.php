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

    //Basicos para rol 1

    public function pesoAdmn(Request $request){
        $buscar = $request->buscar;
        if($buscar == ''){
            $total = Cuota::where('user_id','=',Auth::user()->id)
            ->where('month','=',Carbon::now()->month)->get();

            $total[0]->premium = Cuota::where('month','=',Carbon::now()->month)->sum('premium');
            $total[0]->smart = Cuota::where('month','=',Carbon::now()->month)->sum('smart');
            $total[0]->premium_real = Cuota::where('month','=',Carbon::now()->month)->sum('premium_real');
            $total[0]->smart_real = Cuota::where('month','=',Carbon::now()->month)->sum('smart_real');
            $total[0]->qty_premium = Cuota::where('month','=',Carbon::now()->month)->sum('qty_premium');
            $total[0]->qty_smart = Cuota::where('month','=',Carbon::now()->month)->sum('qty_smart');
            $total[0]->qty_premium_real = Cuota::where('month','=',Carbon::now()->month)->sum('qty_premium_real');
            $total[0]->qty_smart_real = Cuota::where('month','=',Carbon::now()->month)->sum('qty_smart_real');
        }
        else{
            $total = Cuota::where('user_id','=',$buscar)
                ->where('month','=',Carbon::now()->month)->get();
        }
        

        return['total'=>$total];
    }

    public function ticketPromedioAdmn(Request $request){
        $fecha = new Carbon('yesterday');
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;
        $cantidades = 0;

        if($buscar != ''){
            $usuario = User::findOrFail($buscar);
            $ventas = Venta::where('sucursal_id','=',$usuario->sucursal_id)
                    ->where('fecha','=',$hoy)->get();
        
            if(sizeof($ventas)){
                $desc = Desc_venta::where('venta_id','=',$ventas[0]->id)->get();

                if(sizeof($desc)){
                    foreach($desc as $ep=>$det)
                    {
                        $cantidades+=$det->cantidad;
                    }
                }
            }
            
        }
        else{
            $ventas = Venta::where('fecha','=',$hoy)->get();
            $ventas[0]->total = Venta::where('fecha','=',$hoy)->sum('total');

            foreach($ventas as $ep=>$venta){
                $desc = Desc_venta::where('venta_id','=',$venta->id)->get();

                if(sizeof($desc)){
                    foreach($desc as $ep=>$det)
                    {
                        $cantidades+=$det->cantidad;
                    }
                }
            }
        }
        
        return['ventas'=>$ventas[0]->total,'cantidades'=>$cantidades];
    }

    public function forecastAdmn(Request $request){
        $fecha = Carbon::now();
        $four_weeks = Carbon::now()->subDays(28);
        $total=0;
        $buscar = $request->buscar;
        $cant = 0;

        if($buscar == ''){
            $inventario=Inventario::whereMonth('fecha',Carbon::now()->month)
            ->whereYear('fecha',Carbon::now()->year)
            ->where('activo','=',1)
            ->orderBy('fecha','desc')->sum('total');

            $ventas = Venta::whereMonth('fecha',$fecha->month)->sum('total');

            $ventas_30 = Venta::where('fecha', '>=', $four_weeks)->get();

            if(sizeof($ventas_30)){
                foreach($ventas_30 as $ep=>$det)
                {
                    $cant=Desc_venta::where('venta_id','=',$det->id)->sum('cantidad');
                    $total+=$cant;
                }
            }

        }
        else{
            $usuario = User::findOrFail($buscar);
            
            $inventario=Inventario::whereMonth('fecha',Carbon::now()->month)
            ->whereYear('fecha',Carbon::now()->year)
            ->where('sucursal_id','=',$usuario->sucursal_id)
            ->where('activo','=',1)
            ->orderBy('fecha','desc')->sum('total');

            $ventas = Venta::whereMonth('fecha',$fecha->month)->where('sucursal_id','=',$usuario->sucursal_id)->sum('total');

            $ventas_30 = Venta::where('fecha', '>=', $four_weeks)->where('sucursal_id','=',$usuario->sucursal_id)->get();

            if(sizeof($ventas_30)){
                foreach($ventas_30 as $ep=>$det)
                {
                    $cant=Desc_venta::where('venta_id','=',$det->id)->sum('cantidad');
                    $total+=$cant;
                }
            }

        }

        
        return[
                'ventas'=>$ventas,
                'hoy'=>$fecha->day, 
                'diasMes'=>$fecha->daysInMonth,
                'ventas_30'=>$cant,
                'inventario'=>$inventario
            ];
    }

    public function wosDetalladoAdmn(Request $request){
        $fecha = Carbon::now();
        $four_weeks = Carbon::now()->subDays(28);
        $buscar = $request->buscar;

        if($buscar == ''){
            $inventario=Inventario::whereMonth('fecha',Carbon::now()->month)
            ->whereYear('fecha',Carbon::now()->year)
            ->where('activo','=',1)
            ->orderBy('fecha','desc')->get();

            $detInventario=Detalle_inventario::join('equipos','detalle_inventarios.equipo_id','=','equipos.id')
            ->select( 'detalle_inventarios.cantidad','equipos.modelo','detalle_inventarios.inventario_id','detalle_inventarios.equipo_id')
            ->where('detalle_inventarios.inventario_id','=',$inventario[0]->id)->get();

            $ventas_30 = Venta::where('fecha', '>=', $four_weeks)->get();

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

        }
        else{
            $usuario = User::findOrFail($buscar);
            
            $inventario=Inventario::whereMonth('fecha',Carbon::now()->month)
            ->whereYear('fecha',Carbon::now()->year)
            ->where('sucursal_id','=',$usuario->sucursal_id)
            ->orderBy('fecha','desc')->get();

            $detInventario=Detalle_inventario::join('equipos','detalle_inventarios.equipo_id','=','equipos.id')
            ->select( 'detalle_inventarios.cantidad','equipos.modelo','detalle_inventarios.inventario_id','detalle_inventarios.equipo_id')
            ->where('detalle_inventarios.inventario_id','=',$inventario[0]->id)->get();

            $ventas_30 = Venta::where('fecha', '>=', $four_weeks)->where('sucursal_id','=',$usuario->sucursal_id)->get();

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
        }
        

        return ['detalle_inventario'=>$detInventario];

    }


}

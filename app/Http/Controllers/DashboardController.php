<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Persona;
use Auth;
use App\Venta;
use App\Corte;
use App\Desc_venta;
use App\Cuota;
use App\Sucursal;
use App\Inventario;
use App\Detalle_inventario;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function alcance(Request $request){
        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;

        if(Auth::user()->rol_id != 1){
            $cuota = Cuota::where('user_id','=',Auth::user()->id)
            ->where('month','=',Carbon::now()->month)
            ->where('year','=',Carbon::now()->year)->get();
        }
        else{
            if($buscar ==''){
                $cuota = Cuota::join('users','cuotas.user_id','=','users.id')
                ->where('cuotas.month','=',Carbon::now()->month)
                ->where('cuotas.year','=',Carbon::now()->year)
                ->where('users.condicion','=',1)->get();
            }
            else{
                $cuota = Cuota::where('user_id','=',$buscar)
                ->where('month','=',Carbon::now()->month)
                ->where('year','=',Carbon::now()->year)->get();
            }
        }
        

        return['cuota'=>$cuota,'hoy'=>$fecha->day,'diasMes'=>$fecha->daysInMonth];
    }

    public function ventaDia(Request $request){
        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;

        

        if(Auth::user()->rol_id != 1){
            $corte = Corte::where('sucursal_id','=',Auth::user()->sucursal_id)->where('fecha','=',$hoy)->sum('total');
            $venta = Venta::where('sucursal_id','=',Auth::user()->sucursal_id)->where('fecha','=',$hoy)->sum('total');
        }
        else{
            if($buscar==''){
                $corte = Corte::join('sucursales','cortes.sucursal_id','=','sucursales.id')
                    ->where('cortes.fecha','=',$hoy)
                    ->where('sucursales.condicion','=',1)->sum('cortes.total');

                $venta = Venta::join('sucursales','ventas.sucursal_id','=','sucursales.id')
                ->where('ventas.fecha','=',$hoy)
                ->where('sucursales.condicion','=',1)->sum('ventas.total');
            }
            else{
                $user = User::findOrFail($buscar);
                $corte = Corte::where('sucursal_id','=',$user->sucursal_id)->where('fecha','=',$hoy)->sum('total');
                $venta = Venta::where('sucursal_id','=',$user->sucursal_id)->where('fecha','=',$hoy)->sum('total');
            }
            
        }
        

        return['corte'=>$corte,'venta'=>$venta];
    }

    
}

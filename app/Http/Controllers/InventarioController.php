<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inventario;
use App\Detalle_inventario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

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
            
            'inventarios'=> $inventarios , 'hoy'=>$hoy
        ];
    }

    public function store(Request $request)
    {
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
}

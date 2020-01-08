<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Corte;
use App\Desc_corte;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

class CorteController extends Controller
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
                    $cortes = Corte::join('sucursales','cortes.sucursal_id','=','sucursales.id')
                    ->select('cortes.id','cortes.fecha','cortes.total',
                            'sucursales.pv','sucursales.cadena')
                    ->whereMonth('cortes.fecha',Carbon::now()->month)
                    ->whereYear('cortes.fecha',Carbon::now()->year)
                    ->orderBy('cortes.fecha','asc')->paginate(31);
                }
                else{
                    $cortes = Corte::join('sucursales','cortes.sucursal_id','=','sucursales.id')
                    ->select('cortes.id','cortes.fecha','cortes.total',
                            'sucursales.pv','sucursales.cadena')
                    ->whereMonth('cortes.fecha',Carbon::now()->month)
                    ->whereYear('cortes.fecha',Carbon::now()->year)
                    ->where('cortes.sucursal_id','=',$sucursal_id)
                    ->orderBy('cortes.fecha','asc')->paginate(31);
                }
            }
            else{
                if($sucursal_id == ''){
                    $cortes = Corte::join('sucursales','cortes.sucursal_id','=','sucursales.id')
                    ->select('cortes.id','cortes.fecha','cortes.total',
                            'sucursales.pv','sucursales.cadena')
                    ->whereBetween('cortes.fecha', [$buscar, $buscar2])
                    ->orderBy('cortes.fecha','asc')->paginate(31);
                }
                else{
                    $cortes = Corte::join('sucursales','cortes.sucursal_id','=','sucursales.id')
                    ->select('cortes.id','cortes.fecha','cortes.total',
                            'sucursales.pv','sucursales.cadena')
                    ->whereBetween('cortes.fecha', [$buscar, $buscar2])
                    ->where('cortes.sucursal_id','=',$sucursal_id)
                    ->orderBy('cortes.fecha','asc')->paginate(31);
                }
            }
            
        }
        else{
            if($buscar == ''){
                $cortes = Corte::join('sucursales','cortes.sucursal_id','=','sucursales.id')
                ->select('cortes.id','cortes.fecha','cortes.total',
                        'sucursales.pv','sucursales.cadena')
                ->whereMonth('cortes.fecha',Carbon::now()->month)
                ->whereYear('cortes.fecha',Carbon::now()->year)
                ->where('cortes.user_id','=',Auth::user()->id)
                ->orderBy('cortes.fecha','asc')->paginate(31);
            }
            else{
                $cortes = Corte::join('sucursales','cortes.sucursal_id','=','sucursales.id')
                ->select('cortes.id','cortes.fecha','cortes.total',
                        'sucursales.pv','sucursales.cadena')
                ->whereBetween('cortes.fecha', [$buscar, $buscar2])
                ->where('cortes.user_id','=',Auth::user()->id)
                ->orderBy('cortes.fecha','asc')->paginate(31);
            } 
        }
        

        return [ 
            'pagination' => [
                'total'         => $cortes->total(),
                'current_page'  => $cortes->currentPage(),
                'per_page'      => $cortes->perPage(),
                'last_page'     => $cortes->lastPage(),
                'from'          => $cortes->firstItem(),
                'to'            => $cortes->lastItem(),
            ],
            'cortes'=> $cortes , 'hoy'=>$hoy 
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

        $mes = new Carbon($request->fecha);
        
        try{
            DB::beginTransaction(); 
            $corte = new Corte();
            $corte->sucursal_id = $sucursal_id;
            $corte->fecha = $request->fecha;
            $corte->user_id = $user_id;
            $corte->hora = $hora;
            $corte->save();
 
            $equipos = $request->data;//Array de detalles
            //Recorro todos los elementos
 
            foreach($equipos as $ep=>$det)
            {
                if($det['cant'] != 0 || $det['cant']!= '0'){

                    $desc = new Desc_corte();
                    $desc->corte_id = $corte->id;
                    $desc->equipo_id = $det['id'];
                    $desc->cantidad = $det['cant'];
                    $desc->total = $det['cant'] * $det['precio'];
    
                    if($det['tipo'] == 0){
                        $smart+=$desc->total;
                    }                    
                    else{
                        $premium+=$desc->total;
                    }
                    $desc->save();

                }
            }

            $corte->total = $premium + $smart;
            $corte->save();
 
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function indexDetalle( Request $request ){

        $cortes = Desc_Corte::join('equipos','desc_cortes.equipo_id','=','equipos.id')
                ->select( 'desc_cortes.cantidad','desc_cortes.total','equipos.modelo','equipos.tipo', 'desc_cortes.corte_id')
                ->where('desc_cortes.corte_id','=',$request->id)
                ->orderBy('equipos.modelo','asc')->get();

        return [ 'cortes'=> $cortes ];
    }
}

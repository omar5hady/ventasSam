<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

class CuotaController extends Controller
{
    public function index(Request $request){

        $month = $request->month;
        $user_id = $request->user_id;
        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $mes = $fecha->month;
        $anio = $fecha->year;

        if(Auth::user()->rol_id == 2){
            if($month == ''){
                $cuotas = Cuota::where('user_id','=',Auth::user()->id)
                        ->where('month','=',$mes)
                        ->where('year','=',$anio)->paginate(8);
            }
            else{
                $cuotas = Cuota::where('user_id','=',Auth::user()->id)
                        ->where('month','=',$month)
                        ->where('year','=',$anio)->paginate(8);
            }
            
        }
        else{
            if($month == ''){
                if($user_id == ''){
                    $cuotas = Cuota::join('users','cuotas.user_id','=','users.id')
                        ->select('cuotas.id','cuotas.premium','cuotas.smart','cuotas.premium_real',
                            'cuotas.smart_real','cuotas.qty_premium','cuotas.qty_smart','cuotas.month',
                            'cuotas.year','cuotas.qty_smart_real','qty_premium_real','users.usuario')
                        ->where('month','=',$mes)
                        ->where('year','=',$anio)
                        ->where('users.condicion','=',1)->paginate(8);
                }
                else{
                    $cuotas = Cuota::join('users','cuotas.user_id','=','users.id')
                        ->select('cuotas.id','cuotas.premium','cuotas.smart','cuotas.premium_real',
                            'cuotas.smart_real','cuotas.qty_premium','cuotas.qty_smart','cuotas.month',
                            'cuotas.year','cuotas.qty_smart_real','qty_premium_real','users.usuario')
                        ->where('month','=',$mes)
                        ->where('year','=',$anio)
                        ->where('user_id','=',$user_id)->paginate(8);
                }
                
            }
            else{
                if($user_id == ''){
                    $cuotas = Cuota::join('users','cuotas.user_id','=','users.id')
                        ->select('cuotas.id','cuotas.premium','cuotas.smart','cuotas.premium_real',
                            'cuotas.smart_real','cuotas.qty_premium','cuotas.qty_smart','cuotas.month',
                            'cuotas.year','cuotas.qty_smart_real','qty_premium_real','users.usuario')
                        ->where('month','=',$month)
                        ->where('year','=',$anio)
                        ->where('users.condicion','=',1)->paginate(8);
                }
                else{
                    $cuotas = Cuota::join('users','cuotas.user_id','=','users.id')
                        ->select('cuotas.id','cuotas.premium','cuotas.smart','cuotas.premium_real',
                            'cuotas.smart_real','cuotas.qty_premium','cuotas.qty_smart','cuotas.month',
                            'cuotas.year','cuotas.qty_smart_real','qty_premium_real','users.usuario')
                        ->where('month','=',$month)
                        ->where('user_id','=',$user_id)
                        ->where('year','=',$anio)->paginate(8);
                }
                 
            }
        }

        return[
            'pagination' => [
                'total'         => $cuotas->total(),
                'current_page'  => $cuotas->currentPage(),
                'per_page'      => $cuotas->perPage(),
                'last_page'     => $cuotas->lastPage(),
                'from'          => $cuotas->firstItem(),
                'to'            => $cuotas->lastItem(),
            ],
            'cuotas' => $cuotas,'mes'=>$mes
        ];
    }

    public function store(Request $request){
        if($request->vendedor_id == ''){
            $user = Auth::user()->id;
        }
        else{
            $user = $request->vendedor_id;
        }
        $cuota = new Cuota();
        $cuota->user_id = $user;
        $cuota->premium = $request->premium;
        $cuota->smart = $request->smart;
        $cuota->qty_premium = $request->qty_premium;
        $cuota->qty_smart = $request->qty_smart;
        $cuota->month = Carbon::now()->month;
        $cuota->year = Carbon::now()->year;
        $cuota->save();
    }

    public function update(Request $request){
        $cuota = Cuota::findOrFail($request->id);
        $cuota->premium = $request->premium;
        $cuota->smart = $request->smart;
        $cuota->qty_premium = $request->qty_premium;
        $cuota->qty_smart = $request->qty_smart;
        $cuota->save();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Persona;
use Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
 
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $personas = User::join('personas','users.id','=','personas.id')
            ->join('roles','users.rol_id','=','roles.id')
            ->join('sucursales','users.sucursal_id','=','sucursales.id')
            ->select('personas.id','personas.nombre','personas.apellidos','personas.celular',
                'users.usuario','users.password','users.sucursal_id','sucursales.pv', 'sucursales.cadena',
                'users.condicion','users.rol_id','roles.nombre as rol')
            ->orderBy('users.condicion', 'desc')
            ->orderBy('personas.id', 'desc')
            ->paginate(8);
        }
        else{
            if($criterio == 'personas.nombre'){
                $personas = User::join('personas','users.id','=','personas.id')
                ->join('roles','users.rol_id','=','roles.id')
                ->join('sucursales','users.sucursal_id','=','sucursales.id')
                ->select('personas.id','personas.nombre','personas.apellidos','personas.celular',
                    'users.usuario','users.password','users.sucursal_id','sucursales.pv', 'sucursales.cadena',
                    'users.condicion','users.rol_id','roles.nombre as rol')
                ->where(DB::raw("CONCAT(personas.nombre,' ',personas.apellidos)"), 'like', '%'. $buscar . '%')
                ->orderBy('users.condicion', 'desc')
                ->orderBy('personas.id', 'desc')
                ->paginate(8);
            }
            else{
                $personas = User::join('personas','users.id','=','personas.id')
                ->join('roles','users.rol_id','=','roles.id')
                ->join('sucursales','users.sucursal_id','=','sucursales.id')
                ->select('personas.id','personas.nombre','personas.apellidos','personas.celular',
                    'users.usuario','users.password','users.sucursal_id','sucursales.pv', 'sucursales.cadena',
                    'users.condicion','users.rol_id','roles.nombre as rol')
                ->where($criterio, 'like', '%'. $buscar . '%')
                ->orderBy('users.condicion', 'desc')
                ->orderBy('personas.id', 'desc')
                ->paginate(8);
            }
        }

        return [
            'pagination' => [
                'total'        => $personas->total(),
                'current_page' => $personas->currentPage(),
                'per_page'     => $personas->perPage(),
                'last_page'    => $personas->lastPage(),
                'from'         => $personas->firstItem(),
                'to'           => $personas->lastItem(),
            ],
            'personas' => $personas
        ];
    }

    public function store(Request $request)
    {
        if(!$request->ajax())return redirect('/');
         
        try{
            DB::beginTransaction();
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->apellidos = $request->apellidos;
            $persona->celular = $request->celular;
            $persona->save();
 
            $user = new User();
            $user->usuario = $request->usuario;
            $user->password = bcrypt( $request->password);
            $user->condicion = '1';
            $user->rol_id = $request->rol_id;     
            $user->sucursal_id = $request->sucursal_id;      
 
            $user->id = $persona->id;
            $user->save();

            DB::commit();

            
 
        } catch (Exception $e){
            DB::rollBack();
        }         
         
    }

    public function update(Request $request)
    {
        if(!$request->ajax())return redirect('/');

        try{
            DB::beginTransaction();
 
            //Buscar primero el proveedor a modificar
            $user = User::findOrFail($request->id);
 
            $Persona = Persona::findOrFail($request->id);
 
            $Persona->nombre = $request->nombre;
            $Persona->apellidos = $request->apellidos;
            $Persona->celular = $request->celular;
            $Persona->save();
            
             
            $user->usuario = $request->usuario;
            $user->password = bcrypt( $request->password);
            $user->condicion = '1';
            $user->rol_id = $request->rol_id;
            $user->sucursal_id = $request->sucursal_id;
            $user->save();

 
            DB::commit();
 
        } catch (Exception $e){
            DB::rollBack();
        }
 
    }

    public function activar(Request $request){
        $user = User::findOrFail($request->id);
        $user->condicion = 1;
        $user->save();
    }

    public function desactivar(Request $request){
        $user = User::findOrFail($request->id);
        $user->condicion = 0;
        $user->save();
    }

    public function selectVendedor(Request $request){
        $personas = User::join('personas','users.id','=','personas.id')
            ->select('personas.id','personas.nombre','personas.apellidos','users.rol_id','users.condicion')
            ->where('users.rol_id','=',2)
            ->where('users.condicion','=',1)
            ->orderBy('personas.nombre', 'desc')
            ->orderBy('personas.apellidos', 'desc')
            ->get();

        return ['personas'=>$personas];
    }

    public function excel(Request $request){

        $personas = User::get();

        return Excel::create('Asesores', function($excel) use ($personas){
            $excel->sheet('asesores', function($sheet) use ($personas){
                
                $sheet->row(1, [
                    'Usuario', 'Condicion','Rol' ,'Sucursal'
                ]);


                $sheet->cells('A1:D1', function ($cells) {
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

                
                $cont=1;

                foreach($personas as $index => $persona) {

                    $sheet->row($index+2, [
                        $persona->usuario, 
                        $persona->condicion, 
                        $persona->rol_id, 
                        $persona->sucursal_id,
                    ]);	
                }
                $num='A1:F' . $cont;
                $sheet->setBorder($num, 'thin');
            });
        }
        
        )->download('xlsx');
    }

}

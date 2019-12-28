<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;

class RolController extends Controller
{
    public function index(Request $request){
        $buscar = $request->buscar;
        if($buscar==''){
            $roles = Rol::orderBy('condicion','DESC')->orderBy('nombre','ASC')->paginate(15);
        }
        else{
            $roles = Rol::where('nombre','like','%'.$buscar.'%')
                ->orderBy('condicion','DESC')->orderBy('nombre','ASC')->paginate(15);
        }

        return [
            'pagination' => [
                'total'         => $roles->total(),
                'current_page'  => $roles->currentPage(),
                'per_page'      => $roles->perPage(),
                'last_page'     => $roles->lastPage(),
                'from'          => $roles->firstItem(),
                'to'            => $roles->lastItem(),
            ],
            'roles' => $roles
        ];
    }
}

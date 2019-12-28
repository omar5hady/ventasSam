@extends('principal')
@section('contenido')

@if(Auth::check())
    <template v-if="menu==1">
        <equipo-component></equipo-component>
    </template>
    <template v-if="menu==2">
        <sucursal-component></sucursal-component>
    </template>
    <template v-if="menu==3">
        <user-component></user-component>
    </template>
@endif



@endsection
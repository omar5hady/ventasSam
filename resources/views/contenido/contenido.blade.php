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

    <template v-if="menu==4">
        <share-component></share-component>
    </template>

    <template v-if="menu==5">
        <ventas-component rol-id="{{Auth::user()->rol_id}}"></ventas-component>
    </template>

    <template v-if="menu==6">
        <cuota-component rol-id="{{Auth::user()->rol_id}}"></cuota-component>
    </template>

    <template v-if="menu==7">
        <inventario-component rol-id="{{Auth::user()->rol_id}}"></inventario-component>
    </template>

    <template v-if="menu==8">
        <cortes-component rol-id="{{Auth::user()->rol_id}}"></cortes-component>
    </template>

    <template v-if="menu==9">
        <share-admin></share-admin>
    </template>
@endif



@endsection
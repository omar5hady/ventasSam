<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li @click="menu=0" class="nav-item">
                <a class="nav-link active" href="#"><i class="icon-speedometer"></i> Escritorio</a>
            </li>
            <li class="nav-title">
                Menú
            </li>
            @if(Auth::user()->rol_id == 1)
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-superpowers "></i> Administracion</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=1" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-mobile-phone fa-lg"></i> Equipos</a>
                    </li>
                    <li @click="menu=2" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-institution"></i> Puntos de venta</a>
                    </li>
                </ul>
            </li>
            @endif
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-wallet"></i> Samsung</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=5" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-basket"></i> Ventas</a>
                    </li>
                    <li @click="menu=8" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-basket"></i> Cortes</a>
                    </li>
                    <li @click="menu=6" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-wallet"></i> Cuota</a>
                    </li>
                    <li @click="menu=7" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-book"></i> Inventario</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-hdd-o"></i> Herramientas</a>
                <ul class="nav-dropdown-items">
                    @if(Auth::user()->rol_id == 1)
                    <li @click="menu=9" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-share-alt"></i> Basicos</a>
                    </li>
                    @elseif(Auth::user()->rol_id == 2)
                    <li @click="menu=4" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-share-alt"></i> Basicos</a>
                    </li>
                    @endif
                    <li @click="menu=10" class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-mixcloud"></i> Compartidos</a>
                    </li>
                </ul>
            </li>
            @if(Auth::user()->rol_id == 1)
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Acceso</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item" @click="menu=3">
                        <a class="nav-link" href="#"><i class="icon-user"></i> Usuarios</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user-following"></i> Roles</a>
                    </li> --}}
                </ul>
            </li>
            @endif
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
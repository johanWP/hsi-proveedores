<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        @if (! Auth::guest())
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ Gravatar::get($user->email) }}" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->name }}</p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('adminlte_lang::message.online') }}</a>
                </div>
            </div>
        @endif

        <!-- search form (Optional) -->
        {{--<form action="#" method="get" class="sidebar-form">--}}
            {{--<div class="input-group">--}}
                {{--<input type="text" name="q" class="form-control" placeholder="{{ trans('adminlte_lang::message.search') }}..."/>--}}
              {{--<span class="input-group-btn">--}}
                {{--<button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>--}}
              {{--</span>--}}
            {{--</div>--}}
        {{--</form>--}}
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">{{-- trans('adminlte_lang::message.header') --}}MENU</li>
            <!-- Optionally, you can add icons to the links -->
            @if(! Auth::user()->hasRole('proveedor'))
                <li class="active"><a href="{{ url('home') }}"><i class='fa fa-home'></i>
                        <span>{{ trans('adminlte_lang::message.home') }}</span></a>
                </li>
            @endif

            @if(Auth::user()->can('ver_otros_usuarios'))
                <li><a href="{{ url('/usuarios') }}"><i class='fa fa-users'></i> <span>Usuarios</span></a></li>
            @endif

            @if(Auth::user()->can('dar_permisos'))
            <li class="treeview">
                <a href="#"><i class='fa fa-key'></i> <span>Roles & Permisos</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/roles') }}"><span>Roles</span></a></li>
                    <li><a href="{{ url('/permisos') }}"><span>Permisos</span></a></li>
                </ul>
            </li>
            @endif

            @if(Auth::user()->can('ver_pagos_otros'))
            <li class="treeview">
                <a href="#"><i class='fa fa-usd'></i>
                    <span>Pagos</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/pagos/todos') }}">Ver Todos</a></li>
                    <li><a href="{{ url('/pagos') }}">Mis Pagos</a></li>
                </ul>
            </li>
            @else
                <li><a href="{{ url('/pagos') }}"><i class='fa fa-usd'></i> <span>Mis Pagos</span></a></li>
            @endif
            {{--<li><a href="#"><i class='fa fa-file-text'></i> <span>Archivos de Pagos</span></a></li>--}}
            @if(Auth::user()->can('generar_archivo_de_pagos'))
            <li class="treeview">
                <a href="#"><i class='fa fa-file-text'></i>
                    <span>Archivos de Pagos</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/banco/galicia">Banco Galicia</a></li>
                    {{--<li><a href="#">{{ trans('adminlte_lang::message.linklevel2') }}</a></li>--}}
                </ul>
            </li>
            @endif
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-6">
                    <h1>
                        Tipos de ahorros
                        <small>Ahorros</small>
                    </h1>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
                        <li class="breadcrumb-item active">Tipos de ahorros</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @if (Session::has('message'))
            <div class="alert alert-success alert-dismissible" data-closable>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
               {{ Session::get('message') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <a href="{{ url('tipoCuotaAhorros/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
            </div>
        </div>
        <br>
        <div class="container-fluid">
            <div class="card card-{{ $tiposCuotasAhorros->total()?'primary':'danger' }} card-outline">
                <div class="card-header with-border">
                    <h3 class="card-title">Tipo de ahorros</h3>
                </div>
                <div class="card-body">
                    {!! Form::model(Request::only('name', 'tipo_ahorro', 'estado'), ['url' => 'tipoCuotaAhorros', 'method' => 'GET', 'role' => 'search']) !!}
                    <div class="row">
                        <div class="col-md-5 col-sm-12">
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
                        </div>
                        <div class="col-md-3 col-sm-12">
                            {!! Form::select('tipo_ahorro', ['VOLUNTARIO' => 'Voluntario', 'PROGRAMADO' => 'Programado'], null, ['class' => 'form-control', 'placeholder' => 'Tipo ahorro']); !!}
                        </div>
                        <div class="col-md-3 col-sm-12">
                            {!! Form::select('estado', [true => 'Activa', false => 'Inactiva'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
                        </div>
                        <div class="col-md-1 col-sm-12">
                            <button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    @if(!$tiposCuotasAhorros->total())
                        <p>
                            <div class="row">
                                <div class="col-md-12">
                                    No se encontraron tipos de ahorros <a href="{{ url('tipoCuotaAhorros/create') }}" class="btn btn-outline-primary btn-sm">crear una nuevo</a>
                                </div>
                            </div>
                        </p>
                    @else
                        <br>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Tipo ahorro</th>
                                        <th>Cuenta</th>
                                        <th class="text-center">Tasa E.A.</th>
                                        <th class="text-center">Capitalización<br>simultanea</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiposCuotasAhorros as $cuota)
                                        <tr>
                                            <td>{{ $cuota->codigo }}</td>
                                            <td>{{ $cuota->nombre }}</td>
                                            <td>{{ $cuota->tipo_ahorro }}</td>
                                            <td>{{ $cuota->cuenta->full }}</td>
                                            <td class="text-right">{{ number_format($cuota->tasa, 0) }}%</td>
                                            <td class="text-center">
                                                <span class="badge badge-pill badge-{{ $cuota->capitalizacion_simultanea ? 'success' : 'danger' }}">
                                                    {{ $cuota->capitalizacion_simultanea ? 'Sí' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-{{ $cuota->esta_activa ? 'success' : 'danger' }}">
                                                    {{ $cuota->esta_activa ? 'ACTIVA' : 'INACTIVA' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('tipoCuotaAhorroEdit', $cuota->id) }}" class="btn btn-outline-info btn-sm" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12 text-center">
                            {!! $tiposCuotasAhorros->appends(Request::only('name', 'tipo_ahorro', 'estado'))->render() !!}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="badge badge-pill badge-{{ $tiposCuotasAhorros->total()?'primary':'danger' }}">{{ $tiposCuotasAhorros->total() }}</span> elementos.
                </div>
            </div>
        </div>
    </section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
    $(window).keydown(function(event) {
        if(event.altKey && event.keyCode == 78) {
            window.location.href = "{{ url('tipoCuotaAhorros/create') }}";
            event.preventDefault();
        }
    });
</script>
@endpush

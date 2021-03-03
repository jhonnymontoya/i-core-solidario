@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-6">
                    <h1>
                        Extracto Social
                        <small>Reportes</small>
                    </h1>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#"> Reportes</a></li>
                        <li class="breadcrumb-item active">Extracto Social</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @if ($errors->count())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4>Error!</h4>
                <p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
            </div>
        @endif
        {!! Form::open(['url' => 'extractoSocial', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
        <div class="container-fluid">
            <div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
                <div class="card-header with-border">
                    <h3 class="card-title">Crear nueva configuración de extracto social</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('anio') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Año</label>
                                {!! Form::text('anio', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Año', 'autofocus']) !!}
                                @if ($errors->has('anio'))
                                    <div class="invalid-feedback">{{ $errors->first('anio') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('fecha_inicio_socio_visible') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Fecha inicio visible para socio</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                    {!! Form::text('fecha_inicio_socio_visible', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
                                    @if ($errors->has('fecha_inicio_socio_visible'))
                                        <div class="invalid-feedback">{{ $errors->first('fecha_inicio_socio_visible') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('fecha_fin_socio_visible') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Fecha fin visible para socio</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                    {!! Form::text('fecha_fin_socio_visible', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
                                    @if ($errors->has('fecha_fin_socio_visible'))
                                        <div class="invalid-feedback">{{ $errors->first('fecha_fin_socio_visible') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('tasa_promedio_ahorros_externa') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Tasa promedio ahorros externa</label>
                                <div class="input-group">
                                    {!! Form::text('tasa_promedio_ahorros_externa', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Tasa promedio ahorros externa', 'data-maskMoney', 'data-allowzero' => 'false', 'data-allownegative' => 'false', 'data-precision' => 2]) !!}
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @if ($errors->has('tasa_promedio_ahorros_externa'))
                                        <div class="invalid-feedback">{{ $errors->first('tasa_promedio_ahorros_externa') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('tasa_promedio_creditos_externa') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Tasa promedio créditos externa</label>
                                <div class="input-group">
                                    {!! Form::text('tasa_promedio_creditos_externa', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Tasa promedio créditos externa', 'data-maskMoney', 'data-allowzero' => 'false', 'data-allownegative' => 'false', 'data-precision' => 2]) !!}
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @if ($errors->has('tasa_promedio_creditos_externa'))
                                        <div class="invalid-feedback">{{ $errors->first('tasa_promedio_creditos_externa') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('gasto_social_total') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Gasto social total</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    {!! Form::text('gasto_social_total', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Gasto social total', 'data-maskMoney', 'data-allowzero' => 'false', 'data-allownegative' => 'false', 'data-precision' => 0]) !!}
                                    @if ($errors->has('gasto_social_total'))
                                        <div class="invalid-feedback">{{ $errors->first('gasto_social_total') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('gasto_social_individual') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Gasto social individual</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    {!! Form::text('gasto_social_individual', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Gasto social individual', 'data-maskMoney', 'data-allowzero' => 'false', 'data-allownegative' => 'false', 'data-precision' => 0]) !!}
                                    @if ($errors->has('gasto_social_individual'))
                                        <div class="invalid-feedback">{{ $errors->first('gasto_social_individual') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('mensaje_general') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Mensaje general</label>
                                {!! Form::textarea('mensaje_general', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Mensaje general']) !!}
                                @if ($errors->has('mensaje_general'))
                                    <div class="invalid-feedback">{{ $errors->first('mensaje_general') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('mensaje_ahorros') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Mensaje ahorros</label>
                                {!! Form::textarea('mensaje_ahorros', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Mensaje ahorros']) !!}
                                @if ($errors->has('mensaje_ahorros'))
                                    <div class="invalid-feedback">{{ $errors->first('mensaje_ahorros') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('mensaje_creditos') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Mensaje créditos</label>
                                {!! Form::textarea('mensaje_creditos', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Mensaje créditos']) !!}
                                @if ($errors->has('mensaje_creditos'))
                                    <div class="invalid-feedback">{{ $errors->first('mensaje_creditos') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('mensaje_convenios') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Mensaje convenios</label>
                                {!! Form::textarea('mensaje_convenios', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Mensaje convenios']) !!}
                                @if ($errors->has('mensaje_convenios'))
                                    <div class="invalid-feedback">{{ $errors->first('mensaje_convenios') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('mensaje_inversion_social') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Mensaje inversión social</label>
                                {!! Form::textarea('mensaje_inversion_social', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Mensaje inversión social']) !!}
                                @if ($errors->has('mensaje_inversion_social'))
                                    <div class="invalid-feedback">{{ $errors->first('mensaje_inversion_social') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
                    <a href="{{ url('extractoSocial') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
    $(function(){
        $("input[name='tasa_promedio_ahorros_externa']").maskMoney('mask');
        $("input[name='tasa_promedio_creditos_externa']").maskMoney('mask');
        $("input[name='gasto_social_total']").maskMoney('mask');
        $("input[name='gasto_social_individual']").maskMoney('mask');
    });
</script>
@endpush

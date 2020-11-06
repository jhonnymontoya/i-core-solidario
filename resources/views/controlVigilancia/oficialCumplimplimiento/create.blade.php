@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-6">
                    <h1>
                        Oficial de cumplimiento
                        <small>SARLAFT</small>
                    </h1>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#"> SARLAFT</a></li>
                        <li class="breadcrumb-item active">Oficial de cumplimiento</li>
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
        <div class="container-fluid">
            <div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
                {!! Form::open(['url' => 'oficialCumplimiento', 'method' => 'post', 'role' => 'form']) !!}
                <div class="card-header with-border">
                    <h3 class="card-title">Crear nuevo oficial de cumplimiento</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('nombre') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Nombre</label>
                                {!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre del oficial de cumplimiento', 'autofocus']) !!}
                                @if ($errors->has('nombre'))
                                    <div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('email') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Correo electrónico</label>
                                {!! Form::email('email', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico del oficial de cumplimiento', 'autofocus']) !!}
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                @php
                                    $valid = $errors->has('emailcc') ? 'is-invalid' : '';
                                @endphp
                                <label class="control-label">Copia correo electrónico</label>
                                {!! Form::email('emailcc', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico de copia', 'autofocus']) !!}
                                @if ($errors->has('emailcc'))
                                    <div class="invalid-feedback">{{ $errors->first('emailcc') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
                    <a href="{{ url('oficialCumplimiento') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
@endpush

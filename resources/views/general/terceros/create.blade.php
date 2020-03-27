@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Terceros
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Terceros</li>
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
		{!! Form::open(['url' => 'tercero', 'method' => 'post', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Agregar nuevo tercero</h3>
				</div>
				<div class="card-body">
					<div class="row form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
							    <label class="control-label">Tipo de tercero</label>
							    <div>
							        @php
							            $valid = $errors->has('tipo_tercero') ? 'is-invalid' : '';
							            $tipoTercero = empty(old('tipo_tercero')) ? 'NATURAL' : old('tipo_tercero');
							        @endphp
							        <div class="btn-group btn-group-toggle" data-toggle="buttons">
							            <label class="btn btn-primary {{ $tipoTercero == 'NATURAL' ? 'active' : '' }}">
							                {!! Form::radio('tipo_tercero', 'NATURAL', ($tipoTercero == 'NATURAL' ? true : false), ['class' => [$valid]]) !!}Natural
							            </label>
							            <label class="btn btn-primary {{ $tipoTercero == 'JURÍDICA' ? 'active' : '' }}">
							                {!! Form::radio('tipo_tercero', 'JURÍDICA', ($tipoTercero == 'JURÍDICA' ? true : false ), ['class' => []]) !!}Jurídico
							            </label>
							        </div>
							        @if ($errors->has('tipo_tercero'))
							            <div class="invalid-feedback">{{ $errors->first('tipo_tercero') }}</div>
							        @endif
							    </div>
							</div>
						</div>
					</div>

					{{-- INICIO NATURAL --}}
					<div id="natural" style="display: {{ $tipoTercero == 'NATURAL' ? 'block' : 'none' }}">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    @php
								        $valid = $errors->has('nTipoIdentificacion') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Tipo identificación</label>
								    {!! Form::select('nTipoIdentificacion', $natural, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción', 'style' => 'width: 100%;']) !!}
								    @if ($errors->has('nTipoIdentificacion'))
								        <div class="invalid-feedback">{{ $errors->first('nTipoIdentificacion') }}</div>
								    @endif
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
								    @php
								        $valid = $errors->has('nNumeroIdentificacion') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Número de identificación</label>
								    {!! Form::number('nNumeroIdentificacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Numero de identificación', 'autofocus']) !!}
								    @if ($errors->has('nNumeroIdentificacion'))
								        <div class="invalid-feedback">{{ $errors->first('nNumeroIdentificacion') }}</div>
								    @endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
								    @php
								        $valid = $errors->has('nPrimerNombre') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Primer nombre</label>
								    {!! Form::text('nPrimerNombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Primer nombre', 'autofocus']) !!}
								    @if ($errors->has('nPrimerNombre'))
								        <div class="invalid-feedback">{{ $errors->first('nPrimerNombre') }}</div>
								    @endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								    @php
								        $valid = $errors->has('nSegundoNombre') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Otros nombres</label>
								    {!! Form::text('nSegundoNombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Otros nombres', 'autofocus']) !!}
								    @if ($errors->has('nSegundoNombre'))
								        <div class="invalid-feedback">{{ $errors->first('nSegundoNombre') }}</div>
								    @endif
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
								    @php
								        $valid = $errors->has('nPrimerApellido') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Primer apellido</label>
								    {!! Form::text('nPrimerApellido', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Primer apellido', 'autofocus']) !!}
								    @if ($errors->has('nPrimerApellido'))
								        <div class="invalid-feedback">{{ $errors->first('nPrimerApellido') }}</div>
								    @endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								    @php
								        $valid = $errors->has('nSegundoApellido') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Segundo apellido</label>
								    {!! Form::text('nSegundoApellido', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Segundo apellido', 'autofocus']) !!}
								    @if ($errors->has('nSegundoApellido'))
								        <div class="invalid-feedback">{{ $errors->first('nSegundoApellido') }}</div>
								    @endif
								</div>
							</div>
						</div>
					</div>
					{{-- FIN NATURAL --}}

					{{-- INICIO JURÍDICA --}}
					<div id="juridico" style="display: {{ $tipoTercero != 'NATURAL' ? 'block' : 'none' }}">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    @php
								        $valid = $errors->has('jTipoIdentificacion') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Tipo identificación</label>
								    {!! Form::select('jTipoIdentificacion', $juridico, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción', 'style' => 'width: 100%;']) !!}
								    @if ($errors->has('jTipoIdentificacion'))
								        <div class="invalid-feedback">{{ $errors->first('jTipoIdentificacion') }}</div>
								    @endif
								</div>
							</div>

							<div class="col-md-6">
								<div class="row">
									<div class="col-md-10">
										<div class="form-group">
										    @php
										        $valid = $errors->has('jNumeroIdentificacion') ? 'is-invalid' : '';
										    @endphp
										    <label class="control-label">Número de identificación</label>
										    {!! Form::text('jNumeroIdentificacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación', 'autofocus']) !!}
										    @if ($errors->has('jNumeroIdentificacion'))
										        <div class="invalid-feedback">{{ $errors->first('jNumeroIdentificacion') }}</div>
										    @endif
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group {{ ($errors->has('jNumeroIdentificacion')?'has-error':'') }}">
											<label class="control-label">
												DV
											</label>
											<br>
											<label class="dv">0</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
								    @php
								        $valid = $errors->has('jRazonSocial') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Razón social</label>
								    {!! Form::text('jRazonSocial', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Razón social']) !!}
								    @if ($errors->has('jRazonSocial'))
								        <div class="invalid-feedback">{{ $errors->first('jRazonSocial') }}</div>
								    @endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								    @php
								        $valid = $errors->has('jSigla') ? 'is-invalid' : '';
								    @endphp
								    <label class="control-label">Sigla</label>
								    {!! Form::text('jSigla', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Sigla']) !!}
								    @if ($errors->has('jSigla'))
								        <div class="invalid-feedback">{{ $errors->first('jSigla') }}</div>
								    @endif
								</div>
							</div>
						</div>
					</div>
					{{-- FIN JURÍDICA --}}
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tercero') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		$(".select2").select2();

		$("input[name='tipo_tercero']").change(function(event){
			var $valor = this.value;
			if($valor == 'NATURAL') {
				$("#juridico").hide();
				$("#natural").show();
			}
			else {
				$("#natural").hide();
				$("#juridico").show();
			}
		});

		$("input[name='jNumeroIdentificacion']").on('keyup keypress blur change focus', function(e){
			digitoVerificacion(this.value, "{{ url('tercero/dv') }}", $(".dv"));
		});
		digitoVerificacion($("input[name='jNumeroIdentificacion']").val(), "{{ url('tercero/dv') }}", $(".dv"));
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Terceros
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Terceros</li>
		</ol>
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
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Agregar nuevo tercero</h3>
					</div>
					<div class="box-body">
						<div class="row form-horizontal">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('tipo_tercero')?'has-error':'') }}">
									<label class="control-label col-md-2 col-sm-12">
										@if ($errors->has('tipo_tercero'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo de tercero
									</label>
									<div class="col-md-9 col-sm-12">
										@php
											$tipoTercero = 'NATURAL';
											$tipoTercero = empty(old('tipo_tercero')) ? $tipoTercero : old('tipo_tercero');
										@endphp
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary {{ $tipoTercero == 'NATURAL' ? 'active' : '' }}">
												{!! Form::radio('tipo_tercero', 'NATURAL', $tipoTercero == 'NATURAL' ? true : false) !!}Natural
											</label>
											<label class="btn btn-primary {{ $tipoTercero != 'NATURAL' ? 'active' : '' }}">
												{!! Form::radio('tipo_tercero', 'JURÍDICA', $tipoTercero != 'NATURAL' ? true : false ) !!}Jurídico
											</label>
										</div>
										@if ($errors->has('tipo_tercero'))
											<span class="help-block">{{ $errors->first('tipo_tercero') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						{{-- INICIO NATURAL --}}
						<div id="natural" style="display: {{ $tipoTercero == 'NATURAL' ? 'block' : 'none' }}">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('nTipoIdentificacion')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('nTipoIdentificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo identificación
										</label>
										{!! Form::select('nTipoIdentificacion', $natural, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una opción', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('nTipoIdentificacion'))
											<span class="help-block">{{ $errors->first('nTipoIdentificacion') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('nNumeroIdentificacion')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('nNumeroIdentificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Número de identificación
										</label>
										{!! Form::number('nNumeroIdentificacion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de identificación', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('nNumeroIdentificacion'))
											<span class="help-block">{{ $errors->first('nNumeroIdentificacion') }}</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('nPrimerNombre')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('nPrimerNombre'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Primer nombre
										</label>
										{!! Form::text('nPrimerNombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Primer nombre', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('nPrimerNombre'))
											<span class="help-block">{{ $errors->first('nPrimerNombre') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('nSegundoNombre')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('nSegundoNombre'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Otros nombres
										</label>
										{!! Form::text('nSegundoNombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Otros nombres', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('nSegundoNombre'))
											<span class="help-block">{{ $errors->first('nSegundoNombre') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('nPrimerApellido')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('nPrimerApellido'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Primer apellido
										</label>
										{!! Form::text('nPrimerApellido', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Primer apellido', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('nPrimerApellido'))
											<span class="help-block">{{ $errors->first('nPrimerApellido') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('nSegundoApellido')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('nSegundoApellido'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Segundo apellido
										</label>
										{!! Form::text('nSegundoApellido', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Segundo apellido', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('nSegundoApellido'))
											<span class="help-block">{{ $errors->first('nSegundoApellido') }}</span>
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
									<div class="form-group {{ ($errors->has('jTipoIdentificacion')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('jTipoIdentificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo identificación
										</label>
										{!! Form::select('jTipoIdentificacion', $juridico, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una opción', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('jTipoIdentificacion'))
											<span class="help-block">{{ $errors->first('jTipoIdentificacion') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-6">
									<div class="row">
										<div class="col-md-10">
											<div class="form-group {{ ($errors->has('jNumeroIdentificacion')?'has-error':'') }}">
												<label class="control-label">
													@if ($errors->has('jNumeroIdentificacion'))
														<i class="fa fa-times-circle-o"></i>
													@endif
													Número de identificación
												</label>
												{!! Form::text('jNumeroIdentificacion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de identificación']) !!}
												@if ($errors->has('jNumeroIdentificacion'))
													<span class="help-block">{{ $errors->first('jNumeroIdentificacion') }}</span>
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
									<div class="form-group {{ ($errors->has('jRazonSocial')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('jRazonSocial'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Razón social
										</label>
										{!! Form::text('jRazonSocial', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Razón social', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('jRazonSocial'))
											<span class="help-block">{{ $errors->first('jRazonSocial') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('jSigla')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('jSigla'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Sigla
										</label>
										{!! Form::text('jSigla', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Sigla', 'style' => 'width: 100%;']) !!}
										@if ($errors->has('jSigla'))
											<span class="help-block">{{ $errors->first('jSigla') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
						{{-- FIN JURÍDICA --}}
					</div>
					<div class="box-footer">
						{!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tercero') }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Afiliar socio
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Afiliar socio</li>
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (!empty($faltantes))
			<div class="alert alert-warning alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				Por favor completar los siguientes campos...<br>
				@foreach($faltantes as $faltante)
					<br>
					<label>{!! $faltante !!}</label>
				@endforeach
			</div>
		@endif
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($socio, ['url' => ['socio', $socio, 'afiliacion'], 'method' => 'put', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Afiliar socio</h3>
				</div>
				<div class="card-body">
					{{-- INICIO FILA --}}
					<div class="row">
						{{-- INICIO CAMPO --}}
						<div class="col-md-12">
							<h4>{{ $socio->tercero->nombre_completo }}</h4>
						</div>
						{{-- FIN CAMPO --}}
					</div>
					{{-- FIN FILA --}}
					<br>
					{{-- INICIO FILA --}}
					<div class="row form-horizontal">
						{{-- INICIO CAMPO --}}
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_afiliacion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha de afiliación</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_afiliacion', date('d/m/Y'), ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha_afiliacion'))
										<div class="invalid-feedback">{{ $errors->first('fecha_afiliacion') }}</div>
									@endif
								</div>
							</div>
						</div>
						{{-- FIN CAMPO --}}
						{{-- INICIO CAMPO --}}
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_antiguedad') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha de antigüedad</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_antiguedad', date('d/m/Y'), ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha_antiguedad'))
										<div class="invalid-feedback">{{ $errors->first('fecha_antiguedad') }}</div>
									@endif
								</div>
							</div>
						</div>
						{{-- FIN CAMPO --}}
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO FILA --}}
					<div class="row form-horizontal">
						{{-- INICIO CAMPO --}}
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('referido') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Referido por</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-child"></i>
										</span>
									</div>
									{!! Form::select('referido', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('referido'))
										<div class="invalid-feedback">{{ $errors->first('referido') }}</div>
									@endif
								</div>
							</div>
						</div>
						{{-- FIN CAMPO --}}
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO FILA --}}
					<div class="row">
						{{-- INICIO CAMPO --}}
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('comentario') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Comentario</label>
								<div>
									{!! Form::textarea('comentario', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Comentario']) !!}
									@if ($errors->has('comentario'))
										<div class="invalid-feedback">{{ $errors->first('comentario') }}</div>
									@endif
								</div>
							</div>
						</div>
						{{-- FIN CAMPO --}}
					</div>
					{{-- FIN FILA --}}
					<br>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Procesar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('socio') }}" class="btn btn-outline-danger">Cancelar</a>
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
<script type="text/javascript">
	$("select[name='referido']").selectAjax("{{ url('tercero/getTerceroConParametros') }}", {id:"{{ old('referido') }}", entidad: {{ $socio->tercero->entidad->id }}});
	$("input[name='fecha_afiliacion']").on('keyup keypress change', function(e){
			$("input[name='fecha_antiguedad']").val($(this).val());
	});
</script>
@endpush

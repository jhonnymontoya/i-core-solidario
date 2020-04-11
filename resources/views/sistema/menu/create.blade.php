@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Menús
						<small>Sistema</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Sistema</a></li>
						<li class="breadcrumb-item active">Menús</li>
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
				{!! Form::open(['url' => 'menu', 'method' => 'post', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Crear nuevo menú</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('padre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Menú padre</label>
								{!! Form::select('padre', $lista, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Menú padre']) !!}
								@if ($errors->has('padre'))
									<div class="invalid-feedback">{{ $errors->first('padre') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre del menú', 'required']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('ruta') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Ruta</label>
								{!! Form::text('ruta', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Ruta del menú']) !!}
								@if ($errors->has('ruta'))
									<div class="invalid-feedback">{{ $errors->first('ruta') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('icono') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Icono</label>
								{!! Form::text('icono', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Icono']) !!}
								@if ($errors->has('icono'))
									<div class="invalid-feedback">{{ $errors->first('icono') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('menu') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
	$(function(){
		$("input[name='nombre']").enfocar();
		$(".select2").select2();
	});
</script>
@endpush

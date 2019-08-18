@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Perfiles
			<small>sistema</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">sistema</a></li>
			<li class="active">Perfiles</li>
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
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			{!! Form::model($perfil, ['url' => ['perfil', $perfil], 'method' => 'PUT', 'role' => 'form']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Editar perfil</h3>

				<div class="card-tools pull-right">
					<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">

				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('entidad_id')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('entidad_id'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Entidad
							</label>
							{!! Form::select('entidad_id', $entidades, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una entidad', 'required']) !!}
							@if ($errors->has('entidad_id'))
								<span class="help-block">{{ $errors->first('entidad_id') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-10">
						<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre del perfil', 'required']) !!}
							@if ($errors->has('nombre'))
								<span class="help-block">{{ $errors->first('nombre') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('esta_activo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								¿Activo?
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary{{ $perfil->esta_activo?' active':'' }}">
									{!! Form::radio('esta_activo', '1', true) !!}Sí
								</label>
								<label class="btn btn-danger{{ !$perfil->esta_activo?' active':'' }}">
									{!! Form::radio('esta_activo', '0', false) !!}No
								</label>
							</div>
							@if ($errors->has('esta_activo'))
								<span class="help-block">{{ $errors->first('esta_activo') }}</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('descripcion'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Descripción
							</label>
							{!! Form::textarea('descripcion', null, ['class' => 'form-control', 'placeholder' => 'Descripción']) !!}
							@if ($errors->has('descripcion'))
								<span class="help-block">{{ $errors->first('descripcion') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('menus')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('menus'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Menús
							</label>
							{!! Form::select('menus[]', $menu_list, $perfil->menus->pluck('id')->toArray(), ['class' => 'form-control', 'multiple', 'style' => 'height:200px;', 'required']) !!}
							@if ($errors->has('menus'))
								<span class="help-block">{{ $errors->first('menus') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('perfil') }}" class="btn btn-danger pull-right">Cancelar</a>
			</div>
			{!! Form::close() !!}
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

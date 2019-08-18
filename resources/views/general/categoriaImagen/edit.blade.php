@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Categoría de imágenes
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Categoría de imágenes</li>
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
			{!! Form::model($categoriaImagen, ['url' => ['categoriaImagen', $categoriaImagen], 'method' => 'PUT', 'role' => 'form']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Editar categoría de imagen</h3>

				<div class="card-tools pull-right">
					<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre de la categoría', 'required']) !!}
							@if ($errors->has('nombre'))
								<span class="help-block">{{ $errors->first('nombre') }}</span>
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
							{!! Form::text('descripcion', null, ['class' => 'form-control', 'placeholder' => 'Descripción']) !!}
							@if ($errors->has('descripcion'))
								<span class="help-block">{{ $errors->first('descripcion') }}</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('ancho')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('ancho'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Ancho (pixeles)
							</label>
							{!! Form::number('ancho', null, ['class' => 'form-control', 'placeholder' => 'Ancho de la imágen permitida (pixeles)', 'min' => '10', 'max' => '500', 'required', $tieneImagenes?'readonly':'']) !!}
							@if ($errors->has('ancho'))
								<span class="help-block">{{ $errors->first('ancho') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('alto')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('alto'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Alto (pixeles)
							</label>
							{!! Form::number('alto', null, ['class' => 'form-control', 'placeholder' => 'Alto de la imágen permitida  (pixeles)', 'min' => '10', 'max' => '500', 'required', $tieneImagenes?'readonly':'']) !!}
							@if ($errors->has('alto'))
								<span class="help-block">{{ $errors->first('alto') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('categoriaImagen') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("input[name='codigo']").enfocar();
	});
</script>
@endpush

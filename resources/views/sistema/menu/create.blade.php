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

					<div class="card-tools pull-right">
						<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('padre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('padre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Menú padre
								</label>
								{!! Form::select('padre', $lista, null, ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'Menú padre']) !!}
								@if ($errors->has('padre'))
									<span class="help-block">{{ $errors->first('padre') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre del menú', 'required']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('ruta')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('ruta'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Ruta
								</label>
								{!! Form::text('ruta', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'ruta del menú']) !!}
								@if ($errors->has('ruta'))
									<span class="help-block">{{ $errors->first('ruta') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('icono')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('icono'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Icono
								</label>
								{!! Form::text('icono', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Icono del menú']) !!}
								@if ($errors->has('icono'))
									<span class="help-block">{{ $errors->first('icono') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
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

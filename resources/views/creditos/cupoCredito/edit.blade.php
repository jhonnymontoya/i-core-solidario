@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Cupos de crédito
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Cupos de crédito</li>
		</ol>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif

		<div class="row">
			{!! Form::model($modalidad, ['url' => ['cupoCredito', $modalidad], 'method' => 'put', 'role' => 'form']) !!}
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Editar modalidad</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('codigo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Código
									</label>
									{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autocomplete' => 'off', 'readonly']) !!}
									@if ($errors->has('codigo'))
										<span class="help-block">{{ $errors->first('codigo') }}</span>
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
									{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre', 'autocomplete' => 'off', 'readonly']) !!}
									@if ($errors->has('nombre'))
										<span class="help-block">{{ $errors->first('nombre') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('apalancamiento_cupo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('apalancamiento_cupo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Veces apalancamiento
									</label>
									{!! Form::text('apalancamiento_cupo', null, ['class' => 'form-control', 'placeholder' => 'Veces apalncamiento', 'autocomplete' => 'off', 'autofocus']) !!}
									@if ($errors->has('apalancamiento_cupo'))
										<span class="help-block">{{ $errors->first('apalancamiento_cupo') }}</span>
									@endif
								</div>
							</div>							
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('cupoCredito') }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
				</div>
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
@endpush

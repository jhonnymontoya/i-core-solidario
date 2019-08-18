@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tarjetas
						<small>Tarjeta</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Tarjeta</a></li>
						<li class="breadcrumb-item active">Tarjetas</li>
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
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('error') }}
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('message') }}
			</div>
		@endif
		{!! Form::open(['url' => 'tarjetas', 'method' => 'post', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Crear nuevas tarjetas</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('fechaVencimiento')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fechaVencimiento'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha de vencimiento
									</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('fechaVencimiento', $fecha, ['class' => 'form-control pull-right', 'placeholder' => 'yyyy/mm']) !!}
									</div>
									@if ($errors->has('fechaVencimiento'))
										<span class="help-block">{{ $errors->first('fechaVencimiento') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('numeroInicial')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('numeroInicial'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Número inicial
									</label>
									{!! Form::number('numeroInicial', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número inicial']) !!}
									@if ($errors->has('numeroInicial'))
										<span class="help-block">{{ $errors->first('numeroInicial') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('numeroFinal')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('numeroFinal'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Número final
									</label>
									{!! Form::number('numeroFinal', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número final']) !!}
									@if ($errors->has('numeroFinal'))
										<span class="help-block">{{ $errors->first('numeroFinal') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tarjetas') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("input[name='fechaVencimiento']").datepicker( {
			format: "yyyy/mm",
			viewMode: "months",
			minViewMode: "months",
			autoclose: "true"
		});
	});
</script>
@endpush

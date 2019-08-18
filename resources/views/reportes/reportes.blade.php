@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Estadísticos
						<small>Reportes</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Reportes</a></li>
						<li class="breadcrumb-item active">Estadísticos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		
		<div class="card card-primary">
			<div class="card-header with-border">
				<h3 class="card-title">Reportes - Estadísticos</h3>
			</div>
			<div class="card-body">
				{!! Form::model(Request::all(), ['url' => 'reportes/estadisticos', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
				<br>
				<div class="row form-horizontal">
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('tipo_reporte')?'has-error':'') }}">
							<label class="col-sm-4 control-label">
								@if ($errors->has('tipo_reporte'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Reporte
							</label>
							<div class="col-sm-8">
								{!! Form::select('tipo_reporte', ['ASOCIADOS' => 'Asociados'], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione uno']) !!}
								@if ($errors->has('tipo_reporte'))
									<span class="help-block">{{ $errors->first('tipo_reporte') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('fecha_consulta')?'has-error':'') }}">
							<label class="col-sm-4 control-label">
								@if ($errors->has('fecha_consulta'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha
							</label>
							<div class="col-sm-8 input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								{!! Form::text('fecha_consulta', date('Y/m'), ['class' => 'form-control', 'placeholder' => 'yyyy/mm' ]) !!}
								@if ($errors->has('fecha_consulta'))
									<span class="help-block">{{ $errors->first('fecha_consulta') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="btn-group">
							<button type="submit" class="btn btn-success"><i class="fa fa-play"></i> Procesar</button>
						</div>
					</div>
				</div>
				{!! Form::close() !!}
				<hr>
				<hr>
				{!! $data !!}
			</div>
			<div class="card-footer">
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
	$("input[name='fecha_consulta']").datepicker( {
		format: "yyyy/mm",
		viewMode: "months",
		minViewMode: "months",
		autoclose: "true"
	});
</script>
@endpush
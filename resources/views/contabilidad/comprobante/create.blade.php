@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Comprobantes
			<small>Contabilidad</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Contabilidad</a></li>
			<li class="active">Comprobantes</li>
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
		{!! Form::open(['url' => 'comprobante', 'method' => 'post', 'role' => 'form', 'id' => 'formProcesar']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Crear nuevo comprobante</h3>
					</div>
					<div class="box-body">
						<div class="row form-horizontal">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('tipo_comprobante_id')?'has-error':'') }}">
									<label class="col-sm-4 control-label">
										@if ($errors->has('tipo_comprobante_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo de Comprobante
									</label>
									<div class="col-sm-8">
										{!! Form::select('tipo_comprobante_id', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un tipo de comprobante']) !!}
										@if ($errors->has('tipo_comprobante_id'))
											<span class="help-block">{{ $errors->first('tipo_comprobante_id') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('fecha_movimiento')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('fecha_movimiento'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha
									</label>
									<div class="col-sm-9">
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											{!! Form::text('fecha_movimiento', Date('d/m/Y'), ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
										</div>
										@if ($errors->has('fecha_movimiento'))
											<span class="help-block">{{ $errors->first('fecha_movimiento') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
						<div class="row form-horizontal">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
									<label class="col-sm-2 control-label">
										@if ($errors->has('descripcion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Descripción
									</label>
									<div class="col-sm-10">
										{!! Form::text('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
										@if ($errors->has('descripcion'))
											<span class="help-block">{{ $errors->first('descripcion') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<a class="btn btn-success" id="continuar">Continuar</a>
						{{--{!! Form::submit('Continuar', ['class' => 'btn btn-success', 'id' => 'continuar']) !!}--}}
						<a href="{{ url('comprobante') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("select[name='tipo_comprobante_id']").selectAjax("{{ url('api/tipoComprobante') }}", {entidad: {{ Auth::getSession()->get('entidad')->id }}, id:"{{ old('tipo_comprobante_id') }}"});
		$("#continuar").click(function(){
			$("#continuar").addClass("disabled");
			$("#formProcesar").submit();
		});
	});
</script>
@endpush

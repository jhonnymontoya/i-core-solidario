@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Anular comprobante</li>
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
				<div class="card-header with-border">
					<h3 class="card-title">pago de impuestos</h3>
				</div>
				<div class="card-body">
					{!! Form::open(['route' => ['comprobante.pagoImpuesto', $movimientoTemporal], 'method' => 'get', 'role' => 'form']) !!}
					<div class="row form-horizontal">
						<div class="col-md-5 col-sm-12">
							<div class="form-group {{ ($errors->has('impuesto')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('impuesto'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Impuesto
								</label>
								<div class="col-sm-8">
									@php
										$impuesto = null;
										if(Request::has('impuesto')) {
											$impuesto = Request::get('impuesto');
										}
									@endphp
									{!! Form::select('impuesto', $impuestos, $impuesto, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un impuesto']) !!}
									@if ($errors->has('impuesto'))
										<span class="help-block">{{ $errors->first('impuesto') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-5 col-sm-12">
							<div class="form-group {{ ($errors->has('fechaCorte')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('fechaCorte'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha de corte
								</label>
								<div class="col-sm-8">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										@php
											$fechaCorte = "";
											if(Request::has('fechaCorte')) {
												$fechaCorte = Request::get('fechaCorte');
											}
											else {
												$fechaCorte = \Carbon\Carbon::now()->startOfMonth()->subDay()->format('d/m/Y');
											}
										@endphp
										{!! Form::text('fechaCorte', $fechaCorte, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fechaCorte'))
										<span class="help-block">{{ $errors->first('fechaCorte') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 col-sm-12">
								{!! Form::submit('Buscar', ['class' => 'btn btn-success']) !!}
							</div>
						</div>
					</div>
					{!! Form::close() !!}
					@if ($req)
						<hr>
						@if (empty($infPagImp))
							<h4>No se encontraron datos a mostrar.</h4>
						@else
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Total a pagar:</dt>
										<dd>${{ number_format($total) }}</dd>
									</dl>
								</div>
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Número registros:</dt>
										<dd>{{ number_format($totalRegistros) }}</dd>
									</dl>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Número de cuentas:</dt>
										<dd>{{ number_format(count($cuentas)) }}</dd>
									</dl>
								</div>
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Fecha de corte:</dt>
										<dd>{{ $fechaCorte }}</dd>
									</dl>
								</div>
							</div>

							{{--PANELES--}}
							@foreach ($cuentas as $cuenta)
								<div class="panel panel-default">
									<div class="panel-heading" role="tab" id="cuenta{{ $cuenta->cuenta }}">
										<h4 class="panel-title">
											<a role="button" data-toggle="collapse" data-parent="#accordion" href="#cod{{ $cuenta->cuenta }}" aria-expanded="false" aria-controls="cod{{ $cuenta->cuenta }}">
												{{ $cuenta->cuenta }} - {{ $cuenta->nombre }} <span class="pull-right">${{ number_format($cuenta->subTotal) }}</span>
											</a>
										</h4>
									</div>
									<div id="cod{{ $cuenta->cuenta }}" class="panel-collapse collapse" role="tabpanel">
										<div class="panel-body">
											@foreach ($terceros[$cuenta->cuenta] as $tercero)
												<div class="row">
													<div class="col-md-12">
														<span>
															<strong>{{ $tercero->nombreCompleto }}:</strong>
															${{ number_format($tercero->total) }}
														</span>
													</div>
												</div>
											@endforeach
										</div>
									</div>
								</div>
							@endforeach
						@endif
					@endif
				</div>
				<div class="card-footer">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							@if (!empty($req) && !empty($infPagImp))
								{!! Form::open(['route' => ['comprobante.cargarImpuesto', $movimientoTemporal], 'method' => 'put']) !!}
								{!! Form::hidden('impuesto', $impuesto) !!}
								{!! Form::hidden('fechaCorte', $fechaCorte) !!}
								{!! Form::submit('Aceptar', ['class' => 'btn btn-success']) !!}
								{!! Form::close() !!}
							@endif
						</div>
						<div class="col-md-6 col-sm-12">
							<a href="{{ route('comprobanteEdit', $movimientoTemporal->id) }}" class="btn btn-danger pull-right">Volver al comprobante</a>
						</div>
					</div>
				</div>
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
		$(".select2").select2();
	});
</script>
@endpush

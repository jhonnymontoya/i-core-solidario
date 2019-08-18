@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Reliquidar créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Reliquidar créditos</li>
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
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Reliquidar créditos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('tercero'), ['url' => 'reliquidarCredito', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('tercero')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('tercero'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Seleccione tercero
								</label>
								<div class="col-sm-8">
									{!! Form::select('tercero', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione tercero']) !!}
									@if ($errors->has('tercero'))
										<span class="help-block">{{ $errors->first('tercero') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group {{ ($errors->has('fecha')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('fecha'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha
								</label>
								<div class="col-sm-8">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('fecha', Request::has('fecha') ? Request::get('fecha') : date("d/m/Y"), ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fecha'))
										<span class="help-block">{{ $errors->first('fecha') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					@if($tercero)
						<br>
						{!! Form::model(Request::only('tercero'), ['url' => 'ajusteCreditos/ajuste', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
						{!! Form::hidden('tercero', Request::get('tercero')) !!}
						<div class="row">
							<div class="col-md-12">
								<label>Reliquidación de créditos para:</label> {{ $tercero->tipoIdentificacion->codigo }} {{$tercero->nombre_completo}}
							</div>
						</div>
						<br>

						@if(Request::has('tercero'))
							<div class="row">
								<div class="col-md-12 table-responsive">
									@if($creditos->count())
										<table class="table table-hover">
											<thead>
												<tr>
													<th>Número obligación</th>
													<th>Fecha de crédito</th>
													<th class="text-center">Valor inicial</th>
													<th class="text-center">Valor cuota</th>
													<th class="text-center">Plazo</th>
													<th class="text-center">Saldo capital</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												@foreach($creditos as $credito)
													@php
														$saldo = $credito->saldoObligacion($fecha);
														if($saldo == 0)continue;
													@endphp
													<tr>
														<td>{{ $credito->numero_obligacion }}</td>
														<td>{{ $credito->fecha_desembolso }}</td>
														<td class="text-right">${{ number_format($credito->valor_credito, 0) }}</td>
														<td class="text-right">${{ number_format($credito->valor_cuota, 0) }}</td>
														<td class="text-right">{{ $credito->plazo }} {{ $credito->plazo == 1 ? 'cuota' : 'cuotas' }}</td>
														<td class="text-right">${{ number_format($saldo, 0) }}</td>
														<td class="text-center">
															<a href="{{ route('reliquidarCreditoReliquidar', $credito->id) }}?fechaReliquidacion={{ $fecha }}" class="btn btn-info btn-xs">Reliquidar</a>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									@else
										<h4 class="text-danger">El tercero no tiene creditos activos en el momento</h4 class="text-danger">
									@endif
								</div>
							</div>
						@endif
						{!! Form::close() !!}
					@endif
				</div>
				<div class="card-footer">
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
		$("select[name='tercero']").select2({
			allowClear: true,
			placeholder: "Seleccione un tercero",
			ajax: {
				url: 'tercero/getTerceroConParametros',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						estado: 'ACTIVO',
						tipo: 'NATURAL'
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			}
		});
		
		@if(Request::has('tercero') && !empty(Request::get('tercero')))
			$.ajax({url: 'tercero/getTerceroConParametros', dataType: 'json', data: {id: {{ Request::get('tercero') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='tercero']"));
					$("select[name='tercero']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush
@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Ajuste créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Ajuste créditos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				@if (Session::has('codigoComprobante'))
					<a href="{{ route('reportesReporte', 1) }}?codigoComprobante={{ Session::get('codigoComprobante') }}&numeroComprobante={{ Session::get('numeroComprobante') }}" title="Imprimir comprobante" target="_blank">
						{{ Session::get('message') }}
					</a>
					<i class="fas fa-external-link-alt"></i>
				@else
					{{ Session::get('message') }}
				@endif
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
					<h3 class="card-title">Ajuste créditos</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('tercero', 'fechaAjuste'), ['url' => 'ajusteCreditos', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('tercero')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tercero'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Seleccione tercero
								</label>
								{!! Form::select('tercero', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione tercero']) !!}
								@if ($errors->has('tercero'))
									<span class="help-block">{{ $errors->first('tercero') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('fechaAjuste') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha ajuste</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fechaAjuste', Request::has('fechaAjuste') ? Request::get('fechaAjuste') : date("d/m/Y"), ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fechaAjuste'))
										<div class="invalid-feedback">{{ $errors->first('fechaAjuste') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-1 col-sm-12">
							<label class="control-label">&nbsp;</label>
							<br>
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
					</div>
					{!! Form::close() !!}
					@if($tercero)
						<br>
						{!! Form::model(Request::only('tercero'), ['url' => 'ajusteCreditos/ajuste', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
						{!! Form::hidden('tercero', Request::get('tercero')) !!}
						<div class="row">
							<div class="col-md-12">
								<label>Ajuste para:</label> {{ $tercero->tipoIdentificacion->codigo }} {{$tercero->nombre_completo}}
							</div>
						</div>
						<br>

						@if(Request::has('tercero'))
							<div class="row">
								<div class="col-md-12 table-responsive">
									@if($creditos->count())
										<table class="table table-striped table-hover">
											<thead>
												<tr>
													<th>Número obligación</th>
													<th>Modalidad</th>
													<th>Fecha de crédito</th>
													<th class="text-center">Tasa M.V.</th>
													<th class="text-center">Valor inicial</th>
													<th class="text-center">Valor cuota</th>
													<th class="text-center">Saldo capital</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												@foreach($creditos as $credito)
													<tr>
														<td>
															<a href="{{ route('ajusteCredito', $credito->id) }}?fechaAjuste={{Request::get('fechaAjuste')}}">
																{{ $credito->numero_obligacion }}
															</a>
														</td>
														<td>
															{{ Str::limit($credito->modalidadCredito->nombre, 40) }}
														</td>
														<td>{{ $credito->fecha_desembolso }}</td>
														<td class="text-right">{{ number_format($credito->tasa, 2) }}%</td>
														<td class="text-right">${{ number_format($credito->valor_credito, 0) }}</td>
														<td class="text-right">${{ number_format($credito->valor_cuota, 0) }}</td>
														<td class="text-right">${{ number_format($credito->saldoObligacion(Request::get("fechaAjuste")), 0) }}</td>
														<td class="text-center">
															<a href="{{ route('ajusteCredito', $credito->id) }}?fechaAjuste={{Request::get('fechaAjuste')}}" class="btn btn-outline-info btn-sm">Ajustar</a>
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
		
		@if(Request::has('tercero'))
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

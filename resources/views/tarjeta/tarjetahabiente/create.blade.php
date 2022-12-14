@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tarjetahabiente
						<small>Tarjeta</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Tarjeta</a></li>
						<li class="breadcrumb-item active">Tarjetahabiente</li>
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
		{!! Form::open(['url' => 'tarjetaHabiente', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Crear nuevo tarjetahabiente</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('tercero_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tarjetahabiente</label>
								{!! Form::select('tercero_id', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
								@if ($errors->has('tercero_id'))
									<div class="invalid-feedback">{{ $errors->first('tercero_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('producto_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Producto</label>
								{!! Form::select('producto_id', $productos, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione un producto']) !!}
								@if ($errors->has('producto_id'))
									<div class="invalid-feedback">{{ $errors->first('producto_id') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tarjeta_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tarjeta</label>
								{!! Form::select('tarjeta_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('tarjeta_id'))
									<div class="invalid-feedback">{{ $errors->first('tarjeta_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-2 ahorros" style="display: none;">
							<div class="form-group">
								@php
									$valid = $errors->has('cuenta_ahorro_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta ahorros</label>
								{!! Form::select('cuenta_ahorro_id', [], null, ['class' => [$valid, 'form-control', 'select2'], 'style' => 'width: 100%;']) !!}
								@if ($errors->has('cuenta_ahorro_id'))
									<div class="invalid-feedback">{{ $errors->first('cuenta_ahorro_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3 modalidad" style="display: none">
							<div class="form-group">
								<label class="control-label">Modalidad</label><br>
								<div class="valor-modalidad"></div>
							</div>
						</div>
						<div class="col-md-1 tasa" style="display: none">
							<div class="form-group">
								<label class="control-label">Tasa</label><br>
								<div class="valor-tasa"></div>
							</div>
						</div>
						<div class="col-md-1 plazo" style="display: none">
							<div class="form-group">
								<label class="control-label">Plazo</label><br>
								<div class="valor-plazo"></div>
							</div>
						</div>
						<div class="col-md-2 cupo"  style="display: none">
							<div class="form-group">
								@php
									$valid = $errors->has('cupo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cupo</label>
								{!! Form::text('cupo', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Cupo', 'data-maskMoney', 'style' => 'width: 100%;']) !!}
								@if ($errors->has('cupo'))
									<div class="invalid-feedback">{{ $errors->first('cupo') }}</div>
								@endif
							</div>
							
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tarjetaHabiente') }}" class="btn btn-outline-danger">Cancelar</a>
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
		$(window).load(function(){
			$("input[name='cupo']").maskMoney('mask');
		});
		$("select[name='tercero_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('tercero/getTerceroConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						tipo: 'NATURAL',
						estado: 'ACTIVO',
						page: params.page
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
		@php
			$tercero = Request::has("tercero_id") ? Request::get("tercero_id") : "";
			$tercero = empty(old('tercero_id')) ? $tercero : old('tercero_id');
		@endphp
		@if(!empty($tercero))
			$.ajax({url: '{{ url('tercero/getTerceroConParametros') }}', dataType: 'json', data: {id: {{ $tercero }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='tercero_id']"));
					$("select[name='tercero_id']").val(element.id).trigger("change");
				}
			});
		@endif
		$("select[name='tarjeta_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('tarjetas/getTarjetas') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page
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
		@if(!empty(old('tarjeta_id')))
			$.ajax({url: '{{ url('tarjetas/getTarjetas') }}', dataType: 'json', data: {id: {{ old('tarjeta_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='tarjeta_id']"));
					$("select[name='tarjeta_id']").val(element.id).trigger("change");
				}
			});
		@endif
		function clear() {
			$(".ahorros").hide();
			$(".modalidad").hide(); 
			$(".tasa").hide();
			$(".plazo").hide(); 
			$(".cupo").hide();
			$(".valor-modalidad").text("");
			$(".valor-tasa").text("");
			$(".valor-plazo").text("");
			$("select[name='cuenta_ahorro_id']").val("").trigger("change");
		}
		function cambioProducto() {
			clear();
			$producto = $("select[name='producto_id']");
			if($producto.val() != "") {
				$.ajax({url: '{{ url('tarjetaProducto/getProductoConParametros') }}', dataType: 'json', data: {id: $producto.val() }}).done(function(data){
					$data = data.items[0];
					switch($data.modalidad) {
						case 'CUENTAAHORROS':
							$(".ahorros").show();
							break;
						case 'CREDITO':
							$(".modalidad").show(); 
							$(".tasa").show();
							$(".plazo").show(); 
							$(".cupo").show();
							$(".valor-modalidad").text($data.modalidadCredito.nombre);
							$(".valor-tasa").text($data.modalidadCredito.tasa + "%");
							$(".valor-plazo").text($data.modalidadCredito.plazo);
							break;
						case 'MIXTO':
							$(".ahorros").show();
							$(".modalidad").show(); 
							$(".tasa").show();
							$(".plazo").show(); 
							$(".cupo").show();
							$(".valor-modalidad").text($data.modalidadCredito.nombre);
							$(".valor-tasa").text($data.modalidadCredito.tasa + "%");
							$(".valor-plazo").text($data.modalidadCredito.plazo);
							break;
					}
				});
			}
		}
		$("select[name='producto_id']").change(function(event){
			cambioProducto();
		})		
		cambioProducto();
		$("select[name='tercero_id']").change(function(event){
			$url = "{{ url('cuentaAhorros/:tercero/cuentas') }}".replace(":tercero", $(this).val());
			$.ajax({url: $url, dataType: 'json'}).done(function(data){
				$cuenta = $("select[name='cuenta_ahorro_id']");
				$cuenta.find('option').remove().end();
				if(data.total_count > 0) {
					data.items.forEach(function(item){
						if(item.estado == "ACTIVA" && item.cuentaVinculada == false) {
							$('<option>').val(item.id).text(item.text).appendTo($cuenta);
						}
					});
				}
			});
		});
	});
</script>
@endpush

@extends('layouts.consulta')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">

	<section class="content">
		@php
			if(Session::has('message')) {
				$message = Session::get('message'); //Consumir mensaje de la sessión
			}
		@endphp
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
					<h3 class="card-title">Enviar solicitud de crédito</h3>
				</div>
				<div class="card-body">
					<div class="alert alert-secondary" role="alert">
						Envia tu solicitud de crédito, nuestros funcionarios la recibiran, la procesarán y se pondrán en contacto contigo.
					</div>

					{!! Form::open(['url' => 'consulta/solicitarCredito', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask', 'id' => 'frmEnviarSolicitudCredito']) !!}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('modalidad') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Modalidad de crédito</label>
								{!! Form::select('modalidad', $modalidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Modalidad de crédito', 'autocomplete' => 'off']) !!}
								@if ($errors->has('modalidad'))
									<div class="invalid-feedback">{{ $errors->first('modalidad') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
							    @php
							        $valid = $errors->has('valorCredito') ? 'is-invalid' : '';
							        if(Request::has('valorCredito')) {
							        	$cupoDisponible = Request::get('valorCredito');
							        }
							    @endphp
							    <label class="control-label">Valor crédito</label>
							    <div class="input-group">
							        <div class="input-group-prepend">
							            <span class="input-group-text">$</span>
							        </div>
							        {!! Form::text('valorCredito', $cupoDisponible, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Valor crédito', 'data-maskMoney', 'autofocus']) !!}
							        @if ($errors->has('valorCredito'))
							            <div class="invalid-feedback">{{ $errors->first('valorCredito') }}</div>
							        @endif
							    </div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
							    @php
							        $valid = $errors->has('plazo') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Plazo (cuotas)</label>
						        {!! Form::number('plazo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Plazo (cuotas)', 'min' => 1, 'max' => 1000]) !!}
						        @if ($errors->has('plazo'))
						            <div class="invalid-feedback">{{ $errors->first('plazo') }}</div>
						        @endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    @php
							        $valid = $errors->has('observaciones') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Observaciones</label>
							    {!! Form::textarea('observaciones', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Observaciones']) !!}
							    @if ($errors->has('observaciones'))
							        <div class="invalid-feedback">{{ $errors->first('observaciones') }}</div>
							    @endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<button type="button" class="btn btn-outline-success float-right" data-toggle="modal" data-target="#confirmacion">
								Enviar
							</button>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal" tabindex="-1" role="dialog" id="confirmacion">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Solicitar crédito</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>¿Seguro que desea enviar la solicitud de crédito?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn btn-outline-success" id="btnEnviar">Enviar</button>
				<button type="button" class="btn btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(window).load(function(){
		$("input[name='valorCredito']").maskMoney('mask');
	});

	$("#btnEnviar").click(function(){
		$("#frmEnviarSolicitudCredito").submit();
	});
</script>
@endpush

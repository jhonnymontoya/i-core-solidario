@extends('layouts.consulta')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
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

		<div class="container-fluid">
			<br>
			<div class="card">
				<div class="card-header with-border">
					<h3 class="card-title">Perfil</h3>
					<div class="card-tools">
						<a href="{{ url('consulta/perfil/editar') }}" class="btn btn-outline-info btn-sm"><i class="fas fa-edit"></i> Editar</a>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<?php
							$antiguedad = 'No aplica';
							if($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') {
								$antiguedad = $socio->fecha_antiguedad != null? $socio->fecha_antiguedad->diffForHumans() : 'Sin antigüedad';
							}
							$contacto = $socio->tercero->getContacto();
							$label = "bg-";
							$porcentaje = $socio->endeudamiento();
							if($porcentaje <= $porcentajeMaximoEndeudamientoPermitido) $label .= 'green';
							else $label .= 'red';
							$saldo = $socio->tercero->cupoDisponible($fecha);
						?>
						<div class="col-md-6">
							<dl>
								<dt>Nombre</dt>
								<dd>{{ $tercero->tipoIdentificacion->codigo }} {{ $tercero->nombre_completo }}</dd>

								<dt>Antigüedad</dt>
								<dd>{{ $antiguedad }}</dd>

								<dt>Fecha afiliación</dt>
								<dd>{{ $socio->fecha_afiliacion }}</dd>

								<dt>Email</dt>
								<dd>{{ empty($contacto) ? 'Sin información' : $contacto->email }}</dd>

								<dt>Teléfono</dt>
								<dd>{{ empty($contacto) ? 'Sin información' : ($contacto->movil ?: $contacto->telefono) }}</dd>
							</dl>
						</div>

						<div class="col-md-6">
							<dl>
								<dt>Empresa</dt>
								<dd>{{ empty($socio->pagaduria) ? '' : $socio->pagaduria->nombre }}</dd>

								<dt>Fecha nacimiento</dt>
								@if (empty($socio->tercero->fecha_nacimiento))
									<dd></dd>
								@else
									<dd>{{ $socio->tercero->fecha_nacimiento }} ({{ $socio->tercero->fecha_nacimiento->diffForHumans() }})</dd>
								@endif

								<dt>Ingreso empresa</dt>
								<dd>{{ $socio->fecha_ingreso }} ({{ $socio->fecha_ingreso->diffForHumans() }})</dd>

								<dt>Endeudamiento</dt>
								<dd><span class="badge badge-pill {{ $label }}">{{ number_format($porcentaje, 2) }}%</span></dd>

								<dt>Sueldo</dt>
								<dd>${{ number_format($socio->sueldo_mes) }}</dd>
							</dl>
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-6">
							@php
								$saldo = $socio->tercero->cupoDisponible($fecha);
							@endphp
							<h4 class="text-{{ $saldo <= 0 ? 'danger' : 'primary' }}"><strong>Cupo disponible: ${{ number_format($saldo) }}</strong>&nbsp;<small data-toggle="tooltip" data-original-title="Sujeto a políticas de crédito"><i class="fa fa-info-circle"></i></small></h4>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-6"><strong>Último periodo aplicado:</strong></div>
								<div class="col-md-6">
									<?php
										if(!is_null($recaudoAplicado)) {
											?>
											<span class="badge badge-success">{{ $recaudoAplicado->numero_periodo }}</span> {{ $recaudoAplicado->fecha_recaudo }}
											<?php
										}
									?>
								</div>
							</div>
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
@endpush

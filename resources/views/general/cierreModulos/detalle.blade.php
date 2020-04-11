@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Control cierres de periodos
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Control cierres de periodos</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Proceso de cierres periodo {{ $periodo->mes }} - {{ $periodo->anio }}</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					<?php
						$contador = 0;
					?>
					@foreach($periodo->entidad->getModulos() as $modulo)
						@if($contador % 3 == 0)
							@if($contador != 0)
								</div>
							@endif
							<div class="row">
						@endif
						<div class="col-md-4 col-sm-12 col-xs-12">
							@php
								$link = "";
								$estaCerrado = $periodo->moduloCerrado($modulo->id);
								switch ($modulo->id) {
									case 2: //Contabilidad
										$link = route('cierreModulosDetalleContabilidad', $periodo->id);
										break;
									case 3: //Convenios
										$link = "#";
										break;
									case 4: //Nómina
										$link = "#";
										break;
									case 6: //Ahorros y aportes
										$link = route('cierreModulosDetalleAhorros', $periodo->id);
										break;
									case 7: //Cartera
										$link = route('cierreModulosDetalleCartera', $periodo->id);
										break;
									case 10: //Socios
										$link = route('cierreModulosDetalleSocios', $periodo->id);
										break;
									default:
										$link = '#';
										break;
								}
							@endphp
							<div class="small-box bg-{{ $estaCerrado ? 'green' : 'red' }}">
								<div class="inner">
									<h3>{{ $contador + 1 }}</h3>
									<p>{{ $modulo->nombre }}</p>
								</div>
								<div class="icon">
									<i class="fa {{ $modulo->icono }}"></i>
								</div>
								@if ($estaCerrado)
									<a class="small-box-footer">Cerrado <i class="fa fa-check"></i></a>
								@else
									<a href="{{ $link }}" class="small-box-footer">
										Cerrar <i class="fa fa-lock"></i>
									</a>
								@endif
							</div>
						</div>
						<?php
							$contador += 1;
						?>
					@endforeach
					@if($contador > 0)
						</div>
					@endif
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					<a href="{{ url('cierreModulos') }}" class="btn btn-outline-danger btn-block">Volver</a>
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

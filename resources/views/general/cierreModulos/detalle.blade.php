@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Control cierres de periodos
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Control cierres de periodos</li>
		</ol>
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
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Proceso de cierres periodo {{ $periodo->mes }} - {{ $periodo->anio }}</h3>
					</div>
					{{-- INICIO BOX BODY --}}
					<div class="box-body">
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
					{{-- FIN BOX BODY --}}
					<div class="box-footer">
						<a href="{{ url('cierreModulos') }}" class="btn btn-danger btn-block">Volver</a>
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

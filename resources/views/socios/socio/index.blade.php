@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Socios
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Socios</li>
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
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('socio/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="card">
			<div class="card-header with-border">
				<h3 class="card-title">Buscar socios</h3>
				<div class="card-tools pull-right">
					<button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::all(), ['url' => '/socio', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-4 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
					</div>
					<div class="col-md-3 col-sm-12">
						{!! Form::select('pagaduria', $pagadurias, null, ['class' => 'form-control select2', 'placeholder' => 'Pagaduria']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						{!! Form::select('estado', ['ACTIVO' => 'Activo', 'NOVEDAD' => 'Novedad', 'RETIRO' => 'Retiro', 'LIQUIDADO' => 'Liquidado', 'PROCESO' => 'Proceso'], null, ['class' => 'form-control select2', 'placeholder' => 'Estado']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						{!! Form::select('calificacion', ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'K' => 'K'], null, ['class' => 'form-control select2', 'placeholder' => 'Calificación']); !!}
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				<br>
			</div>
		</div>
		@if($terceros->count() > 0)
			<div class="row">
				<div class="col-md-12">
					<strong>{{ $terceros->total() }} socios en total, mostrando {{ $terceros->count() }}</strong>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					{!! $terceros->appends(Request::all())->render() !!}
				</div>
			</div>
		@else
			<div class="row"><div class="col-md-12"><h4>No se encontraron socios</h4></div></div>
		@endif
		<?php
			$contador = 0;
		?>
		@foreach($terceros as $socio)
			@if($contador % 3 == 0)
				@if($contador != 0)
					</div>
				@endif
				<div class="row">
			@endif
			<?php
				$color = 'light-blue';
				switch ($socio->socio->estado) {
					case 'ACTIVO':
						$color = 'green';
						break;
					case 'NOVEDAD':
						$color = 'orange';
						break;
					case 'RETIRO':
						$color = 'maroon';
						break;
					case 'LIQUIDADO':
						$color = 'red';
						break;
					case 'PROCESO':
						$color = 'light-blue';
						break;
				}
			?>
			<div class="col-sm-4">
				<div class="card card-widget widget-user">
					<div class="widget-user-header bg-{{ $color }}-active">
						<div class="widget-user-username">
							{{ $socio->primer_nombre . ' ' . $socio->segundo_nombre }}
						</div>
						<div class="widget-user-desc">
							{{ $socio->primer_apellido . ' ' . $socio->segundo_apellido }}
						</div>
						<div class="widget-user-desc text-right">
							<i>{{ mb_convert_case($socio->socio->estado, MB_CASE_TITLE, "UTF-8") }}</i>
						</div>
					</div>
					<div class="widget-user-image">
						<img class="img-circle" src="{{ asset('storage/asociados/' . (empty($socio->socio->avatar)?'avatar-160x160.png':$socio->socio->avatar) ) }}" alt="{{ $socio->nombre_corto }}" />
					</div>
					<div class="card-footer">
						<ul class="nav nav-stacked">
							<li>
								<a>{{ $socio->identificacion }}</a>
							</li>
							<li>
								<a>Pagaduría
									<span class="pull-right">{{ empty($socio->socio->pagaduria) ? '' : $socio->socio->pagaduria->nombre }}</span>
								</a>
							</li>
							<?php
								$antiguedad = 'No aplica';
								if($socio->socio->estado == 'ACTIVO' || $socio->socio->estado == 'NOVEDAD')
								{
									$antiguedad = $socio->socio->fecha_antiguedad != null? $socio->socio->fecha_antiguedad->diffForHumans() : 'Sin antigüedad';
								}
							?>
							<li>
								<a>Antigüedad
									<span class="pull-right">{{ $antiguedad }}</span>
								</a>
							</li>
							<li>
								<a>Calificación
									<span class="pull-right badge bg-green">A</span>
								</a>
							</li>
							<li>
								<a>Endeudamiento
									<?php
										$label = "bg-";
										$porcentaje = $socio->socio->endeudamiento();
										if($porcentaje <= $porcentajeMaximoEndeudamientoPermitido)
										{
											$label .= 'green';
										}
										else
										{
											$label .= 'red';
										}
									?>
									<span class="pull-right badge {{ $label }}">{{ number_format($porcentaje, 2) }}%</span>
								</a>
							</li>
							<li>
								<a>Cupo disponible
									<span class="pull-right">${{ number_format($socio->cupoDisponible()) }}</span>
								</a>
							</li>
						</ul>
						<div class="row">
							<div class="col-sm-12 col-xs-12 border-right text-center">
								<a href="{{ route('socioEdit', $socio->socio) }}" class="btn btn-default" title="Editar">
									<i class="fa fa-edit"></i>
								</a>
								<a href="{{ route('socioAfiliacion', $socio->socio) }}" class="btn btn-default {{ ($socio->socio->estado == 'ACTIVO' || $socio->socio->estado == 'NOVEDAD') ? 'disabled' : '' }}" title="Afiliar">
									<i class="fa fa-thumbs-o-up"></i>
								</a>
								<a href="{{ url('socio/consulta') }}?socio={{ $socio->socio->id }}&fecha={{ date('d/m/Y') }}" class="btn btn-default" title="Consulta">
									<i class="fa fa-bullseye"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
				$contador += 1;
			?>
		@endforeach
		@if($contador > 0)
			</div>
		@endif
		@if($terceros->count() > 0)
			<div class="row">
				<div class="col-md-12 text-center">
					{!! $terceros->appends(Request::all())->render() !!}
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<strong>{{ $terceros->total() }} socios en total, mostrando {{ $terceros->count() }}</strong>
				</div>
			</div>
		@endif
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='name']").enfocar();
		$(".select2").select2();
		$(window).formularioCrear("{{ url('socio/create') }}");
	});
</script>
@endpush

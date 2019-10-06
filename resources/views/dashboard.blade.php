@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Dashboard
						<small>Inicio</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item active">Dashboard</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-8 col-sm-12">
					<div class="card card-default card-outline">
						<div class="card-header">
							<h3 class="card-title">Comparativo asociados último año</h3>
						</div>
						<div class="card-body">
							<canvas id="comparativoSocios"></canvas>
						</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-12">
					<div class="card card-primary card-outline">
						<div class="card-header">
							<h3 class="card-title">Afiliaciones recientes</h3>
							<div class="card-tools">
								<a href="{{ url('socio') }}">Ver todos</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="users-list clearfix">
								@foreach ($ultimosAfiliados as $afiliado)
									<li>
										<a class="users-list-name" href="{{ url('socio/consulta') }}?socio={{ $afiliado->id }}&fecha={{ date('d/m/Y') }}"><img class="img-circle" src="{{ asset('storage/asociados/' . $afiliado->obtenerAvatar()) }}" width="128" alt="{{ $afiliado->tercero->nombre_corto }}" /></a>
										<a class="users-list-name" href="{{ url('socio/consulta') }}?socio={{ $afiliado->id }}&fecha={{ date('d/m/Y') }}">{{ title_case($afiliado->tercero->primer_nombre) }}</a>
										<span class="users-list-date">{{ $afiliado->fecha_afiliacion->diffForHumans() }}</span>
									</li>
								@endforeach
							</ul>
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
<style type="text/css">
	.users-list{
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.users-list-name{
		font-weight: 600;
		color: #444;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		text-transform: capitalize;
	}
	.users-list-date{
		color: #999;
		font-size: 12px;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	var comparativoSocios = $('#comparativoSocios').get(0).getContext('2d');
	var comparativoSociosConfig = {
		type: 'line',
		data: {
			labels  : ['{!! implode('\',\'', $comparativoSocios['labels']) !!}'],
			datasets: [{
					label: 'Año actual',
					backgroundColor: 'rgb(17, 65, 219)',
					borderColor: 'rgb(17, 65, 219)',
					data: [{{ implode(',',  $comparativoSocios['DSActual']) }}],
					fill: false
			},{
					label: 'Año anterior',
					backgroundColor: 'rgb(225, 50, 34)',
					borderColor: 'rgb(225, 50, 34)',
					data: [{{ implode(',',  $comparativoSocios['DSAnterior']) }}],
					fill: false
				}]
		},
		options: {
			responsive: true,
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				intersect: true
			},
			tooltips: {
				mode: 'index',
				callbacks: {
					footer: function(tooltipItems, data) {
						var item0 = 0;
						var item1 = 0;
						if(tooltipItems[0]) {
							item0 = tooltipItems[0].yLabel;
						}
						else {
							return false;
						}
						if(tooltipItems[1]) {
							item1 = tooltipItems[1].yLabel;
						}
						else {
							return false;
						}
						var diferencia = item0 - item1;
						tooltip = (diferencia < 0 ? 'Disminuyeron ' : 'Aumentaron ') + Math.abs(diferencia);
						return tooltip;
					},
				},
				footerFontStyle: 'normal',
				intersect: false
			}
		}
	};
	comparativoSocios = new Chart(comparativoSocios, comparativoSociosConfig);
</script>
@endpush

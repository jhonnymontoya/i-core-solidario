@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Reportes
						<small>Reportes</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Reportes</a></li>
						<li class="breadcrumb-item active">Reportes</li>
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
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">{{ $reporte->modulo->nombre }} - {{ $reporte->nombre }}</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::all(), ['url' => ['reportes', $reporte], 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-12">
							@if($reporte->parametros)
								<div class="row">
									<div class="col-md-6">
										<h4>PARÁMETROS</h4>	
									</div>
									<div class="col-md-5">
										<button type="submit" class="btn btn-success btn-sm pull-right"><i class="fa fa-play"></i> Procesar</button>
									</div>
								</div>
								<br>
								@foreach ($reporte->parametros as $parametro)
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label class="col-sm-2 control-label">
													{{ $parametro->nombre }}
												</label>
												<div class="col-sm-3">
													{!! Form::text($parametro->parametro, empty($parametro->valor_defecto) ? null : $parametro->valor_defecto, ['class' => 'form-control select2', 'placeholder' => $parametro->nombre]) !!}
												</div>
												<div class="col-sm-6">
													<em>{{ $parametro->descripcion }}</em>
												</div>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="btn-group">
								<button type="submit" class="btn btn-success"><i class="fa fa-play"></i> Procesar</button>
								@if($data)
								<div class="btn-group">
									<a target="_blank" href="{{ Request::fullUrlWithQuery(['print' => true]) }}" class="btn btn-primary">
										<i class="fa fa-print"></i> Imprimir
									</a>
									<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<span class="caret"></span>
										<span class="sr-only">Opciones</span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<li><a class="btn xlsx"><i class="fa fa-file-excel-o"></i> XLSX</a></li>
										<li><a class="btn csv"><i class="fa fa-file-excel-o"></i> CSV</a></li>
										<li><a class="btn txt"><i class="fa fa-file-text-o"></i> TXT</a></li>
									</ul>
								</div>
								@endif
								<a href="{{ url('reportes') }}" class="btn btn-danger">
									<i class="fa fa-folder"></i> Ir a reportes
								</a>
							</div>
						</div>
					</div>
					{!! Form::close() !!}
					<hr>
					<div class="datos-tabla">
						{!! $data !!}
					</div>
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
	$(function () {
		$(".knob").knob({
			draw: function () {
				// "tron" case
				if (this.$.data('skin') == 'tron') {

				var a = this.angle(this.cv)  // Angle
				, sa = this.startAngle          // Previous start angle
				, sat = this.startAngle         // Start angle
				, ea                            // Previous end angle
				, eat = sat + a                 // End angle
				, r = true;

				this.g.lineWidth = this.lineWidth;

				this.o.cursor
				&& (sat = eat - 0.3)
				&& (eat = eat + 0.3);

				if (this.o.displayPrevious) {
				ea = this.startAngle + this.angle(this.value);
				this.o.cursor
				&& (sa = ea - 0.3)
				&& (ea = ea + 0.3);
				this.g.beginPath();
				this.g.strokeStyle = this.previousColor;
				this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
				this.g.stroke();
				}

				this.g.beginPath();
				this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
				this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
				this.g.stroke();

				this.g.lineWidth = 2;
				this.g.beginPath();
				this.g.strokeStyle = this.o.fgColor;
				this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
				this.g.stroke();

				return false;
				}
			}
		});
		@if($data)
			var instancia = $(".datos-tabla").find('table');
			var instancia = new TableExport(instancia, {
				headers: true,
				footers: true,
				filename: '{{ str_slug(str_limit($reporte->nombre . ' ', 24) . date('ymd'), '-') }}',
				bootstrap: false,
				exportButtons: false
			});
			var data = instancia.getExportData()['tableexport-1'];
			$(".xlsx").click(function(e){
				e.preventDefault();
				instancia.export2file(data["xlsx"].data, data["xlsx"].mimeType, data["xlsx"].filename, data["xlsx"].fileExtension);
			});
			$(".csv").click(function(e){
				e.preventDefault();
				instancia.export2file(data["csv"].data, data["csv"].mimeType, data["csv"].filename, data["csv"].fileExtension);
			});
			$(".txt").click(function(e){
				e.preventDefault();
				instancia.export2file(data["txt"].data, data["txt"].mimeType, data["txt"].filename, data["txt"].fileExtension);
			});
		@endif
	});
</script>
@endpush
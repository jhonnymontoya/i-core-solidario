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
					{!! Form::model(Request::all(), ['url' => ['reportes', $reporte], 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-12">
							@if($reporte->parametros)
								<div class="row">
									<div class="col-md-6">
										<h4>PARÁMETROS</h4>	
									</div>
									<div class="col-md-6 text-right">
										<button type="submit" class="btn btn-outline-success btn-sm pull-right procesar"><i class="far fa-play-circle"></i> Procesar</button>
										<button type="submit" class="btn btn-outline-success btn-sm pull-right procesando" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...</button>
									</div>
								</div>
								<br>
								@foreach ($reporte->parametros as $parametro)
									<div class="row form-horizontal">
										<div class="col-md-12">
											<div class="form-group row">
												<label class="control-label col-sm-2">{{ $parametro->nombre }}</label>
												<div class="col-sm-3">
													{!! Form::text($parametro->parametro, empty($parametro->valor_defecto) ? null : $parametro->valor_defecto, ['class' => 'form-control', 'placeholder' => $parametro->nombre, 'autocomplete' => 'off']) !!}
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
								<button type="submit" class="btn btn-outline-success procesar"><i class="far fa-play-circle"></i> Procesar</button>
								<button type="submit" class="btn btn-outline-success procesando" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...</button>
								@if($data)
								<a target="_blank" href="{{ Request::fullUrlWithQuery(['print' => true]) }}" class="btn btn-outline-primary">
									<i class="fa fa-print"></i> Imprimir
								</a>
								<a href="#" class="btn btn-outline-info copiar" data-clipboard-target=".table">
									<i class="far fa-copy"></i> Copiar
								</a>
								@endif
								<a href="{{ url('reportes') }}" class="btn btn-outline-danger">
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
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.procesando {
		display: none;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function () {
		$('.procesar').click(function(){
			$('.procesar').hide();
			$('.procesando').show();
		});
		const Toast = Swal.mixin({
			toast: true,
			position: 'bottom-end',
			showConfirmButton: false,
			timer: 3000
		});
		var btnCopiar = new ClipboardJS('.copiar');
		btnCopiar.on('success', function(e) {
			e.clearSelection();
			Toast.fire({
				type: 'success',
				title: 'Copiado.'
			})
		});
		$(".copiar").click(function(e){
			e.preventDefault();
		});
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
	});
</script>
@endpush
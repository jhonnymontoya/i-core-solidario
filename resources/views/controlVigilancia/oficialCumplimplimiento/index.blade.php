@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Oficial de cumplimiento
						<small>SARLAFT</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> SARLAFT</a></li>
						<li class="breadcrumb-item active">Oficial de cumplimiento</li>
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

		@if ($oficialCumplimiento == null)
			<div class="row">
				<div class="col-md-2">
					<a href="{{ url('oficialCumplimiento/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
				</div>
			</div>
		@endif
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $oficialCumplimiento != null ?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Oficial de cumplimiento</h3>
				</div>
				<div class="card-body">
					@if($oficialCumplimiento == null)
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontró nungún oficial de cumplimiento <a href="{{ url('oficialCumplimiento/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<div class="row">
							<div class="col-md-11 offset-md-1">
								<h4>Datos del oficial de cumplimiento:</h4>
							</div>
						</div>

						<br>
						<div class="row">
							<div class="col-md-9 offset-md-2">
								<dl class="row">
									<dt class="col-2">Nombre:</dt>
									<dd class="col-md-10">{{ $oficialCumplimiento->nombre }}</dd>

									<dt class="col-2">Correo electrónico:</dt>
									<dd class="col-md-10">
										<a href="mailto:{{ $oficialCumplimiento->email }}">
											{{ $oficialCumplimiento->email }}
										</a>
									</dd>

									@if(empty($oficialCumplimiento->email_copia) == false)
										<dt class="col-2">Copia correo electrónico:</dt>
										<dd class="col-md-10">
											<a href="mailto:{{ $oficialCumplimiento->email_copia }}">
												{{ $oficialCumplimiento->email_copia }}
											</a>
										</dd>
									@endif
								</dl>
							</div>
						</div>
						<div class="text-right">
		                    <a href="{{ url('oficialCumplimiento/edit') }}" class="btn btn-outline-primary pull-right">Editar</a>
		                </div>
					@endif
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
	$(function(){
		$("input[name='name']").enfocar();
		$(window).formularioCrear("{{ url('oficialCumplimiento/create') }}");
	});
</script>
@endpush

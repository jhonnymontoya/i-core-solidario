@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Archivos SES
			<small>Listas de control</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Control y Vigilancia</a></li>
			<li class="active">Listas de control</li>
		</ol>
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => ['listaControl', $lista->id], 'method' => 'put', 'id' => 'cargarArchivo', 'files' => true]) !!}
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Cargar registros</h3>
					</div>
					{{-- INICIO BOX BODY --}}
					<div class="box-body">
						<div class="row">
							<div class="col-md-12">
								<p>Actualizar lista de control: <strong>{{ $lista->tipo }}</strong></p>
							</div>
						</div>
						<div class="row form-horizontal">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('archivo')?'has-error':'') }}">
									<label class="col-md-5 control-label">
										@if ($errors->has('archivo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Seleccione archivo
									</label>
									<div class="col-md-7">
										{!! Form::file('archivo', ['class' => 'form-control', ]) !!}
										@if ($errors->has('archivo'))
											<span class="help-block">{{ $errors->first('archivo') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-8">
								{!! Form::submit('Cargar', ['class' => 'btn btn-success']) !!}
							</div>
						</div>
					</div>
					{{-- FIN BOX BODY --}}
					<div class="box-footer">
						<a href="{{ url('listaControl') }}" class="btn btn-danger pull-right">Volver</a>
						{!! Form::submit("Cargar", ["class" => 'btn btn-success pull-right']) !!}
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.box-footer > [type='submit'] {
		margin-right: 20px;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$cargando = false;
		$("#cargarArchivo").submit(function(event){
			$form = $(this);
			$submit = $form.find("input[type='submit']");
			$submit.addClass('disabled');
			$submit.val('Cargando....');
			if(!$cargando) {
				$cargando = true;
			}
			else {
				event.preventDefault();
			}
		});
	});
</script>
@endpush

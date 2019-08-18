@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Conceptos de recaudo
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Conceptos de recaudo</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			{!! Form::open(['url' => 'conceptosRecaudos', 'method' => 'post', 'role' => 'form']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Crear nuevo concepto de recaudo</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('pagaduria_id')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('pagaduria_id'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Pagaduria
							</label>
							{!! Form::select('pagaduria_id', $pagadurias, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione pagaduría', 'autofocus']) !!}
							@if ($errors->has('pagaduria_id'))
								<span class="help-block">{{ $errors->first('pagaduria_id') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('codigo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Código concepto
							</label>
							{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Código del concepto']) !!}
							@if ($errors->has('codigo'))
								<span class="help-block">{{ $errors->first('codigo') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre del concepto']) !!}
							@if ($errors->has('nombre'))
								<span class="help-block">{{ $errors->first('nombre') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('conceptosRecaudos') }}" class="btn btn-danger pull-right">Cancelar</a>
			</div>
			{!! Form::close() !!}
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
	});
</script>
@endpush

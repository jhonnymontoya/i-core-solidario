@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuentas
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Cuentas</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($cuenta, ['url' => ['cuentaContable', $cuenta], 'method' => 'put', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Editar cuenta contable</h3>

					<div class="card-tools pull-right">
						<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código de la cuenta</label>
								{!! Form::number('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código de la cuenta', 'min' => '1', 'step' => '1', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre de la cuenta</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre de la cuenta', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">							
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Nivel de la cuenta</label>
								<div id="id_level">{{ $cuenta->nivel }}</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Tipo de cuenta</label>
								<div id="id_tipo">{{ $cuenta->tipo_cuenta }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Categoría de la cuenta</label>
								<div id="id_categoria">{{ $cuenta->categoria }}</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label class="control-label">Cuenta padre</label>
								<div id="id_padre">{{ empty($cuenta->cuenta_padre) ? '' : $cuenta->cuenta_padre->nombre }}</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('naturaleza') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Naturaleza</label>
								{!! Form::select('naturaleza', ['DÉBITO' => 'Débito', 'CRÉDITO' => 'Crédito'], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Naturaleza de la cuenta']) !!}
								@if ($errors->has('naturaleza'))
									<div class="invalid-feedback">{{ $errors->first('naturaleza') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Módulo</label>
								{!! Form::text('modulo', (empty($cuenta->modulo) ? '' : $cuenta->modulo->nombre), ['class' => 'form-control', 'readonly']) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">¿Acepta saldos negativos?</label>
								<div>
									@php
										$valid = $errors->has('negativo') ? 'is-invalid' : '';
										$aceptaNegativos = empty(old('negativo')) ? $cuenta->acepta_saldo_negativo : old('negativo');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $aceptaNegativos ? 'active' : '' }}">
											{!! Form::radio('negativo', 1, ($aceptaNegativos ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$aceptaNegativos ? 'active' : '' }}">
											{!! Form::radio('negativo', 0, (!$aceptaNegativos ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('negativo'))
										<div class="invalid-feedback">{{ $errors->first('negativo') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">¿Es cuenta de resultado (PYG)?</label>
								<div>
									@php
										$valid = $errors->has('resultado') ? 'is-invalid' : '';
										$esCuentaResultado = empty(old('resultado')) ? $cuenta->es_pyg : old('resultado');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $esCuentaResultado ? 'active' : '' }}">
											{!! Form::radio('resultado', 1, ($esCuentaResultado ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$esCuentaResultado ? 'active' : '' }}">
											{!! Form::radio('resultado', 0, (!$esCuentaResultado ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('resultado'))
										<div class="invalid-feedback">{{ $errors->first('resultado') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">¿Es cuenta de orden?</label>
								<div>
									@php
										$valid = $errors->has('ordent') ? 'is-invalid' : '';
										$cuentaOrden = empty(old('ordent')) ? $cuenta->cuenta_orden : old('ordent');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $cuentaOrden ? 'active' : '' }}">
											{!! Form::radio('ordent', 1, ($cuentaOrden ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$cuentaOrden ? 'active' : '' }}">
											{!! Form::radio('ordent', 0, (!$cuentaOrden ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('ordent'))
										<div class="invalid-feedback">{{ $errors->first('ordent') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-6" id="id_orden" style="display:{{ !empty($cuenta->cuenta_orden) ? 'block' : 'none'}};">
							<div class="form-group">
								@php
									$valid = $errors->has('orden') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta de compensación</label>
								{!! Form::number('orden', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Cuenta de compensación', 'min' => '1', 'step' => '1']) !!}
								@if ($errors->has('orden'))
									<div class="invalid-feedback">{{ $errors->first('orden') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">¿Activo?</label>
								<div>
									@php
										$valid = $errors->has('esta_activo') ? 'is-invalid' : '';
										$estaActivo = empty(old('esta_activo')) ? $cuenta->esta_activo : old('esta_activo');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activo', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activo', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activo'))
										<div class="invalid-feedback">{{ $errors->first('esta_activo') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('comentario') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Comentario</label>
								{!! Form::textarea('comentario', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Comentario de la cuenta']) !!}
								@if ($errors->has('comentario'))
									<div class="invalid-feedback">{{ $errors->first('comentario') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('cuentaContable') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
				{!! Form::close() !!}
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

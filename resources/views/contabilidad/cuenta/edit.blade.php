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
							<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('codigo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Código de la cuenta
								</label>
								{!! Form::number('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código de la cuenta', 'min' => '1', 'step' => '1', 'autocomplete' => 'off', 'readonly']); !!}
								@if ($errors->has('codigo'))
									<span class="help-block">{{ $errors->first('codigo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre de la cuenta
								</label>
								{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre de la cuenta', 'autocomplete' => 'off', 'autofocus']); !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
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
							<div class="form-group {{ ($errors->has('naturaleza')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('naturaleza'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Naturaleza
								</label>
								{!! Form::select('naturaleza', ['DÉBITO' => 'Débito', 'CRÉDITO' => 'Crédito'], null, ['class' => 'form-control', 'placeholder' => 'Naturaleza de la cuenta']) !!}
								@if ($errors->has('naturaleza'))
									<span class="help-block">{{ $errors->first('naturaleza') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('modulo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('modulo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Módulo
								</label>
								{!! Form::text('modulo', (empty($cuenta->modulo) ? '' : $cuenta->modulo->nombre), ['class' => 'form-control', 'readonly']) !!}
								@if ($errors->has('modulo'))
									<span class="help-block">{{ $errors->first('modulo') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('negativo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('negativo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Acepta saldos negativos?
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $cuenta->acepta_saldo_negativo ? 'active' : '' }}">
										{!! Form::radio('negativo', '1', $cuenta->acepta_saldo_negativo ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ !$cuenta->acepta_saldo_negativo ? 'active' : '' }}">
										{!! Form::radio('negativo', '0', !$cuenta->acepta_saldo_negativo ? true : false) !!}No
									</label>
								</div>
								@if ($errors->has('negativo'))
									<span class="help-block">{{ $errors->first('negativo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('resultado')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('resultado'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Es cuenta de resultados (PYG)?
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-{{ $cuenta->es_pyg ? 'primary' : 'danger' }} ">
										{{ $cuenta->es_pyg ? 'Sí' : 'No' }}
									</label>
								</div>
								@if ($errors->has('resultado'))
									<span class="help-block">{{ $errors->first('resultado') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('ordent')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('ordent'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Es cuenta de orden?
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-{{ !empty($cuenta->cuenta_orden) ? 'primary' : 'danger' }}">
										{{ !empty($cuenta->cuenta_orden) ? 'Sí' : 'No' }}
									</label>
								</div>
								@if ($errors->has('ordent'))
									<span class="help-block">{{ $errors->first('ordent') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6" id="id_orden" style="display:{{ !empty($cuenta->cuenta_orden) ? 'block' : 'none'}};">
							<div class="form-group {{ ($errors->has('orden')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('orden'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Cuenta de compensación
								</label>
								<br>
								{!! Form::number('orden', $cuenta->cuenta_orden, ['class' => 'form-control', 'placeholder' => 'Código de la cuenta', 'min' => '1', 'step' => '1', 'autocomplete' => 'off', 'readonly']); !!}
								@if ($errors->has('orden'))
									<span class="help-block">{{ $errors->first('orden') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('esta_activo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Estado
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $cuenta->esta_activo ? 'active' : '' }}">
										{!! Form::radio('esta_activo', '1', $cuenta->esta_activo ? true : false) !!}Activa
									</label>
									<label class="btn btn-outline-danger {{ !$cuenta->esta_activo ? 'active' : '' }}">
										{!! Form::radio('esta_activo', '0', !$cuenta->esta_activo ? true : false) !!}Inactiva
									</label>
								</div>
								@if ($errors->has('esta_activo'))
									<span class="help-block">{{ $errors->first('esta_activo') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group {{ ($errors->has('comentario')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('comentario'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Comentario
								</label>
								{!! Form::textarea('comentario', null, ['class' => 'form-control', 'placeholder' => 'Comentario de la cuenta']) !!}
								@if ($errors->has('comentario'))
									<span class="help-block">{{ $errors->first('comentario') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
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

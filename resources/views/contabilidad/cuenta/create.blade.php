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
				{!! Form::open(['url' => 'cuentaContable', 'method' => 'post', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Crear nueva cuenta contable</h3>

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
								{!! Form::number('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código de la cuenta', 'min' => '1', 'step' => '1', 'autocomplete' => 'off', 'autofocus']); !!}
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
								{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre de la cuenta', 'autocomplete' => 'off']); !!}
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
								<div id="id_level">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Tipo de cuenta</label>
								<div id="id_tipo">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Categoría de la cuenta</label>
								<div id="id_categoria">&nbsp;</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label class="control-label">Cuenta padre</label>
								<div id="id_padre">&nbsp;</div>
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
								{!! Form::select('modulo', $modulos, null, ['class' => 'form-control', 'placeholder' => 'Módulo de la cuenta']) !!}
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
									<label class="btn btn-outline-primary active">
										{!! Form::radio('negativo', '1', true) !!}Sí
									</label>
									<label class="btn btn-outline-danger">
										{!! Form::radio('negativo', '0', false) !!}No
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
									<label class="btn btn-outline-primary">
										{!! Form::radio('resultado', '1', false) !!}Sí
									</label>
									<label class="btn btn-outline-danger active">
										{!! Form::radio('resultado', '0', true) !!}No
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
									<label class="btn btn-outline-primary {{ old('ordent') == true ? 'active' : '' }}">
										{!! Form::radio('ordent', '1', old('ordent') == true ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ empty(old('ordent')) | old('ordent')  == false ? 'active' : '' }}">
										{!! Form::radio('ordent', '0', empty(old('ordent')) | old('ordent')  == false ? true : false) !!}No
									</label>
								</div>
								@if ($errors->has('ordent'))
									<span class="help-block">{{ $errors->first('ordent') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6" id="id_orden" style="display:{{ old('ordent') == true ? 'block' : 'none'}};">
							<div class="form-group {{ ($errors->has('orden')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('orden'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Cuenta de compensación
								</label>
								<br>
								{!! Form::number('orden', null, ['class' => 'form-control', 'placeholder' => 'Código de la cuenta', 'min' => '1', 'step' => '1', 'autocomplete' => 'off']); !!}
								@if ($errors->has('orden'))
									<span class="help-block">{{ $errors->first('orden') }}</span>
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
<script type="text/javascript">

	$("input[name='codigo']").keyup(function(){
		f_verify(this);
	});

	$("input[name='codigo']").change(function(){
		f_verify(this);
	});

	f_verify($("input[name='codigo']"));

	function f_verify(obj){
		var vCode = $(obj).val();
		var vLevel = getLevel(vCode);
		if(vLevel == 0){
			$("#id_level").html("&nbsp;");
		}
		else{
			$("#id_level").html(vLevel);
		}
		var vKind = getKind(vLevel);
		$("#id_tipo").html(vKind);
		var vCategory = getCategory(vCode);
		$("#id_categoria").html(vCategory);

		$.ajax({
				url: "padre",
				method: 'GET',
				dataType: 'json',
				data: "codigo=" + vCode,
		}).done(function(data){
			if(!$.isEmptyObject(data)){
				$("#id_padre").text(data.codigo + ' - ' + data.nombre);
			}
			else
			{
				$("#id_padre").text('');
			}
		}).fail(function(data){
			$("#id_padre").text('');
		});
		
	}

	function validateCode(code){
		var patt = /^([1-9]([1-9](([0-9][1-9])|([1-9][0-9])){0,8})?)?$/g;
		return patt.test(code);
	}

	function getLevel(code){
		var v_len = code.length;
		var level = 0;
		switch(v_len){
			case 1:level = 1;break;
			case 2:level = 2;break;
			case 4:level = 3;break;
			case 6:level = 4;break;
			case 8:level = 5;break;
			case 10:level = 6;break;
			case 12:level = 7;break;
			case 14:level = 8;break;
			case 16:level = 9;break;
			case 18:level = 10;break;
			default:level = 0;break;
		}
		return level;
	}

	function getKind(level){
		var kind = "";
		switch(level){
			case 1:kind = "CLASE";break;
			case 2:kind = "GRUPO";break;
			case 3:kind = "CUENTA";break;
			case 4:kind = "SUBCUENTA";break;
			case 5:case 6:case 7:case 8:case 9:case 10:kind = "AUXILIAR";break;
			default:kind = "";break;
		}
		return kind;
	}

	function getCategory(code){
		var digit = code[0];
		var category = "";
		switch(digit){
			case "1":case "2":case "3":category = "BALANCE GENERAL";break;
			case "4":case "5":case "6":case "7":category = "ESTADO DE GANANCIA O PÉRDIDAS";break;
			case "8":case "9":category = "ORDEN";break;
			default:category = "";break;
		}
		return category;
	}

	$('input[name="ordent"][value="1"]').change(function()
	{
		if($(this).is(":checked")) {
			$("#id_orden").show();
			$("input[name='orden']").enfocar();
		}
	});
	$('input[name="ordent"][value="0"]').change(function()
	{
		if($(this).is(":checked")) {
			$("input[name='orden']").val("");
			$("#id_orden").hide();
		}
	});
</script>
@endpush

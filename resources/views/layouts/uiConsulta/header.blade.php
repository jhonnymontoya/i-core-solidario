<header class="main-header">
	@php
		$usuario = Auth::user();
		$socio = $usuario->socios[0];
		$tercero = $socio->tercero;
		$nombre = title_case($tercero->nombre_corto);
		$fechaAfiliacion = $socio->fecha_antiguedad->diffForHumans();

		$imagen = $socio->avatar;
		if(!empty($imagen)) {
			$imagen = sprintf("storage/asociados/%s", $imagen);
		}
		else {
			$imagen = "storage/asociados/avatar-160x160.png";
		}
		$imagen = asset($imagen);
	@endphp
	
	{{-- Header Navbar: style can be found in header.less --}}
	<nav class="navbar navbar-static-top">
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				{{-- Cuenta de usuario --}}
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="{{ $imagen }}" class="user-image" alt="{{ $nombre }}">
						<span class="hidden-xs">{{ $nombre }}</span>
					</a>
					<ul class="dropdown-menu">
						{{-- Imagen del usuario --}}
						<li class="user-header">
							<img src="{{ $imagen }}" class="img-circle" alt="{{ $nombre }}">
							<p>
								{{ $nombre }}
								<small>Socio desde {{ $fechaAfiliacion }}</small>
								@if(Session::has('entidad'))
									<small>
										@if(Session::has('entidad'))
											<small>
												@if(!empty(Session::get('entidad')->terceroEntidad->sigla))
													{{ str_limit(Session::get('entidad')->terceroEntidad->sigla, 20) }}
												@else
													{{ str_limit(Session::get('entidad')->terceroEntidad->razon_social, 20) }}
												@endif
											</small>
										@endif
									</small>
								@endif
							</p>
						</li>
						{{-- Pie del menú --}}
						<li class="user-footer">
							<table width="100%">
								<tbody>
									<tr>
										<td width="33%">
											{!! Form::open(['url' => 'consulta/perfil', 'method' => 'get']) !!}
											{!! Form::submit('Perfil', ['class' => 'btn btn-outline-info btn-flat']) !!}
											{!! Form::close() !!}
										</td>
										<td width="33%" align="center">
											{{--
											@if (Auth::user()->perfiles()->count() > 1)
												{!! Form::open(['url' => 'entidad/seleccion', 'method' => 'get']) !!}
												{!! Form::submit('Cambiar entidad', ['class' => 'btn btn-outline-warning btn-flat']) !!}
												{!! Form::close() !!}
											@endif
											--}}
										</td>
										<td width="33%" align="right">
											{!! Form::open(['url' => 'logout', 'method' => 'post']) !!}
											{!! Form::submit('Salir', ['class' => 'btn btn-outline-danger btn-flat']) !!}
											{!! Form::close() !!}
										</td>
									</tr>
								</tbody>
							</table>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
</header>
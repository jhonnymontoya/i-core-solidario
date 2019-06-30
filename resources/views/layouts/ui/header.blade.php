<header class="main-header">
	{{-- Logo --}}
	<a href="{{ url('/dashboard') }}" class="logo">
		{{-- mini logo for sidebar mini 50x50 pixels --}}
		<span class="logo-mini">
			<img src="{{ asset('img/logo32x32.png') }}">
		</span>
		{{-- logo for regular state and mobile devices --}}
		<span class="logo-lg">I-<b>Core</b></span>
	</a>
	{{-- Header Navbar: style can be found in header.less --}}
	<nav class="navbar navbar-static-top">
		{{-- Sidebar toggle button--}}
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
			<span class="sr-only">Alternar navegación</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>

		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				@if (!empty(Auth::getSession()->get('entidad')))
					@php
						$terceroEntidad = Auth::getSession()->get('entidad')->terceroEntidad;
					@endphp
					<li><a href="{{ url('entidad/seleccion') }}">{{ $terceroEntidad->sigla }}</a></li>
				@endif
				{{-- Mensajes --}}
				<li class="dropdown messages-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-envelope-o"></i>
					</a>
					<ul class="dropdown-menu">
						<li class="header">Tiene 0 mensajes</li>
						<li>
							<ul class="menu">
								<li>
								</li>
							</ul>
						</li>
						<li class="footer"><a href="#">Ver todos los mensajes</a></li>
					</ul>
				</li>
				{{-- Notificaciones --}}
				<li class="dropdown notifications-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-bell-o"></i>
					</a>
					<ul class="dropdown-menu">
						<li class="header">Tiene 0 notificaciones</li>
						<li>
							<ul class="menu">
								<li>
								</li>
							</ul>
						</li>
						<li class="footer"><a href="#">Ver todas</a></li>
					</ul>
				</li>
				{{-- Tareas --}}
				<li class="dropdown tasks-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-flag-o"></i>
					</a>
					<ul class="dropdown-menu">
						<li class="header">Tiene 0 tareas</li>
						<li>
							<ul class="menu">
								<li>
								</li>
							</ul>
						</li>
						<li class="footer">
							<a href="#">Ver todas las tareas</a>
						</li>
					</ul>
				</li>
				{{-- Cuenta de usuario --}}
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="{{ asset('storage/avatars/' . (empty(Auth::user()->avatar)?'avatar-160x160.png':Auth::user()->avatar) ) }}" class="user-image" alt="{{ Auth::user()->nombre_corto }}">
						<span class="hidden-xs">{{ Auth::user()->nombre_corto }}</span>
					</a>
					<ul class="dropdown-menu">
						{{-- Imagen del usuario --}}
						<li class="user-header">
							<img src="{{ asset('storage/avatars/' . (empty(Auth::user()->avatar)?'avatar-160x160.png':Auth::user()->avatar) ) }}" class="img-circle" alt="{{ Auth::user()->nombre_corto }}">
							<p>
								{{ Auth::user()->nombre_corto }}
								<small>Usuario desde {{ Auth::user()->created_at->diffForHumans() }}</small>
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
						{{-- Cuerpo del menú --}}
						<li class="user-body">
							<div class="row">
								<div class="col-xs-6 text-center">
									<a href="#">Mis notificaciones</a>
								</div>
								<div class="col-xs-6 text-center">
									<a href="#">Mis transacciones</a>
								</div>
							</div>
						</li>
						{{-- Pie del menú --}}
						<li class="user-footer">
							<table width="100%">
								<tbody>
									<tr>
										<td width="33%">
											{!! Form::open(['url' => 'profile', 'method' => 'get']) !!}
											{!! Form::submit('Perfil', ['class' => 'btn btn-info btn-flat']) !!}
											{!! Form::close() !!}
										</td>
										<td width="33%" align="center">
											@if (Auth::user()->perfiles()->count() > 1)
												{!! Form::open(['url' => 'entidad/seleccion', 'method' => 'get']) !!}
												{!! Form::submit('Cambiar entidad', ['class' => 'btn btn-warning btn-flat']) !!}
												{!! Form::close() !!}
											@endif
										</td>
										<td width="33%" align="right">
											{!! Form::open(['url' => 'logout', 'method' => 'post']) !!}
											{!! Form::submit('Salir', ['class' => 'btn btn-danger btn-flat']) !!}
											{!! Form::close() !!}
										</td>
									</tr>
								</tbody>
							</table>
						</li>
					</ul>
				</li>
				{{-- Botón de configuración --}}
				<li>
					<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
				</li>
			</ul>
		</div>
	</nav>
</header>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link sidebar-toggle" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
		</li>
	</ul>

	<ul class="navbar-nav ml-auto">
		@if (!empty(Auth::getSession()->get('entidad')))
			@php
				$terceroEntidad = Auth::getSession()->get('entidad')->terceroEntidad;
			@endphp
			<li class="nav-item">
				<a class="nav-link" href="{{ url('entidad/seleccion') }}">{{ $terceroEntidad->sigla }}</a>
			</li>
		@endif
		<li class="nav-item">
			{!! Form::open(['url' => 'logout', 'method' => 'post']) !!}
			<button type="submit" class="btn btn-outline-danger"><i class="fa fa-sign-out-alt"></i></button>
			{!! Form::close() !!}
		</li>
	</ul>
</nav>
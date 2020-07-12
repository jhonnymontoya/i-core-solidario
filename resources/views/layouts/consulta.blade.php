@inject('data', 'App\Helpers\ConsultaHelper')
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>I-Core</title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

		<link rel="canonical" href="{{ url('/') }}">

		<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

		<!-- Google Font: Source Sans Pro -->
  		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" sizes="16x16 24x24 32x32 64x64"/>
		<link rel="apple-touch-icon" href="/img/logos/I-Core_iOS_60x60.png"/>
		<link rel="apple-touch-icon" sizes="76x76" href="/img/logos/I-Core_iOS_76x76.png"/>
		<link rel="apple-touch-icon" sizes="120x120" href="/img/logos/I-Core_iOS_120x120.png"/>
		<link rel="apple-touch-icon" sizes="152x152" href="/img/logos/I-Core_iOS_152x152.png"/>
		<link rel="image_src" href="/img/logos/I-Core_256x256.png"/>
		<link rel="manifest" href="/manifest.json"/>

		<meta name="application-name" content="I-Core, Moderno sistema para la gestión empresarial">

		<meta name="twitter:card" content="summary">
		<meta name="twitter:site" value="@jhonny_montoya">

		<meta property="og:url" content="{{ url('/') }}" />
		<meta property="og:site_name" content="I-Core" />
		<meta property="og:image" content="{{ asset('/img/logos/I-Core_256x256.png') }}">
		<meta property="og:title" content="I-Core" />
		<meta property="og:description" content="I-Core, Moderno sistema para la gestión empresarial">
		<meta name="description" content="I-Core, Moderno sistema para la gestión empresarial">

		@stack('style')

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body class="sidebar-mini layout-fixed">

		<div class="wrapper">
			{{-- Header --}}
			<nav class="main-header navbar navbar-expand navbar-white navbar-light">
				<ul class="navbar-nav d-sm-block d-md-block d-lg-none d-xl-none">
					<li class="nav-item">
						<a class="nav-link sidebar-toggle" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
					</li>
				</ul>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						{!! Form::open(['url' => 'logout', 'method' => 'post']) !!}
						<button type="submit" class="btn btn-outline-danger"><i class="fa fa-sign-out-alt"></i></button>
						{!! Form::close() !!}
					</li>
				</ul>
			</nav>

			{{-- menú izquierdo --}}
			<aside class="main-sidebar sidebar-light-danger elevation-0">
				<a href="{{ url('consulta') }}" class="brand-link">
					<img src="{{ asset('img/I-Core.png') }}" alt="I-Core" class="brand-image img-circle">
					<span class="brand-text font-weight-light">I-Core</span>
				</a>

				<section class="sidebar">
					<div class="user-panel mt-3 pb-3 mb-3 d-flex">
						<div class="image">
							<img src="{{ asset('storage/asociados/' . Auth::user()->socios[0]->obtenerAvatar()) }}" class="img-circle elevation-2" alt="{{ Auth::user()->socios[0]->tercero->nombre_corto }}">
						</div>
						<div class="info">
							<a href="{{ url('consulta/perfil') }}" class="d-block">{{ Auth::user()->socios[0]->tercero->nombre_corto }}</a>
						</div>
					</div>

					<nav class="mt-2">

						<a href="{{ url('consulta/ahorros/lista') }}">
							<div class="info-box bg-success">
								<span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
								<div class="info-box-content">
									<span class="info-box-text">Ahorros</span>
									<span class="info-box-number">${{ number_format($data->ahorros()->saldo) }}</span>
									<div class="progress">
										<div class="progress-bar" style="width: {{ $data->ahorros()->variacionAhorro }}%"></div>
									</div>
									<span class="progress-description">
										Incrementó {{ $data->ahorros()->variacionAhorro }}% en 30 Días
									</span>
								</div>
							</div>
						</a>

						<a href="{{ url('consulta/creditos/lista') }}">
							<div class="info-box bg-warning">
								<span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
								<div class="info-box-content">
									<span class="info-box-text">Créditos</span>
									<span class="info-box-number">${{ number_format($data->creditos()->saldo) }}</span>
									<div class="progress">
										<div class="progress-bar" style="width: {{ $data->creditos()->porcentajePago }}%"></div>
									</div>
									<span class="progress-description">
										Abonado {{ $data->creditos()->porcentajePago }}% del total
									</span>
								</div>
							</div>
						</a>

						<a href="{{ url('consulta/recaudos/lista') }}">
							<div class="info-box bg-primary">
								<span class="info-box-icon"><i class="fa fa-calendar"></i></span>
								<div class="info-box-content">
									<span class="info-box-text">Recaudos nómina</span>
									<span class="info-box-number">${{ number_format($data->recaudos()->aplicado) }}</span>
									<div style="height: 12px;">&nbsp;</div>
									<span class="progress-description">
										Aplicado en {{ $data->recaudos()->fechaRecaudo }}
									</span>
								</div>
							</div>
						</a>

						<div class="info-box bg-default">
							<span class="info-box-icon"><i class="fas fa-hammer"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Herramientas</span>
								<span class="info-box-number"><a href="{{ url('consulta/solicitarCredito') }}" class="link">Solicitar crédito</a></span>
								<span class="info-box-number"><a href="{{ url('consulta/simulador') }}" class="link">Simulador</a></span>
								<span class="info-box-number"><a href="{{ route('consulta.documentacion') }}" class="link">Documentación</a></span>
								<div style="height: 12px;">&nbsp;</div>
							</div>
						</div>
					</nav>
				</section>
			</aside>

			{{-- contenido --}}
			@yield('content')
			{{-- footer --}}
			@include('layouts.uiConsulta.footer')
		</div>

		<script type="text/javascript" src="{{ asset('js/jLbqUZ.js') }}"></script>

		@stack('scripts')
	</body>
</html>

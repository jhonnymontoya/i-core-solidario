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
	<body class="sidebar-mini layout-fixed {{ Auth::getUser()->ui_configuracion }}">

		<div class="wrapper">
			{{-- Header --}}
			@include('layouts.ui.header')
			{{-- menú izquierdo --}}
			@include('layouts.ui.leftaside')
			{{-- contenido --}}
			@yield('content')
			{{-- footer --}}
			@include('layouts.ui.footer')
		</div>

		<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

		@stack('scripts')
	</body>
</html>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>I-Core</title>
		
		<link rel="canonical" href="{{ url('/') }}">
		
		<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css?ver=2') }}">
		
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" sizes="16x16 24x24 32x32 64x64"/>
		<link rel="apple-touch-icon" href="/img/logos/ICore_iOS_60x60.png"/>
		<link rel="apple-touch-icon" sizes="76x76" href="/img/logos/ICore_iOS_76x76.png"/>
		<link rel="apple-touch-icon" sizes="120x120" href="/img/logos/ICore_iOS_120x120.png"/>
		<link rel="apple-touch-icon" sizes="152x152" href="/img/logos/ICore_iOS_152x152.png"/>
		<link rel="image_src" href="/img/logos/ICore_256x256.png"/>
		<link rel="manifest" href="/manifest.json"/>
		
		<meta name="application-name" content="ERP para la administración de información para fondos de empleados - I-Core, un producto de Start Line Soft SAS">
		
		<meta name="twitter:card" content="summary">
		<meta name="twitter:site" value="@jhonny_montoya">

		<meta property="og:url" content="{{ url('/') }}" />
		<meta property="og:site_name" content="I-Core" />
		<meta property="og:image" content="{{ asset('/img/logos/ICore_256x256.png') }}">		
		<meta property="og:title" content="I-Core" />
		<meta property="og:description" content="ERP para la administración de información para fondos de empleados - I-Core, un producto de Start Line Soft SAS">
		<meta name="description" content="ERP para la administración de información para fondos de empleados - I-Core, un producto de Start Line Soft SAS">

		@stack('style')

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	
	<body style="width: 100%; margin: 0 auto;">
		<div class="login-page">
			<div class="login-box">
				@yield('content')
			</div>
		</div>

		<script type="text/javascript" src="{{ asset('js/app.js?ver=2') }}"></script>
		@stack('scripts')
	</body>
</html>
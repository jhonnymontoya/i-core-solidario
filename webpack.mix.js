const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.scripts([
		'resources/assets/js/jquery-3.4.1.js',
		'resources/assets/js/jquery-migrate-3.1.0.js',
		'resources/assets/js/bootstrap.bundle.js',
		'resources/assets/js/jquery.knob.js',
		'resources/assets/js/bootstrap-datepicker.js',
		'resources/assets/js/bootstrap-datepicker.es.js',
		'resources/assets/js/fastclick.js',
		'resources/assets/js/adminlte.js',
		'resources/assets/js/tilt.jquery.js',
		'resources/assets/js/jquery.overlayScrollbars.js',
		'resources/assets/js/select2.full.js',
		'resources/assets/js/raphael.js',
		'resources/assets/js/jquery.maskMoney.js',
		'resources/assets/js/jquery.mask.js',
		'resources/assets/js/jquery.cropit.js',
		'resources/assets/js/morris.js',
		'resources/assets/js/Chart.bundle.js',
		'resources/assets/js/clipboard.js',
		'resources/assets/js/sweetalert2.js',
		'resources/assets/js/Configuracion.js',
		'resources/assets/js/Funciones.js',
	], 'public/js/app.js')
	.styles([
		'resources/assets/css/fontawesome.css',
		'resources/assets/css/adminlte.css',
		'resources/assets/css/select2.css',
		'resources/assets/css/morris.css',
		'resources/assets/css/datepicker3.css',
		'resources/assets/css/OverlayScrollbars.css',
		'resources/assets/css/sweetalert2.css',
		'resources/assets/css/app.css'
	], 'public/css/app.css');

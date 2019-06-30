let mix = require('laravel-mix');

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
		'resources/assets/js/jquery-2.2.3.js',
		////'resources/assets/js/jquery.js',
		'resources/assets/js/bootstrap.js',
		'resources/assets/js/bootstrap-checkbox.js',
		'resources/assets/js/jquery-ui.js',
		//'resources/assets/js/conflict-tooltip.js',
		'resources/assets/js/jquery.dataTables.js',
		'resources/assets/js/dataTables.bootstrap.js',
		'resources/assets/js/raphael.js',
		'resources/assets/js/morris.js',
		//'resources/assets/js/bootstrap-slider.js',
		//'resources/assets/js/bootstrap3-wysihtml5.all.js',
		'resources/assets/js/jquery.maskMoney.js',
		'resources/assets/js/jquery.mask.js',
		//'resources/assets/js/bootstrap.file-input.js',
		'resources/assets/js/jquery.cropit.js',
		//'resources/assets/js/Chart.js',
		'resources/assets/js/Chart.bundle.js',
		'resources/assets/js/jquery.knob.js',
		'resources/assets/js/bootstrap-datepicker.js',
		'resources/assets/js/bootstrap-datepicker.es.js',
		'resources/assets/js/select2.full.js',
		'resources/assets/js/app.js',
		'resources/assets/js/Configuracion.js',
		'resources/assets/js/Funciones.js',
		'resources/assets/js/jszip.js',
		'resources/assets/js/xlsx.js',
		'resources/assets/js/FileSaver.js',
		'resources/assets/js/TableExport.js'
	], 'public/js/app.js')
   .styles([
   		'resources/assets/css/jquery-ui.css',
   		'resources/assets/css/bootstrap.css',
   		'resources/assets/css/bootstrap-slider.css',
   		'resources/assets/css/font-awesome.css',
   		'resources/assets/css/dataTables.bootstrap.css',
   		'resources/assets/css/ionicons.css',
   		'resources/assets/css/select2.css',
   		'resources/assets/css/morris.css',
   		//'resources/assets/css/slider.css',
   		//'resources/assets/css/bootstrap3-wysihtml5.css',
   		'resources/assets/css/datepicker3.css',
   		'resources/assets/css/AdminLTE.css',
   		'resources/assets/css/_all-skins.css',
   		'resources/assets/css/app.css'
   	], 'public/css/app.css');

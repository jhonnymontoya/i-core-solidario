<?php

/***********************************************************************
 * RUTAS DE APIS MOVIL
 ***********************************************************************/

//Rutas de Login
\App\Http\Controllers\Api\Auth\LoginController::routes();

//Rutas de Socio
\App\Http\Controllers\Api\Socio\SocioController::routes();

//Rutas de Ahorros
\App\Http\Controllers\Api\Ahorros\AhorrosController::routes();

//Rutas de Créditos
\App\Http\Controllers\Api\Creditos\CreditosController::routes();


/***********************************************************************
 * FIN RUTAS DE APIS MOVIL
 ***********************************************************************/


/***************************************************************************************
 * Rutas de Generales
 ***************************************************************************************/
//Rutas de Profesiones
Route::get('profesion', 'General\ProfesionController@getProfesion');

//Rutas de ciudad
Route::get('ciudad', 'General\CiudadController@ciudad');

//Rutas de Entidad
Route::put('entidad/directivo', 'General\EntidadController@agregarDirectivo');
Route::put('entidad/legal', 'General\EntidadController@agregarRepresentanteLegal');
Route::put('entidad/controlSocial', 'General\EntidadController@agregarControlSocial');
Route::put('entidad/comiteCartera', 'General\EntidadController@agregarComiteCartera');
Route::put('entidad/comiteRiesgoLiquidez', 'General\EntidadController@agregarComiteRiesgoLiquidez');
Route::delete('entidad/directivo', 'General\EntidadController@eliminarOrganismo');

/***************************************************************************************
 * Rutas de Socios
 ***************************************************************************************/
//Rutas de socio
Route::get('socio', 'Socios\SocioController@socio');
Route::post('socio/beneficiario', 'Socios\SocioController@beneficiario');

/***************************************************************************************
 * Rutas de Tesorería
 ***************************************************************************************/

//Rutas de tipos de comprobantes
Route::get('tipoComprobante', 'Contabilidad\TipoComprobanteController@getTipoComprobante');


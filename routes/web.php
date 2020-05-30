<?php

/***************************************************************************************
 * Rutas de Sistema
 ***************************************************************************************/
    //Rutas de menú
    \App\Http\Controllers\Sistema\MenuController::routes();

    //Rutas de perfil
    \App\Http\Controllers\Sistema\PerfilController::routes();

    //Rutas de usuario
    \App\Http\Controllers\Sistema\UsuarioController::routes();

    //Rutas de profile
    \App\Http\Controllers\Sistema\ProfileController::routes();

    //Rutas de Retroalimentacion de notificaciones
    \App\Http\Controllers\Sistema\NotificacionesRetroalimentacionController::routes();

/***************************************************************************************
 * Rutas de Generales
 ***************************************************************************************/
    //Rutas de entidad
    \App\Http\Controllers\General\EntidadController::routes();

    //Rutas de Categorías de imágenes institucionales
    \App\Http\Controllers\General\CategoriaImagenController::routes();

    //Rutas de tipos de identificación
    \App\Http\Controllers\General\TipoIdentificacionController::routes();

    //Rutas de tipos de indicadores
    \App\Http\Controllers\General\TipoIndicadorController::routes();

    //Rutas de indicadores
    \App\Http\Controllers\General\IndicadorController::routes();

    //Rutas de parametros institucionales
    \App\Http\Controllers\General\ParametroInstitucionalController::routes();

    //Rutas de terceros
    \App\Http\Controllers\General\TerceroController::routes();

    //Rutas de Ciiu's
    \App\Http\Controllers\General\CiiuController::routes();

    //Rutas de Cierre de módulos
    \App\Http\Controllers\General\CierreModulosController::routes();

/***************************************************************************************
 * Rutas de contabilidad
 ***************************************************************************************/

    //Rutas de Comprobantes
    \App\Http\Controllers\Contabilidad\ComprobanteController::routes();

    //Rutas de Cuenta
    \App\Http\Controllers\Contabilidad\CuifController::routes();

    //Rutas de Módulo
    \App\Http\Controllers\Contabilidad\ModuloController::routes();

    //Rutas de Causas de anulación de movimientos contables
    \App\Http\Controllers\Contabilidad\CausasAnulacionMovimientosController::routes();

    //Rutas de tipos de comprobantes contables
    \App\Http\Controllers\Contabilidad\TipoComprobanteController::routes();

    //Rutas de impuesto
    \App\Http\Controllers\Contabilidad\ImpuestoController::routes();

    //Rutas de información tributaria
    \App\Http\Controllers\Contabilidad\InformacionTributariaController::routes();

/***************************************************************************************
 * Rutas de Socios
 ***************************************************************************************/

    //Rutas de afiliación
    \App\Http\Controllers\Socios\SocioController::routes();

    //Rutas de tipos cuotas obligatorias
    \App\Http\Controllers\Socios\TipoCuotaObligatoriaController::routes();

    //Rutas de cuotas obligatorias
    \App\Http\Controllers\Socios\CuotaObligatoriaController::routes();

    //Rutas de retiros de socios
    \App\Http\Controllers\Socios\RetiroSocioController::routes();

/***************************************************************************************
 * Rutas de Reportes
 ***************************************************************************************/

    //Rutas de Reportes
    \App\Http\Controllers\Reportes\ReportesController::routes();

/***************************************************************************************
 * Rutas de Ahorros
 ***************************************************************************************/

    //Rutas de tipos de cuotas de ahorros
    \App\Http\Controllers\Ahorros\TipoCuotaAhorrosController::routes();

    //Rutas de cuotas voluntarias
    \App\Http\Controllers\Ahorros\CuotaVoluntariaController::routes();

    //Rutas de ajuste de ahorros
    \App\Http\Controllers\Ahorros\AjusteAhorrosController::routes();

    //Rutas de ajustes de ahorros en lote
    \App\Http\Controllers\Ahorros\AjusteAhorrosLoteController::routes();

    //Rutas de cuentas de ahorros
    \App\Http\Controllers\Ahorros\CuentaAhorrosController::routes();

    //Rutas de tipo de cuentas de ahorros
    \App\Http\Controllers\Ahorros\TipoCuentaAhorrosController::routes();

    //Rutas de tipos SDAT
    \App\Http\Controllers\Ahorros\TipoSDATController::routes();

    //Rutas de SDAT
    \App\Http\Controllers\Ahorros\SDATController::routes();

/***************************************************************************************
 * Rutas de Creditos
 ***************************************************************************************/

    //Rutas de modalidades de creditos
    \App\Http\Controllers\Creditos\ModalidadCreditoController::routes();

    //Rutas de parámetros contables
    \App\Http\Controllers\Creditos\ParametrosContablesController::routes();

    //Rutas de cupos de creditos
    \App\Http\Controllers\Creditos\CupoCreditoController::routes();

    //Rutas de solicitud de credito
    \App\Http\Controllers\Creditos\SolicitudCreditoController::routes();

    //Rutas de solicitud de credito
    \App\Http\Controllers\Creditos\AjusteCreditosController::routes();

    //Rutas de solicitud de credito en lote (procesoCreditoLote)
    \App\Http\Controllers\Creditos\ProcesoCreditoLoteController::routes();

    //Rutas de tipos de garantias
    \App\Http\Controllers\Creditos\TipoGarantiaController::routes();

    //Rutas de reliquidación de créditos
    \App\Http\Controllers\Creditos\ReliquidarCreditoController::routes();

    //Rutas de seguros de cartera
    \App\Http\Controllers\Creditos\SeguroCarteraController::routes();

    //Rutas de ajustes de crédito en lote
    \App\Http\Controllers\Creditos\AjusteCreditosLoteController::routes();

    //Rutas de cobros administrativos
    \App\Http\Controllers\Creditos\CobroAdministrativoController::routes();

    //Rutas de calificación de cartera
    \App\Http\Controllers\Creditos\ParametrosCalificacionCarteraController::routes();

    //Rutas de deterioro individual
    \App\Http\Controllers\Creditos\ParametrosDeteioroIndividualController::routes();

/***************************************************************************************
 * Rutas de Recaudos
 ***************************************************************************************/

    //Rutas de pagadurias
    \App\Http\Controllers\Recaudos\PagaduriaController::routes();

    //Rutas de conceptos de recaudos
    \App\Http\Controllers\Recaudos\ConceptosRecaudosController::routes();

    //Rutas de recaudos de nómina
    \App\Http\Controllers\Recaudos\RecaudoNominaController::routes();

    //Rutas de recaudos por caja
    \App\Http\Controllers\Recaudos\RecaudoCajaController::routes();

    //Rutas de recaudos desde ahorros
    \App\Http\Controllers\Recaudos\RecaudoAhorroController::routes();

/***************************************************************************************
 * Rutas de Tesorería
 ***************************************************************************************/

    //Rutas de bancos
    \App\Http\Controllers\Tesoreria\BancoController::routes();

/***************************************************************************************
 * Rutas de Tarjetas
 ***************************************************************************************/

    //Rutas de productos
    \App\Http\Controllers\Tarjeta\ProductoController::routes();

    //Rutas de tarjetaabientes
    \App\Http\Controllers\Tarjeta\TarjetaHabienteController::routes();

    //Rutas de tarjetas
    \App\Http\Controllers\Tarjeta\TarjetaController::routes();

/***************************************************************************************
 * Rutas de Control y Vigilancia
 ***************************************************************************************/

    //Rutas de Archivos SES (Super intendencia economia solidaria)
    \App\Http\Controllers\ControlVigilancia\ArchivosSESController::routes();

    //Rutas de Listas de control SARLAF
    \App\Http\Controllers\ControlVigilancia\ListaControlController::routes();

/***************************************************************************************
 * Rutas de Home
 ***************************************************************************************/

    //Rutas de logueo
    App\Http\Controllers\Auth\LoginController::routes();
    //Rutas de restaurado de contraseñas
    App\Http\Controllers\Auth\ForgotPasswordController::routes();
    App\Http\Controllers\Auth\ResetPasswordController::routes();
    //Rutas del home y dashboard
    App\Http\Controllers\DashboardController::routes();

/***************************************************************************************
 * Rutas de Consulta Web
 ***************************************************************************************/

    //Rutas de la consulta de socios
    \App\Http\Controllers\Consulta\ConsultaController::routes();

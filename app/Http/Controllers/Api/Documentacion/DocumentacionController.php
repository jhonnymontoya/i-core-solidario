<?php
namespace App\Http\Controllers\Api\Documentacion;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentacionController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }



    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::get(
            '1.0/documentacion/certificadoTributario',
            'Api\Documentacion\DocumentacionController@certificadoTributario'
        );
    }
}

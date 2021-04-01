<?php
namespace App\Http\Controllers\Api\Documentacion;

use Route;
use Validator;
use App\Traits\ICoreTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Certificados\CertificadoTributario;
use App\Mail\Consulta\DocumenacionCertificadoTributario;

class DocumentacionController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function certificadoTributario(Request $request)
    {
        $usuario = $request->user();
        $entidadId = $this->getEntidadIdParaApi($usuario->usuario);
        $socio = $usuario->socios[0];
        $tercero = $socio->tercero;
        $correos = $tercero->getCorreos();

        if($correos->count() == 0)
        {
            return response()->json(
                ["mensaje" => "No cuenta con correos electrónicos registrados, contácte un funcionaio del fondo de empreados."],
                400
            );
        }

        $anioIc = $tercero->entidad->fecha_inicio_contabilidad->year;
        $ai = $anioIc > 2018 ? $anioIc : 2018;
        $v = Validator::make($request->all(), [
            "anio" => [
                "bail",
                "required",
                "integer",
                "min:" . $ai,
                "max:3000"
            ]
        ]);
        if($v->fails())
        {
            return response()->json(
                ["mensaje" => "No se pudo procesar los datos (Año no válido)"],
                400
            );
        }
        $log = "API: Usuario '%s' consultó el certificado tributario.";
        $log = sprintf($log, $usuario->usuario);
        $this->log($log, 'CONSULTAR', $entidadId);

        $pdf = null;
        $pdf = new CertificadoTributario($socio, $request->anio);

        $mails = "";
        foreach ($correos as $key => $value) {
            if(strlen($mails) == 0)
            {
                $mails .= $key;
            }
            else
            {
                $mails .= " y " . $key;
            }
        }
        $correo = new DocumenacionCertificadoTributario(
            $tercero,
            $pdf->getRuta(),
            $request->anio
        );
        Mail::to($correos->keys())->send($correo);
        $mensaje = "Se ha enviado su certificado tributario a: " . $mails;
        return response()->json(["respuesta" => $mensaje], 200);
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

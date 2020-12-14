<?php

namespace App\Http\Controllers\Api\Socio;

use Route;
use App\Api\Ahorros;
use App\Api\Creditos;
use App\Api\Recaudos;
use App\Traits\ICoreTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Socios\Beneficiario;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\General\ParametroInstitucional;


class SocioController extends Controller
{
    use ICoreTrait;

    const POSITIVO = 'POSITIVO';
    const NEGATIVO = 'NEGATIVO';

    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'sendResetLinkEmail']);
    }

    /**
     * Retorna información del socio
     */
    public function socio(Request $request)
    {
        $usuario = $request->user();
        $this->log("API: Ingresó al dashboard: " . $usuario->usuario, 'CONSULTAR');
        $usuario = $request->user();
        $socio = $this->getSocio($usuario);
        return response()->json($socio);
    }

    /**
     * Retorna información relacionada con el perfil del asociado
     */
    public function perfil(Request $request)
    {
        $usuario = $request->user();
        $this->log("API: Ingresó al perfil: " . $usuario->usuario, 'CONSULTAR');
        $usuario = $request->user();
        $perfil = $this->getPerfil($usuario);
        return response()->json($perfil);
    }

    /**
     * Retorna los beneficiarios del asociado
     */
    public function beneficiarios(Request $request)
    {
        $usuario = $request->user();
        $this->log("API: Ingresó al beneficiarios: " . $usuario->usuario, 'CONSULTAR');
        $usuario = $request->user();
        $perfil = $this->getBeneficiarios($usuario);
        return response()->json($perfil);
    }

    private function getSocio($usuario) {
        $socio = $usuario->socios[0];
        $tercero = $socio->tercero;
        $data = [
            "tipoIdentificacion" => $tercero->tipoIdentificacion->codigo,
            "identificacion" => number_format($tercero->numero_identificacion, 0),
            "primerNombre" => Str::Title($tercero->primer_nombre),
            "segundoNombre" => Str::Title($tercero->segundo_nombre),
            "primerApellido" => Str::Title($tercero->primer_apellido),
            "segundoApellido" => Str::Title($tercero->segundo_apellido),
            "nombre" => Str::Title($tercero->nombre_corto),
            "entidad" => $tercero->entidad->terceroEntidad->nombre,
            "siglaEntidad" => $tercero->entidad->terceroEntidad->sigla,
            "imagen" => $this->getImagen($socio),
            "esImagenReal" => $socio->esAvatarReal(),
            "ahorros" => Ahorros::getAhorros($socio),
            "creditos" => Creditos::getCreditos($socio),
            "recaudo" => Recaudos::getRecaudos($socio),
        ];
        return $data;
    }

    private function getPerfil($usuario) {
        $socio = $usuario->socios[0];
        $tercero = $socio->tercero;
        $nombre1 = Str::Title($tercero->primer_nombre);
        $nombre2 = Str::Title($tercero->segundo_nombre);
        $apellido1 = Str::Title($tercero->primer_apellido);
        $apellido2 = Str::Title($tercero->segundo_apellido);
        $contacto = $tercero->getContacto(true);
        $cupoDisponible = $tercero->cupoDisponible();
        $endeudamiento = $socio->endeudamiento();
        $pmep = $this->getPorcentajeMaximoEndeudamientoPermitido($tercero->entidad_id);
        $data = [
            "codigoTipoIdentificacion" => $tercero->tipoIdentificacion->codigo,
            "tipoIdentificacion" => $tercero->tipoIdentificacion->nombre,
            "identificacion" => number_format($tercero->numero_identificacion, 0),
            "primerNombre" => $nombre1,
            "segundoNombre" => $nombre2,
            "primerApellido" => $apellido1,
            "segundoApellido" => $apellido2,
            "nombreCorto" => Str::Title($tercero->nombre_corto),
            "nombreCompleto" => $this->getNombreCompleto(
                $nombre1,
                $nombre2,
                $apellido1,
                $apellido2
            ),
            "empresa" => empty($socio->pagaduria) ? '' : $socio->pagaduria->nombre,
            "fechaNacimiento" => empty($tercero->fecha_nacimiento) ? '1900-01-01' : $socio->tercero->fecha_nacimiento->format("Y-m-d"),
            "edad" => empty($tercero->fecha_nacimiento) ? '' : $socio->tercero->fecha_nacimiento->diffForHumans(),
            "fechaIngresoEmpresa" => $socio->fecha_ingreso->format("Y-m-d"),
            "antiguedadEmpresa" => $socio->fecha_ingreso->diffForHumans(),
            "fechaAfiliacion" => $socio->fecha_afiliacion->format("Y-m-d"),
            "antiguedadFondo" => $socio->fecha_afiliacion->diffForHumans(),
            "email" => $contacto->email,
            "telefono" => $contacto->getTelefono(),
            "cupoDisponible" => number_format($cupoDisponible),
            "signoCupoDisponible" => $cupoDisponible < 0 ? SocioController::NEGATIVO : SocioController::POSITIVO,
            "endeudamiento" => number_format($endeudamiento, 2),
            "signoEndeudamiento" => $endeudamiento <= $pmep ? SocioController::NEGATIVO : SocioController::POSITIVO,
        ];
        return $data;
    }

    private function getBeneficiarios($usuario) {
        $socio = $usuario->socios[0];
        $beneficiarios = Beneficiario::socioid($socio->id)
            ->with([
                "parentesco",
                "tercero"
            ])
            ->orderBy("porcentaje_beneficio", "desc")
            ->get();

        $data = collect();
        foreach ($beneficiarios as $beneficiario) {
            $tercero = $beneficiario->tercero;
            $parentesco = $beneficiario->parentesco;

            $nombre1 = Str::Title($tercero->primer_nombre);
            $nombre2 = Str::Title($tercero->segundo_nombre);
            $apellido1 = Str::Title($tercero->primer_apellido);
            $apellido2 = Str::Title($tercero->segundo_apellido);

            $dato = [
                "codigoTipoIdentificacion" => $tercero->tipoIdentificacion->codigo,
                "identificacion" => number_format($tercero->numero_identificacion, 0),
                "primerNombre" => $nombre1,
                "segundoNombre" => $nombre2,
                "primerApellido" => $apellido1,
                "segundoApellido" => $apellido2,
                "nombreCorto" => Str::Title($tercero->nombre_corto),
                "nombreCompleto" => $this->getNombreCompleto(
                    $nombre1,
                    $nombre2,
                    $apellido1,
                    $apellido2
                ),
                "parentesco" => $parentesco->nombre,
                "porcentajeBeneficio" => number_format($beneficiario->porcentaje_beneficio, 2),
            ];

            $data->push($dato);
        }

        return $data;
    }

    private function getImagen($socio) {
        $path = sprintf("public/asociados/%s", $socio->obtenerAvatar());
        $content = base64_encode(Storage::get($path));
        return $content;
    }

    public function getNombreCompleto($nombre1, $nombre2, $apellido1, $apellido2) {
        $nombres = "%s %s";
        $apellidos = "%s %s";
        $completo = "%s %s";

        $nombres = trim(sprintf($nombres, $nombre1, $nombre2));
        $apellidos = trim(sprintf($apellidos, $apellido1, $apellido2));

        $completo = trim(sprintf($completo, $nombres, $apellidos));
        return $completo;
    }

    private function getPorcentajeMaximoEndeudamientoPermitido($entidadId){
        $pmep = ParametroInstitucional::entidadId($entidadId)
            ->codigo('CR003')
            ->first();
        $pmep = empty($pmep) ? 100 : $pmep->valor;
    }

    /**
     * Establece las rutas
     */
    public static function routes()//use Route;
    {
        Route::get('1.0/socio', 'Api\Socio\SocioController@socio');
        Route::get('1.0/perfil', 'Api\Socio\SocioController@perfil');
        Route::get('1.0/beneficiarios', 'Api\Socio\SocioController@beneficiarios');
    }
}

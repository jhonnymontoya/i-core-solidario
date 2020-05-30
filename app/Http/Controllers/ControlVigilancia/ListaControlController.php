<?php

namespace App\Http\Controllers\ControlVigilancia;

use App\Http\Controllers\Controller;
use App\Http\Requests\ControlVigilancia\ListaControl\UploadListaControlRequest;
use App\Models\ControlVigilancia\DetalleListaControl;
use App\Models\ControlVigilancia\ListaControl;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Route;
use Session;
use SimpleXMLElement;

class ListaControlController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index() {
		$this->log("Ingresó a listas de control");
		$listas = ListaControl::paginate();
		return view('controlVigilancia.listaControl.index')->withListas($listas);
	}

	public function edit(ListaControl $obj) {
		$this->log("Ingresó a actualizar la lista $obj->nombre");
		return view('controlVigilancia.listaControl.edit')->withLista($obj);
	}

	public function update(ListaControl $obj, UploadListaControlRequest $request) {
		$this->log("Actualizó la lista $obj->nombre", "ACTUALIZAR");
		$contenido = $request->file('archivo')->openFile();
		$contenido = $contenido->fread($contenido->getSize());

		$contenido = new SimpleXMLElement($contenido);
		$data = array();
		$fechaPublicacion = null;

		if($obj->tipo == 'OFAC') {
			$fecha = $this->limpiar($contenido->publshInformation->Publish_Date);
			$fechaPublicacion = Carbon::parse($fecha)->startOfDay();
			$entradas = $contenido->sdnEntry;
			foreach($entradas as $entrada) {
				$tipo = $this->limpiar($entrada->sdnType);
				if($tipo != 'Entity' && $tipo != 'Individual') {
					continue;
				}
				$datos = $this->parceOFACEntityIndividual($entrada, $tipo);
				foreach($datos as $dato) {
					$data[] = $dato;
				}
			}
		}

		if($obj->tipo == 'UN') {
			$fecha = $this->limpiar($contenido->attributes()->dateGenerated);
			$fechaPublicacion = Carbon::parse($fecha)->startOfDay();
			foreach($contenido as $tag => $value) {
				foreach ($value as $key => $val) {
					$datos = $this->parceUNEntityIndividual($val);
					foreach($datos as $dato) {
						$data[] = $dato;
					}
				}
			}
		}

		$data = $this->crearDetalles($data);

		try {
			DB::beginTransaction();
			DetalleListaControl::whereListaControlId($obj->id)->delete();

			$obj->fecha_publicacion = $fechaPublicacion;
			$obj->save();

			$obj->detallesListaControl()->saveMany($data);
			Session::flash("message", "Se actualizó la ista '$obj->nombre'");
			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			Session::flash("error", "Error actualizando la lista de control");
			Log::error('Mensaje de error: ' . $e->getMessage());
		}
		return redirect('listaControl');
	}

	////////////////////////////////////////////////////////////////////////////

	private function crearDetalles($data) {
		$detalles = array();
		foreach($data as $dato) {
			$detalles[] = new DetalleListaControl($dato);
		}
		return $detalles;
	}

	private function parceOFACEntityIndividual($xml, $tipo) {
		$datos = array();
		$identificaciones = $this->documentoIdentidadOFAC($xml);
		$data = array();
		$data["tipo"] = $tipo;
		//Para entidad el last name contiene el nombre de la entiidad
		if($tipo == 'Entity') {
			$data["primer_nombre"] = $this->limpiar($xml->lastName);
			$data["primer_apellido"] = null;
		}
		else {
			$data["primer_nombre"] = $this->limpiar($xml->firstName);
			$data["primer_apellido"] = $this->limpiar($xml->lastName);
		}
		$data["segundo_nombre"] = null;
		$data["segundo_apellido"] = null;
		$data["es_colombiano"] = $this->esColombianoOFAC($xml);
		$data["data"] = json_encode($xml);
		$data["tipo_documento"] = null;
		$data["numero_documento"] = null;
		foreach ($identificaciones as $identificacion) {
			$newData = $data;
			$newData["tipo_documento"] = $identificacion["tipo_documento"];
			$newData["numero_documento"] = $identificacion["numero_documento"];
			$datos[] = $newData;
		}
		if(count($datos) == 0) {
			$datos[] = $data;
		}
		return $datos;
	}

	private function parceUNEntityIndividual($xml) {
		$datos = array();
		$identificaciones = $this->documentoIdentidadUN($xml);
		$data = array();
		$data["tipo"] = $xml->getName();
		$data["primer_nombre"] = $this->limpiar($xml->FIRST_NAME);
		$data["segundo_nombre"] = $this->limpiar($xml->SECOND_NAME);
		$data["primer_apellido"] = $this->limpiar($xml->THIRD_NAME);
		$data["segundo_apellido"] = $this->limpiar($xml->FORTH_NAME);
		$data["es_colombiano"] = $this->esColombianoUN($xml);
		$data["data"] = json_encode($xml);
		$data["tipo_documento"] = null;
		$data["numero_documento"] = null;
		foreach ($identificaciones as $identificacion) {
			$newData = $data;
			$newData["tipo_documento"] = $identificacion["tipo_documento"];
			$newData["numero_documento"] = $identificacion["numero_documento"];
			$datos[] = $newData;
		}
		if(count($datos) == 0) {
			$datos[] = $data;
		}
		return $datos;
	}

	private function limpiar($str) {
		$str = (string) $str;
		if(empty($str)) {
			return null;
		}
		$str = str_replace("\n", "", $str);
		$str = str_replace("\"", "", $str);
		return trim($str);
	}

	private function esColombianoUN($xml) {
		$colombiano = false;
		$nacionalidades = $xml->NATIONALITY->VALUE;
		if(is_null($nacionalidades)) {
			return $colombiano;
		}
		foreach($nacionalidades as $key => $value) {
			$val = $this->limpiar($value);
			if($val == 'Colombia') {
				$colombiano = true;
				break;
			}
		}
		return $colombiano;
	}

	private function esColombianoOFAC($xml) {
		$colombiano = false;
		if($xml->idList->count() == 0) {
			return $colombiano;
		}
		$xml = $xml->idList->id;
		foreach($xml as $key => $value) {
			$pais = $this->limpiar($value->idCountry);
			if($pais == 'Colombia') {
				$colombiano = true;
				break;
			}			
		}
		return $colombiano;
	}

	private function documentoIdentidadUN($xml) {
		$documentos = array();
		if($xml->INDIVIDUAL_DOCUMENT->count() == 0) {
			return $documentos;
		}
		foreach($xml as $key => $value) {
			if($key == 'INDIVIDUAL_DOCUMENT') {
				$documento = [
					"tipo_documento" => $this->limpiar($value->TYPE_OF_DOCUMENT),
					"numero_documento" => $this->limpiar($value->NUMBER)
				];
				array_push($documentos, $documento);
			}
		}
		return $documentos;
	}

	private function documentoIdentidadOFAC($xml) {
		$documentos = array();
		if($xml->idList->count() == 0) {
			return $documentos;
		}
		$xml = $xml->idList->id;
		foreach($xml as $key => $value) {
			$documento = [
				"tipo_documento" => $this->limpiar($value->idType),
				"numero_documento" => $this->limpiar($value->idNumber)
			];
			array_push($documentos, $documento);
		}
		return $documentos;
	}

	////////////////////////////////////////////////////////////////////////////

	public static function routes() {
		Route::get('listaControl', 'ControlVigilancia\ListaControlController@index');
		Route::get('listaControl/{obj}', 'ControlVigilancia\ListaControlController@edit')->name("listaControl.edit");
		Route::put('listaControl/{obj}', 'ControlVigilancia\ListaControlController@update');
	}
}

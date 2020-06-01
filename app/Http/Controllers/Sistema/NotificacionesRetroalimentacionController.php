<?php

namespace App\Http\Controllers\Sistema;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sistema\NotificacionRetroalimentacion;

use Aws\Sns\Message;
use AwsSnsMessageValidatorMessage;
use AwsSnsMessageValidatorMessageValidator;
use GuzzleHttpClient;
use Log;

class NotificacionesRetroalimentacionController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:admin')->except(['postSNS']);
        //$this->middleware('verEnt')->except(['postSNS']);
        //$this->middleware('verMenu')->except(['postSNS']);
    }

    public function index(Request $request)
    {
        $this->logActividad(
            "Ingreso a la retroalimentación de notificaciones",
            $request
        );
        $notificaciones = NotificacionRetroalimentacion::all();
        dd("Entro", $notificaciones);
    }

    public function postSNS(Request $request)
    {
        $this->log(
            "Se creó una retroalimentación de notificación desde AWS SNS",
            'CREAR'
        );

        try {
            // Create a message from the post data and validate its signature
            $message = Message::fromRawPostData();
            $validator = new MessageValidator();
            $validator->validate($message);
        }
        catch (Exception $e) {
            Log::error(
                sprintf(
                    "Mensaje proveniente de AWS no validado '%s'",
                    $message
                )
            );
        }

        //$message->get('Type') === 'SubscriptionConfirmation'
        //$message->get('Type') === 'Notification'

        if($message->get('Type') === 'SubscriptionConfirmation') {
            // Send a request to the SubscribeURL to complete subscription
            (new Client)->get($message->get('SubscribeURL'))->send();
        }
        elseif ($message->get('Type') === 'Notification') {
            $obj = NotificacionRetroalimentacion::create(["trama" => $message]);
        }

        //$trama = file_get_contents('php://input');

        return ;
    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::get(
            'notificacionesRetroalimentacion',
            'Sistema\NotificacionesRetroalimentacionController@index'
        );
        Route::post(
            'notificacionesRetroalimentacion/sns',
            'Sistema\NotificacionesRetroalimentacionController@postSNS'
        );
    }
}

<?php

namespace App\Providers;

use DB;
use Exception;
use App\Traits\FonadminTrait;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;


class CustomValidationRulesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('null', function ($attribute, $value, $parameters, $validator){
            return (boolean)DB::table($parameters[0])->whereNull($parameters[1])->where($parameters[2], $parameters[3])->count();
        });

        /**
         * Regla de validación que devuelve verdadero si los valores que se pasan en la
         * regla de validación devuelven uno o más registros en el base de datos
         *
         * Forma de uso:
         *
         * igual:nombreTabla,nombreColumnaACompararConElValor[,nombreColumnaAdicional,valorAdicional].....
         */
        Validator::extend('igual', function ($attribute, $value, $parameters, $validator){
            if(count($parameters) < 2 || count($parameters) % 2 != 0)throw new Exception("Not enough parameters for validation rule 'igual'");
            $builder = DB::table(array_shift($parameters))->where(array_shift($parameters), $value);
            while(count($parameters) > 0)
            {
                $parameter = array_shift($parameters);
                $value = array_shift($parameters);
                if($value == "NULL")
                {
                    $builder->whereNull($parameter);
                }
                else
                {
                    $builder->where($parameter, $value);
                }
            }
            return (boolean)$builder->count();
        });

        /**
         * Regla de validación que devuelve verdadero si los valores que se pasan
         * en la regla de validación devuelven cero registros en el base de datos
         *
         * Forma de uso:
         *
         * noexiste:nombreTabla,nombreColumnaACompararConElValor[,nombreColumnaAdicional,valorAdicional].....
         */
        Validator::extend('noexiste', function ($attribute, $value, $parameters, $validator){
            if(count($parameters) < 2 || count($parameters) % 2 != 0)throw new Exception("Not enough parameters for validation rule 'noexiste'");
            $builder = DB::table(array_shift($parameters))->where(array_shift($parameters), $value);
            while(count($parameters) > 0) {
                $parameter = array_shift($parameters);
                $value = array_shift($parameters);
                if($value == "NULL") {
                    $builder->whereNull($parameter);
                }
                else {
                    $builder->where($parameter, $value);
                }
            }
            return !(boolean)$builder->count();
        });

        /**
         * Regla de validación que devuelve falso si el modulo se encuentra
         * cerrado para la fecha
         *
         * Forma de uso:
         *
         * modulocerrado:moduloId
         */
        Validator::extend('modulocerrado', function ($attribute, $value, $parameters, $validator){
            if(count($parameters) < 1)throw new Exception("Not enough parameters for validation rule 'moduloCerrado'");
            return !$this->moduloCerrado(array_shift($parameters), $value);
        });
    }
}

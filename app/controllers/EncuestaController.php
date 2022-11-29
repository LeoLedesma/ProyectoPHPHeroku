<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController
{

    function CargarEncuesta($request, $response, $args)
        {
            $parametros = $request->getQueryParams();

            if(isset($parametros['id_mesa']) && isset($parametros['pt_mesa']) && isset($parametros['pt_rest']) && isset($parametros['pt_mozo']) && isset($parametros['pt_cocina']))
            {           
                $encuesta = new Encuesta();
                $encuesta->id_mesa = $parametros['id_mesa'];
                $encuesta->pt_mesa = $parametros['pt_mesa'];
                $encuesta->pt_rest = $parametros['pt_rest'];
                $encuesta->pt_mozo = $parametros['pt_mozo'];
                $encuesta->pt_cocina = $parametros['pt_cocina'];

                if($encuesta->alta())
                {
                    $payload = json_encode(array("Ok" => 'Encuesta recibida.'), JSON_PRETTY_PRINT);
                }else
                {
                    $payload = json_encode(array("ERROR" => 'Ocurrio un error'), JSON_PRETTY_PRINT);
                }

            }
            else
            {
                $payload = json_encode(array("ERROR" => 'No se recibieron todos los parametros'), JSON_PRETTY_PRINT);

            }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
        }

    function MejoresEncuestas($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $lista = Encuesta::obtenerMejores($parametros['cantidad']);
        $payload = json_encode(array("Encuestas" => $lista), JSON_PRETTY_PRINT);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
    }

}
<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController implements IApiUsable
{
   
    function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        if ((isset($parametros['cod_identificacion']) && strlen($parametros['cod_identificacion'])==5 &&
            isset($parametros['nombre_cliente'])
            ))
        {
            $nuevaMesa = new Mesa();    
            $nuevaMesa->cod_identificacion = $parametros['cod_identificacion'];              
            $nuevaMesa->nombre_cliente = $parametros['nombre_cliente'];            
            $id = $nuevaMesa->alta();
        
            $payload = json_encode(array("mensaje" => "Mesa creada con exito. Id: ".$id));
        } else {
            $payload = json_encode(array("mensaje" => "Faltan datos"));
        }    
      
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');    
    }

    function ObtenerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("Mesa" => $lista), JSON_PRETTY_PRINT);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
    }

    function Baja($request, $response, $args)
    {

    }
    function Modificacion($request, $response, $args)
    {

    }
    function Borrar($request, $response, $args)
    {

    }

}

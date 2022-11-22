<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController implements IApiUsable
{
   
    function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        if ((isset($parametros['txt_desc']) && 
            isset($parametros['txt_desc']) && 
            isset($parametros['precio']) && 
            isset($parametros['tiempo_est_preparacion'])))
        {
            $nuevoProducto = new Producto();    
            $nuevoProducto->txt_desc = $parametros['txt_desc'];    
            $nuevoProducto->precio = $parametros['precio'];
            $nuevoProducto->cod_producto = $parametros['cod_producto'];
            $nuevoProducto->tiempo_est_preparacion = $parametros['tiempo_est_preparacion'];
            $id = $nuevoProducto->alta();
        
            $payload = json_encode(array("mensaje" => "Producto creado con exito. Id: ".$id));
        } else {
            $payload = json_encode(array("mensaje" => "Faltan datos"));
        }    
      
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');    
    }

    function ObtenerTodos($request, $response, $args)
    {
        $cod_usuario = AutentificadorJWT::ObtenerRol($request);

        $lista = "";
        if($cod_usuario != Usuario::SOCIO)
        {
            $lista = Producto::obtenerTodosByTipoUsuario($cod_usuario);
        }else
        {
            $lista = Producto::obtenerTodos();
        }

        $payload = json_encode(array("Producto" => $lista), JSON_PRETTY_PRINT);

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

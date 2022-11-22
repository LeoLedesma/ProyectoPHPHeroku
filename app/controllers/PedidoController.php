<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController implements IApiUsable
{
   
    function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        if ((isset($parametros['id_producto']) &&
            isset($parametros['id_mesa'])
            ))
        {
            $nuevoPedido = new Pedido();    
            $nuevoPedido->id_producto = $parametros['id_producto'];              
            $nuevoPedido->id_mesa = $parametros['id_mesa'];            
            $id = $nuevoPedido->alta();
        
            $payload = json_encode(array("mensaje" => "Pedido creado con exito. Id: ".$id));
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
            $lista = Pedido::obtenerTodosPendientesByTipoUsuario($cod_usuario);
        }else
        {
            $lista = Pedido::obtenerTodos();
        }

        
        $payload = json_encode(array("Pedido" => $lista), JSON_PRETTY_PRINT);

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

    function ActualizarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();       
        $idUsuario = AutentificadorJWT::ObtenerUsuario($request);        

        if(Pedido::actualizarPedido($parametros['id_pedido'],$idUsuario,$parametros['cod_estado_pedido'])){

            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }       

        $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');
    }

}

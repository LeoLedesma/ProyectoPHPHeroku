<?php 

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once "./models/AutentificadorJWT.php";

class pedidosMiddleware{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $payload = "";
        $response = new Response();
        
        try{
            $parametros = $request->getParsedBody();
            if(isset($parametros["id_pedido"]) && isset($parametros["cod_estado_pedido"]))
            {
                $cod_usuario = AutentificadorJWT::ObtenerRol($request);            
                $cod_prod = Pedido::obtenerCodTipoProducto($parametros["id_pedido"]);            
    
                if($cod_prod!=false)
                {
                    if(Producto::usuarioPuedeModificar($cod_prod,$cod_usuario)){
                        $response = $handler->handle($request);
                    }
                    else{
                        $payload = json_encode(array("ERROR" => "No estas autorizado"));
                        $response->getBody()->write($payload);
                    }
                }else
                {
                    $payload = json_encode(array("ERROR" => "Pedido no existente"));
                    $response->getBody()->write($payload);
                }

            }else{
                $payload = json_encode(array("ERROR" => "Faltan parametros"));
                $response->getBody()->write($payload);
            }

        }catch (\Throwable $e) {

            $payload = json_encode(array('error' => $e->getMessage()));
            $response->getBody()->write($payload);
        }        

        return $response->withHeader('Content-Type', 'application/json');
    }
}
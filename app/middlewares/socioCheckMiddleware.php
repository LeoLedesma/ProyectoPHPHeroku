<?php 

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once "./models/AutentificadorJWT.php";

class socioCheckMiddleware{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $payload = "";
        $response = new Response();
        
        try{
            $claim = AutentificadorJWT::ObtenerRol($request);

            if($claim == Usuario::SOCIO){
                $response = $handler->handle($request);
            }
            else{
                $payload = json_encode(array("ERROR" => "No estas autorizado"));
                $response->getBody()->write($payload);
            }
        }catch (\Throwable $e) {

            $payload = json_encode(array('error' => $e->getMessage()));
            $response->getBody()->write($payload);
        }        

        return $response->withHeader('Content-Type', 'application/json');
    }
}
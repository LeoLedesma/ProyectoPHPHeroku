<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class loginMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        
        $reponse = new Response();

        $parametros = $request->getParsedBody();

        if(isset($parametros["contrasenia"]) && isset($parametros["usuario"]))
        {
            if($parametros["contrasenia"] != "" && $parametros["usuario"] != ""){

                $reponse = $handler->handle($request);
            }else{
                $reponse->getBody()->write("Error hay campos vacios");
            }
        }else{
            $reponse->getBody()->write("Faltan completar campos");
        }

        return $reponse;
    }
}
<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController implements IApiUsable
{
   
    function Alta($request, $response, $args)
    {
        echo "entramos";

         $parametros = $request->getParsedBody();
        
        if ((isset($parametros['usuario']) && 
            isset($parametros['contrasenia']) && 
            isset($parametros['cod_tipo_usuario'])))
        {
            $nuevoUsuario = new Usuario();    
            $nuevoUsuario->usuario = $parametros['usuario'];              
            $nuevoUsuario->contrasenia = $parametros['contrasenia'];
            $nuevoUsuario->cod_usuario = $parametros['cod_tipo_usuario'];
            $id = $nuevoUsuario->alta();
        
            $payload = json_encode(array("mensaje" => "Usuario creado con exito. Id: ".$id));
        } else {
            $payload = json_encode(array("mensaje" => "Faltan datos"));
        }    
      
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');
    
    }

    function ObtenerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("Usuario" => $lista), JSON_PRETTY_PRINT);

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

    public function Login($request, $response)
  {    
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $contrasenia = $parametros['contrasenia'];    

    if(isset($usuario) && isset($contrasenia))
    {
      $claims = Usuario::verificarDatos($usuario,$contrasenia);            
      if ($claims != 0) {
        $token = AutentificadorJWT::CrearToken($claims);
        $payload = json_encode(array('OK' => $token));
  
        $response->getBody()->write($payload);
        return $response->withStatus(200)
          ->withHeader(
            'Content-Type',
            'application/json'
          );  
  
      }else{      
        $response->getBody()->write("El usuario no existe");
      }

    }else
    {
      $response->getBody()->write("Datos Invalidos");
    }


    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }

}

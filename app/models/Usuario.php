<?php

class Usuario
{
    public $id_usuario;
    public $usuario;
    public $contrasenia;
    public $cod_usuario;
    public $txt_tipo_usuario;
    public $fec_registro;
    public $fec_suspension;
    public $fec_expulsion;


    const SOCIO = 1;
    const BARTENDER = 2;
    const CERVEZERO = 3;
    const COCINERO = 4;
    const MOZO = 5;

    

  public static function verificarDatos($usuario, $contrasenia)
  {           
      $usuarioRecibido = self::obtenerUsuario($usuario);
        
      if($usuarioRecibido != null && (password_verify($contrasenia,$usuarioRecibido->contrasenia) ||  $usuarioRecibido->contrasenia == $contrasenia)){                        
             return array("id_rol" => $usuarioRecibido->cod_usuario,
                          "id_usuario"=>$usuarioRecibido->id_usuario);            
      }    
      return 0;
  }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, usuario, contrasenia, us.cod_usuario, txt_desc FROM musuarios us inner join ttipo_usuario  ttu on ttu.cod_usuario = us.cod_usuario where usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
       

    function alta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        $consulta = $objAccesoDatos->prepararConsulta(' insert into musuarios (usuario, contrasenia, cod_usuario, fec_registro) VALUES (:usuario,:contrasenia, :cod_tipo_usuario, :fec_registro)');
        $claveHash = password_hash($this->contrasenia, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':contrasenia', $claveHash, PDO::PARAM_STR);
        $consulta->bindvalue(':cod_tipo_usuario',$this->cod_usuario, PDO::PARAM_INT);
        $consulta->bindValue('fec_registro', date("Y-m-d H:i:s") ,PDO::PARAM_STR);
        
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, usuario,contrasenia,us.cod_usuario as cod_usuario, tu.txt_desc as txt_tipo_usuario,fec_registro, fec_suspension, fec_expulsion  FROM musuarios us inner join ttipo_usuario tu on tu.cod_usuario = us.cod_usuario");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
    
}

?>
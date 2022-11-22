<?php

use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = '111Ledesma';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (100000), //1 dia
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "TPP3"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }

        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );

            if ($decodificado->aud !== self::Aud()) {
                throw new Exception("No es el usuario valido");
            }

            return $decodificado;
        } catch (Exception $e) {
            throw $e;
        }
       
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
    
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    public static function ObtenerRol($request)
    {
        $token = self::ObtenerToken($request);
        $data = self::ObtenerData($token);
        return $data->id_rol;
    }

    public static function ObtenerUsuario($request)
    {
        $token = self::ObtenerToken($request);
        $data = self::ObtenerData($token);
        return $data->id_usuario;
    }

    public static function ObtenerToken($request)
    {
        $header = $request->getHeaderLine("Authorization");
        if($header != null){
            return trim(explode("Bearer", $header)[1]);
        }
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
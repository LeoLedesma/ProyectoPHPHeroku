<?php

    class Mesa{

        public $id_mesa;
        public $cod_identificacion;
        public $nombre_cliente;
        public $cod_estado_mesa;
        public $txt_estado_mesa;

        const NUEVA = 5;
    

    function alta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mmesas (cod_identificacion,nombre_cliente,cod_estado_mesa) VALUES (:cod_identificacion, :nombre_cliente,:cod_estado_mesa)");
        $consulta->bindValue(':cod_identificacion', $this->cod_identificacion, PDO::PARAM_STR);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':cod_estado_mesa', Mesa::NUEVA , PDO::PARAM_INT);               
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, cod_identificacion,nombre_cliente,mm.cod_estado_mesa, tm.txt_desc as txt_estado_mesa from mmesas mm inner join ttipo_estado_mesa tm on tm.cod_estado_mesa = mm.cod_estado_mesa");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
    }

?>

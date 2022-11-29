<?php

    class Mesa{

        public $id_mesa;
        public $cod_identificacion;
        public $nombre_cliente;
        public $cod_estado_mesa;
        public $txt_estado_mesa;

        const ESPERANDO = 1;
        const COMIENDO = 2;
        const PAGANDO = 3 ;
        const CERRADA = 4;
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

    static function buscarDemoraMesa($id_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("select max(tiempo_est_preparacion) as tiempo_est_preparacion from mpedidos where id_mesa = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch()['tiempo_est_preparacion'];
    }
    static function buscarDemoraPedido($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("select tiempo_est_preparacion from mpedidos where id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch()['tiempo_est_preparacion'];
    }

    static function actualizarMesa($id_mesa, $cod_estado_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mmesas set cod_estado_mesa = :cod_estado_mesa where id_mesa = :id_mesa");        
        $consulta->bindValue(':id_mesa',$id_mesa , PDO::PARAM_INT); 
        $consulta->bindValue(':cod_estado_mesa',$cod_estado_mesa , PDO::PARAM_INT);               
        $consulta->execute();
    }

    static function pedirCuenta($id_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("select sum(precio) total from mpedidos mpd inner join mproductos mpt on mpd.id_producto = mpt.id_producto where id_mesa = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->execute();
        $total = $consulta->fetch()['total'];

        $objAccesoDatos2 = AccesoDatos::obtenerInstancia();
        $consulta2 = $objAccesoDatos2->prepararConsulta("update mmesas set cod_estado_mesa = 3, total = :total where id_mesa = :id_mesa");
        $consulta2->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta2->bindValue(':total', $total, PDO::PARAM_STR);
        $consulta2->execute();

        return $total;
    }

    static function MejorMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("select id_mesa, cod_identificacion,nombre_cliente,mm.cod_estado_mesa, tm.txt_desc as txt_estado_mesa, total from mmesas mm inner join ttipo_estado_mesa tm on tm.cod_estado_mesa = mm.cod_estado_mesa order by total desc limit 1");        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    

    }

?>

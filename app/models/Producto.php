<?php


class Producto{
    public $id_producto;
    public $txt_desc;
    public $cod_producto;
    public $txt_tipo_producto;
    public $precio;
    public $tiempo_est_preparacion;
    public $visible;

    const VINO = 1;
    const TRAGO = 2;
    const CERVEZA = 3;
    const COMIDA = 4;
    const POSTRE = 5;   


    function alta()
    {        
        $id = '';
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('insert into mproductos (txt_desc,precio,cod_producto, tiempo_est_preparacion, visible) VALUES (:txt_desc,:precio,:cod_producto,:tiempo_est_preparacion,:visible)');
        $consulta->bindValue(':txt_desc', $this->txt_desc, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':cod_producto', $this->cod_producto, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo_est_preparacion',date("H:i:s", strtotime($this->tiempo_est_preparacion)), PDO::PARAM_STR);
        $consulta->bindvalue(':visible',1, PDO::PARAM_INT);        
        $consulta->execute();
        
        return $objAccesoDatos->obtenerUltimoId();
    }

    static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto,precio,mp.txt_desc,mp.tiempo_est_preparacion, mp.cod_producto as cod_producto, tp.txt_desc as txt_tipo_producto, visible FROM mproductos mp inner join ttipo_producto tp on tp.cod_producto = mp.cod_producto");
        $consulta->execute();        

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    static function obtenerTodosByTipoUsuario($cod_usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto,mp.txt_desc,mp.tiempo_est_preparacion, mp.cod_producto as cod_producto, tp.txt_desc as txt_tipo_producto, visible FROM mproductos mp inner join ttipo_producto tp on tp.cod_producto = mp.cod_producto where tp.visible_cod_usuario = :cod_usuario");
        $consulta->bindValue(':cod_usuario', $cod_usuario, PDO::PARAM_INT);        
        $consulta->execute();
                
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    static function getTiempoPreparacion($id_producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempo_est_preparacion FROM mproductos where id_producto = :id_producto");
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch()['tiempo_est_preparacion'];
    }

    static function usuarioPuedeModificar($cod_producto,$cod_usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT visible_cod_usuario from ttipo_producto');        
        $consulta->execute();
    
        return $cod_usuario == $consulta->fetch()['visible_cod_usuario'];
    }  

    function suma_horas($hora1,$hora2){
 
    $hora1=explode(":",$hora1);
    $hora2=explode(":",$hora2);
    $temp=0;
 
    //sumo segundos 
    $segundos=(int)$hora1[2]+(int)$hora2[2];
    while($segundos>=60){
        $segundos=$segundos-60;
        $temp++;
    }
 
    //sumo minutos 
    $minutos=(int)$hora1[1]+(int)$hora2[1]+$temp;
    $temp=0;
    while($minutos>=60){
        $minutos=$minutos-60;
        $temp++;
    }
 
    //sumo horas 
    $horas=(int)$hora1[0]+(int)$hora2[0]+$temp;
 
    if($horas<10)
        $horas= '0'.$horas;
 
    if($minutos<10)
        $minutos= '0'.$minutos;
 
    if($segundos<10)
        $segundos= '0'.$segundos;
 
    $sum_hrs = $horas.':'.$minutos.':'.$segundos;
 
    return ($sum_hrs);
 
    }

    
}



?>
<?php

class Pedido{

    public $id_pedido;
    public $id_mesa;
    public $id_producto;
    public $txt_producto;
    public $fec_alta;
    public $cod_estado_pedido;
    public $txt_estado_pedido;
    public $tiempo_est_preparacion;

    const NUEVO = 1;


function alta()
{
    $id = '';
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mpedidos(id_producto, id_mesa, fec_alta, cod_estado_pedido, tiempo_est_preparacion) VALUES (:id_producto, :id_mesa, :fec_alta, :cod_estado_pedido, :tiempo_est_preparacion)");
    $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
    $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
    $consulta->bindValue(':fec_alta', date("Y-m-d H:i:s"), PDO::PARAM_STR);
    $consulta->bindvalue(':cod_estado_pedido',Pedido::NUEVO, PDO::PARAM_INT); 
    $consulta->bindvalue(':tiempo_est_preparacion',Pedido::calcularTiempoElaboracion($this->id_producto), PDO::PARAM_STR); 
    $consulta->execute();
    $id = $objAccesoDatos->obtenerUltimoId();
    
    Pedido::actualizarPedido($id, 1, Pedido::NUEVO);
    
    
    return $id;
}

static function actualizarPedido($id_pedido, $id_usuario, $cod_estado_nuevo)
{
    $ultimoEstado = Pedido::obtenerUltimoEstado($id_pedido);
        
    if($ultimoEstado == false)
    {
        $ultimoEstado = Pedido::NUEVO;
    }else
    {
        $ultimoEstado = $ultimoEstado['cod_estado_nuevo'];
    }    

    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro_historico_pedidos(id_pedido, id_usuario, cod_estado_pedido, cod_estado_nuevo, fec_modificacion) VALUES (:id_pedido,:id_usuario,:cod_estado_pedido,:cod_estado_nuevo,:fec_modificacion)");
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $consulta->bindValue(':cod_estado_pedido', $ultimoEstado, PDO::PARAM_INT); 
    $consulta->bindValue(':cod_estado_nuevo', $cod_estado_nuevo, PDO::PARAM_INT); 
    $consulta->bindValue(':fec_modificacion', date("Y-m-d H:i:s"), PDO::PARAM_STR);    
    return $consulta->execute();    
}

static function obtenerTodos()
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido,id_mesa,mp.id_producto,mpt.txt_desc as txt_producto, fec_alta, mp.cod_estado_pedido, tp.txt_desc as txt_estado_pedido, mp.tiempo_est_preparacion from mpedidos mp inner join ttipo_estado_pedido tp on tp.cod_estado_pedido = mp.cod_estado_pedido inner join mproductos mpt on mpt.id_producto = mp.id_producto");
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
}

static function obtenerTodosPendientesByTipoUsuario($cod_usuario)
{
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido,id_mesa,mp.id_producto,mpt.txt_desc as txt_producto, fec_alta, mp.cod_estado_pedido, tp.txt_desc as txt_estado_pedido, mp.tiempo_est_preparacion         from mpedidos mp inner join ttipo_estado_pedido tp on tp.cod_estado_pedido = mp.cod_estado_pedido         inner join mproductos mpt on mpt.id_producto = mp.id_producto        inner join ttipo_producto ttp on mpt.cod_producto = ttp.cod_producto  where ttp.visible_cod_usuario = :cod_usuario");
        $consulta->bindValue(':cod_usuario', $cod_usuario, PDO::PARAM_INT);
        $consulta->execute();        

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
}


static function obtenerUno($id_pedido)
{    
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido,id_mesa,mp.id_producto,mpt.txt_desc as txt_producto, fec_alta, mp.cod_estado_pedido, tp.txt_desc as txt_estado_pedido, mp.tiempo_est_preparacion from mpedidos mp inner join ttipo_estado_pedido tp on tp.cod_estado_pedido = mp.cod_estado_pedido inner join mproductos mpt on mpt.id_producto = mp.id_producto where mp.id_pedido = :id_pedido");
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetch(PDO::FETCH_CLASS, 'Pedido');
}

static function obtenerUltimoEstado($id_pedido)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta('SELECT cod_estado_nuevo from registro_historico_pedidos where id_pedido = :id_pedido');
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetch();
}

static function obtenerCodTipoProducto($id_pedido)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta('SELECT mp.cod_producto from mpedidos md inner join mproductos mp on md.id_producto = mp.id_producto where id_pedido = :id_pedido');
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetch();
}

    static function calcularTiempoElaboracion($id_producto)
    {        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('select sec_to_time(sum(time_to_sec(mp.tiempo_est_preparacion)))as tiempo_est_preparacion from mpedidos mp where id_producto in (select id_producto from mproductos mp WHERE mp.cod_producto in (select ttp.cod_producto from ttipo_producto ttp where ttp.visible_cod_usuario in (select ttp.visible_cod_usuario from ttipo_producto ttp inner join mproductos mp on ttp.cod_producto = mp.cod_producto where id_producto = :id_producto)))');
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->execute();

        $a = $consulta->fetch()['tiempo_est_preparacion'];
        $b = Producto::getTiempoPreparacion($id_producto);

        
        $tiempo = self::suma_horas($a,$b);
        
        return $tiempo;
    }


    static function suma_horas($hora1,$hora2){
 
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
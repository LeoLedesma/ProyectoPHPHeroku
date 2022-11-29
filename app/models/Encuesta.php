<?php

class Encuesta{
    public $id_encuesta;
    public $id_mesa;
    public $pt_mesa;
    public $pt_rest;
    public $pt_mozo;
    public $pt_cocina;


function alta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mencuestas( id_mesa, pt_mesa, pt_rest, pt_mozo, pt_cocina) VALUES (:id_mesa, :pt_mesa, :pt_rest, :pt_mozo, :pt_cocina)");
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':pt_mesa', $this->pt_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':pt_rest', $this->pt_rest, PDO::PARAM_INT);
        $consulta->bindValue(':pt_mozo', $this->pt_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':pt_cocina', $this->pt_cocina, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    static function obtenerMejores($cantidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_encuesta, id_mesa, pt_mesa, pt_rest,pt_mozo,pt_cocina FROM mencuestas order by pt_cocina,pt_mozo,pt_rest,pt_mesa asc LIMIT :cantidad");        
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }
}

?>
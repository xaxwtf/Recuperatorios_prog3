<?php
namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class Cripto{
    public $id;
    public $nombre;
    public $foto;
    public $nacionalidad;
    public $precio;
    public function __construct($nombre,$foto, $nacionalidad,$precio){
        $this->id=0;
        $this->nombre=$nombre;
        $this->nacionalidad=$nacionalidad;
        $this->precio=$precio;
        $this->foto=$foto;
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO criptos (nombre, foto, nacionalidad, precio) VALUES (:nombre,  :foto, :nacionalida, :precio)");
        
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':foto',$this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad',$this->timePreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':precio',$this->precio, PDO::PARAM_STR);
        $consulta->execute();   
    }
    public static function TraerUno($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, foto, nacionalidad, precio  FROM criptos  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_CLASS, );
    }

    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, foto, nacionalidad, precio  FROM criptos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }

}
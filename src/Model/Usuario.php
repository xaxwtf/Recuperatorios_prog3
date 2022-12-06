<?php

namespace App\Model;
use App\Model;
use App\Db\AccesoDatos;
use PDO;
class Usuario{

    public $id;
    public $mail;
    public $tipo;
    public $clave;
    

    public function __construct($id, $mail, $tipo, $clave){
        $this->id=$id;
        $this->mail=$mail;
        $this->tipo= $tipo;
        $this->clave= password_hash($clave, PASSWORD_DEFAULT);
    }
    public function CreateInDB(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (mail, tipo,  clave) VALUES (:mail, :tipo,  :pass )");
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo',$this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':pass',$this->clave, PDO::PARAM_STR);
        
        $consulta->execute();
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave  FROM usuarios");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }
    public static function TraerUno($id){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo, clave  FROM usuarios  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS,static::class);
        return $consulta->fetch();

    }
   
    public static function TraerUnoxUserAndPass($mail,$pass){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mail, tipo,  clave  FROM usuarios  where mail= :mail");
        $consulta->bindValue(':mail',$mail, PDO::PARAM_STR);
        $consulta->execute();

        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $rec= $consulta->fetch();
        $r=null;
        if( password_verify( $pass, $rec['clave'])){
            $r= $rec;
        }
        return $r;
    }
    
}


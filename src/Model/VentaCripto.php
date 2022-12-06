<?php

namespace App\Model;
use App\Db\AccesoDatos;
use PDO;

class VentaCripto{

    public $id;
    public $mesaId;
    public $cliente;
    public $detalle_pendiente;
    public $detalle_listo;
    public $estado;
    public $codigoAlfa;
    public $tiempoPreparacion;
    public $fecha_emision;
    public $fecha_finalizacion;
    public $imagen;

    public function __construct(){
        
        
    }
    public function CalcularTotal(){
        $total=0;
        for($i=0;$i<count($this->detalle_listo);$i++){
            $total= $total + $this->detalle_listo[$i]->precio;
        }
        return $total;
    }
    public function CalcularTiempoEsperado(){ ///solo lo usare al subir el detalle
        $total=0;
        if($this->detalle_pendiente!=null){
            for($i=0;$i<count($this->detalle_pendiente);$i++){
                $total= $total + $this->detalle_pendiente[$i]->tiempo_prom_preparacion;
            }
            $this->tiempoPreparacion=$total;
        }
        
        
    }
    private function CrearListaDetalleInDB(){
        for($i=0;$i<count($this->detalle_pendiente);$i++){
            Detalle::AgregarProducto($this->id, $this->detalle_pendiente[$i]->id);
        }
        
    }

    public function CreateInDB(){
        try{

            $this->CalcularTiempoEsperado();
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (mesaid, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion) VALUES ( :mesaId, :cliente, :estado, :codigoAlfa, :tiempoPreparacion, :emision, :final)");
            $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
            $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':codigoAlfa', $this->codigoAlfa, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoPreparacion', $this->tiempoPreparacion, PDO::PARAM_INT);
            $consulta->bindValue(':emision', $this->fecha_emision, PDO::PARAM_STR);
            $consulta->bindValue(':final', $this->fecha_finalizacion, PDO::PARAM_STR);
            $consulta->execute();
            $this->id=Pedido::TraerUltimoId();
            $this->CrearListaDetalleInDB();
        }
        catch(e){
            echo "ERRRO!";
        }
    }
    public static function TraerTodos(){
        $todos=array();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaId, estado, codigoAlfa, tiempoPreparacion FROM pedidos");
        $consulta->execute();
        $recuperados=$consulta->fetchAll(PDO::FETCH_CLASS);
        $aux;
        for($i=0; $i<count($recuperados); $i++)
        {
            $aux= new Pedido();
            $aux->id=$recuperados[$i]->id;
            $aux->detalle=Detalle::RecuperarDetalle($recuperados[$i]->id);
            $aux->estado=$recuperados[$i]->estado;
            $aux->codigoAlfa=$recuperados[$i]->codigoAlfa;
            $aux->mesaId=$recuperados[$i]->mesaId;
            $aux->tiempoPreparacion=$recuperados[$i]->tiempoPreparacion;
            $todos[count($todos)]=$aux;
        }

        return $todos;
    }
    public static function TraerUno($id){
        $retorno=new Pedido();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaId, estado, codigoAlfa, tiempoPreparacion FROM pedidos  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        $recuperado=array();
        $recuperado=$consulta->fetch(PDO::FETCH_ASSOC);
        $retorno->id=$recuperado["id"];
        $retorno->detalle_pendiente=Detalle::RecuperarDetallePendiente($id);
        $retorno->detalle_listo=Detalle::RecuperarDetalleListo($id);
        $retorno->estado=$recuperado["estado"];
        $retorno->codigoAlfa=$recuperado["codigoAlfa"];
        $retorno->mesaId=$recuperado["mesaId"];
        $retorno->tiempoPreparacion=$recuperado["tiempoPreparacion"];
        $retorno->TiempoEsperado();
        return $retorno;
    }
    public static function TraerUnoXCodAlfa($cod){
        $retorno=new Pedido();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaId, estado, codigoAlfa, tiempoPreparacion FROM pedidos  where codigoAlfa= :cod");
        $consulta->bindValue(':cod',$cod, PDO::PARAM_STR);
        $consulta->execute();
        $recuperado=array();
        $recuperado=$consulta->fetch(PDO::FETCH_ASSOC);
        $retorno->id=$recuperado["id"];
        $retorno->detalle_pendiente=Detalle::RecuperarDetallePendiente($id);
        $retorno->detalle_listo=Detalle::RecuperarDetalleListo($id);
        $retorno->estado=$recuperado["estado"];
        $retorno->codigoAlfa=$recuperado["codigoAlfa"];
        $retorno->mesaId=$recuperado["mesaId"];
        $retorno->tiempoPreparacion=$recuperado["tiempoPreparacion"];

        return $retorno;
    }
    public static function TraerUltimoId(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM pedidos WHERE id = (SELECT MAX(id) FROM pedidos)");
        $consulta->execute();
        $aux=$consulta->fetch();
        return $aux['id'];
    }




    function generaCodigo5 () {
        $caracteres = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $codigo = '';
    
        for ($i = 1; $i <= 5; $i++) {
            $codigo = $codigo . $caracteres[$this->numeroAleatorio(0, 35)];
        }
    
        $this->codigoAlfa=$codigo;
    }
    
    function numeroAleatorio ($ninicial, $nfinal) {
        $numero = rand($ninicial, $nfinal);
        return $numero;
    }
    public function CargarDetalleConJson($json){
        $this->detalle_pendiente= json_decode($json);
    }

    public static function EntregarPedido($codAlf){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.pedidos SET estado = 'entregado' WHERE (codigoAlfa = :cod)");
        $consulta->bindValue(':cod',$codAlf, PDO::PARAM_STR);
        $consulta->execute();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaId, cliente, estado, codigoAlfa, tiempoPreparacion FROM pedidos  where codigoAlfa = :id");
        $consulta->bindValue(':id',$codAlf, PDO::PARAM_STR);
        $consulta->execute();
        
        $aux=$consulta->setFetchMode(PDO::FETCH_CLASS,static::class);
        $r=$consulta->fetch();
        Mesa::CambiarEstadoMesa($r->mesaId,"Cliente Comiendo");
        $r->detalle_pendiente=Detalle::RecuperarDetallePendiente($r->id);
        $r->detalle_listo=Detalle::RecuperarDetalleListo($r->id);
        return $r;
    }

    public static function CobrarPedido($codAlf){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.pedidos SET estado = 'Finalizado' WHERE (codigoAlfa = :cod)");
        $consulta->bindValue(':cod',$codAlf, PDO::PARAM_STR);
        $consulta->execute();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaId, cliente, estado, codigoAlfa, tiempoPreparacion FROM pedidos  where codigoAlfa = :id");
        $consulta->bindValue(':id',$codAlf, PDO::PARAM_STR);
        $consulta->execute();
        
        $aux=$consulta->setFetchMode(PDO::FETCH_CLASS,static::class);
        $r=$consulta->fetch();
        Mesa::CambiarEstadoMesa($r->mesaId,"Libre");
        $r->detalle_pendiente=Detalle::RecuperarDetallePendiente($r->id);
        $r->detalle_listo=Detalle::RecuperarDetalleListo($r->id);
        return $r;
    }
    public static function DemorarPedido($codAlf,$minuntos){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.pedidos SET tiempoPreparacion = :tiempoPreparacion WHERE (codigoAlfa = :cod)");
        $consulta->bindValue(':cod',$codAlf, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion',$minuntos, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaId, cliente, estado, codigoAlfa, tiempoPreparacion FROM pedidos  where codigoAlfa = :id");
        $consulta->bindValue(':id',$codAlf, PDO::PARAM_STR);
        $consulta->execute();
        
        $aux=$consulta->setFetchMode(PDO::FETCH_CLASS,static::class);
        $r=$consulta->fetch();
        
        $r->detalle_pendiente=Detalle::RecuperarDetallePendiente($r->id);
        $r->detalle_listo=Detalle::RecuperarDetalleListo($r->id);
        return $r;

    }
}
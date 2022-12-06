<?php
namespace App\Controller;
use App\Model\Pedido;
use App\Model\Mesa;

class PedidoController 
{
    public  function CrearUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $detalle = $parametros['detalle'];
        $mesa=$parametros['mesaId'];
        $cliente=$parametros['cliente'];
        
        // Creamos el Producto
        $pedido = new Pedido();
        $pedido->CargarDetalleConJson($detalle);//recibimos en modo json
        $pedido->mesaId=$mesa;
        $pedido->cliente=$cliente;
        $pedido->estado="en Preparacion";
        $pedido->fecha_emision=date("Y-m-d H:i:s");
        $pedido->generaCodigo5();
        Mesa::CambiarEstadoMesa($pedido->mesaId,"Cliente Esperando Pedido");
        $pedido->CreateInDB();
        //subiendo la imagen al serv
        if(isset($_FILES["imagen"])){
            $newNameFile=date("Y-m-d H:i:s") .".". pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
            $destino = "./Imagenes/" . $newNameFile;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino);
        }
        

        $payload = json_encode(array("PedidoCreado" => $pedido));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Pedido::TraerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args){
        $usr = $args['id'];
        $usuario = Pedido::TraerUno($usr);

        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function TestUltimoId($request, $response, $args){
        $lista=Pedido::TraerUltimoId();
        $payload = json_encode(array("listaPedidos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function EntregarPedido($request, $response, $args){
        $parametros = $request->getParsedBody();
        $p = $parametros['codAlf'];
        $recuperado=Pedido::EntregarPedido($p);

        $payload = json_encode(array("Pedido Entregado" => $recuperado, "Total"=>$recuperado->CalcularTotal()));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function CobrarPedido($request, $response, $args){
        $parametros = $request->getParsedBody();
        $p = $parametros['codAlf'];
        $recuperado=Pedido::CobrarPedido($p);

        $payload = json_encode(array("Pedido Finalizado" => $recuperado, "Total Facturado"=>$recuperado->CalcularTotal()));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function InformarDemora($request, $response, $args){
        $parametros = $request->getParsedBody();
        $p = $parametros['codAlf'];
        $d= $parametros['time'];
        $recuperado=Pedido::DemorarPedido($p,$d);

        $payload = json_encode(array("Pedido DEMORADO" => $recuperado, "Total"=>$recuperado->CalcularTotal()));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}

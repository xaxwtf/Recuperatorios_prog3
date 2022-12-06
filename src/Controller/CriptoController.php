<?php
namespace App\Controller;
use App\Model\Cripto;

class CriptoController 
{
    public  function CrearUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $nacionalidad=$parametros['nacionalidad'];
        $precio=$parametros['precio'];

        $usr;
        if(isset($_FILES["foto"])){
            $newNameFile=date("Y-m-d H:i:s") .".". pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
            $destino = "./Imagenes/" . $newNameFile;
            move_uploaded_file($_FILES["foto"]["tmp_name"], $destino);
            $usr = new Cripto($nombre,$newNameFile,$nacionalidad,$precio);
        }
        else{
            $usr = new Cripto($nombre,null,$nacionalidad,$precio);
        }
        // Creamos el Producto
        
        $usr->CreateInDB();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public  function TraerUno($request, $response, $args)
    {
        $lista=Producto::TraerTodos();
        $payload = json_encode(array("listaProductos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }

    public  function TraerTodos($request, $response, $args)
    {
        $lista=Cripto::TraerTodos();
        $payload = json_encode(array("listaProductos" => $lista));///recupera
        $response->getBody()->write($payload);//escribe
        return $response->withHeader('Content-Type', 'application/json');
    }
}

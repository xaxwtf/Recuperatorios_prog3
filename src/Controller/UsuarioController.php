<?php
namespace App\Controller;

use App\Model\Usuario;
use App\Model\AutentificadorJWT;
use App\Model\Detalle;

class UsuarioController 
{
    public function cargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mail = $parametros['mail'];
        $tipo=$parametros['tipo'];
        $clave=$parametros['clave'];

        // Creamos el usuario
        $usr = new Usuario(1, $mail, $tipo, $clave);
        $usr->CreateInDB();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito","usuario"=>$usr));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['id'];
        $usuario = Usuario::TraerUno($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista=Usuario::TraerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function LogearUsuario($request, $response, $args){
      $parametros = $request->getParsedBody();
      $usr = $parametros['mail'];
      $pass=$parametros['clave'];
      $r=Usuario::TraerUnoxUserAndPass($usr,$pass);
      if ($r!=null){
        echo "\n Exito \n";
        $token=AutentificadorJWT::CrearToken($r);
        $payload = json_encode(array("jwt"=>$token));
      } else {
        echo "\n No existe! \n";
        $payload = json_encode(array("mensaje" => "ERROR! usuario no encontrado"));
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function Perfil($request, $response, $args){

      $header = $request->getHeaderLine('Authorization');
      $payload="no existe el usuario";
      if(!empty($header)){
        $info=AutentificadorJWT::ObtenerData($header);      
        $payload = json_encode(array("Usuario" =>$info));
      }
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    
}
    

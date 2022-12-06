<?php

// Error Handling
require ( __DIR__  . "/../vendor/autoload.php");

error_reporting(-1);
ini_set('display_errors', 1);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;


include_once "../src/Midlewares/EstaLogeado.php";
include_once "../src/Midlewares/EsAdmin.php";


// Instantiate App
$app = AppFactory::create();

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("ESTE SERIA MI TP INTEGRADOR!!!");
    return $response;
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\UsuarioController::class . ':TraerTodos'  );
  $group->get('/{usuario}', App\Controller\UsuarioController::class . ':TraerUno');
  $group->post('/perfil', App\Controller\UsuarioController::class . ':Perfil')->add(new EstaLogeado);///solo si hay alguien logeado
  $group->post('[/alta]' , App\Controller\UsuarioController::class . ':cargarUno');
  $group->post('/login', App\Controller\UsuarioController::class . ':LogearUsuario'); //todos
});


$app->group('/criptos', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\CriptoController::class . ':TraerTodos' );
  $group->post('[/alta]', App\Controller\CriptoController::class . ':CrearUno')->add(new EsAdmin)->add(new EstaLogeado); 
  $group->get('/{id}', App\Controller\CriptoController::class . ':TraerUno' );
});
/*
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', App\Controller\PedidoController::class . ':TraerTodos'  )->add(new EsAdmin_Socio);//socios, admin 
  $group->post('[/add]', App\Controller\PedidoController::class . ':CrearUno')->add(new EsAdmin_Socio_Mozo); // socios, admin , mozo
  $group->get('/{id}', App\Controller\PedidoController::class . ':TraerUno' )->add(new EsAdmin_Socio_Mozo); //socios, admin, mozo  
  $group->post('/entregar', App\Controller\PedidoController::class . ':EntregarPedido')->add(new EsAdmin_Socio_Mozo); //socios, admin , mozo
  $group->post('/cobrar', App\Controller\PedidoController::class . ':CobrarPedido')->add(new EsAdmin_Socio); // socios, admin 
  $group->post('/demora', App\Controller\PedidoController::class . ':InformarDemora'); //esta logeado 
})->add(new EstaLogeado);
*/

$app->run();

<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

require_once("./middlewares/socioCheckMiddleware.php");
require_once("./middlewares/mozoCheckMiddleware.php");
require_once("./middlewares/pedidosMiddleware.php");
require_once("./middlewares/jwtCheckMiddleware.php");
require_once("./middlewares/loginMiddleware.php");


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
//$app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();


// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController::class . ':Alta')->add(new socioCheckMiddleware())->add(new jwtCheckMiddleware()); //Solo socio
    $group->get('[/]', \UsuarioController::class . ':ObtenerTodos')->add(new socioCheckMiddleware())->add(new jwtCheckMiddleware()); //Solo socio
    $group->post('/login', \UsuarioController::class . ':login')->add(new loginMiddleware); 
  });

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \ProductoController::class . ':Alta')->add(new socioCheckMiddleware()); //Solo socio
  $group->get('[/]', \ProductoController::class . ':ObtenerTodos')->add(new mozoCheckMiddleware()); //mozo y socio
})->add(new jwtCheckMiddleware());   

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController::class . ':Alta')->add(new mozoCheckMiddleware()); //mozo y socio
  $group->get('[/]', \MesaController::class . ':ObtenerTodos')->add(new mozoCheckMiddleware()); //mozo y socio
})->add(new jwtCheckMiddleware());

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \PedidoController::class . ':Alta')->add(new mozoCheckMiddleware()); //mozo y socio
  $group->post('/ActualizarPedido', \PedidoController::class . ':ActualizarPedido')->add(new pedidosMiddleware());
  $group->get('[/]', \PedidoController::class . ':ObtenerTodos');    
})->add(new jwtCheckMiddleware());   



$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP");
    return $response;
});

$app->run();

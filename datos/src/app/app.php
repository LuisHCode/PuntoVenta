<?php

use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html');
$dotenv->load();

$container = new Container();

AppFactory::SetContainer($container);

$app = AppFactory::create();

require 'config.php';

$app->add(new Tuupola\Middleware\JwtAuthentication([
  "secure" => false,
  "path" => ["/api"],
  "ignore" => ["/api/auth", "/api/user", "/api/cliente"],
  "secret" => ["acme" => $container->get('key')],
  "algorithm" => ["acme" => "HS256"],
]));

//$rules = [
//  new RequestMethodRule(ignore: ['OPTIONS']),
//  new RequestPathRule(
//    paths: ['/api'], 
//    ignore: ['/api/auth'])
//]; //!Nuevo 4/05/25

//$decoder = new FirebaseDecoder(new Secret($container->get('key'), 'HS256'));
//$authentication = new JwtAuthentication(new Options(), $decoder, $rules);
//$app->addMiddleware($authentication); /* Nuevo 4/05/25 */

require 'conexion.php';
require 'routes.php';

$app->run();
<?php

use Slim\Factory\AppFactory;
use DI\Container;
use JimTools\JwtAuth\Rules\RequestMethodRule; //!Nuevo 4/05/25
use JimTools\JwtAuth\Rules\RequestPathRule; //!Nuevo 4/05/25
use JimTools\JwtAuth\Middleware\JwtAuthentication;
use JimTools\JwtAuth\Options;
use JimTools\JwtAuth\Decoder\FirebaseDecoder;
use JimTools\JwtAuth\Secret;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html');
$dotenv->load();

$container = new Container();

AppFactory::SetContainer($container);

$app = AppFactory::create();

require 'config.php';

$rules = [
  new RequestMethodRule(ignore: ['OPTIONS']),
  new RequestPathRule(
    paths: ['/api'], 
    ignore: ['/api/auth'])
]; //!Nuevo 4/05/25

$decoder = new FirebaseDecoder(new Secret($container->get('key'), 'HS256'));
$authentication = new JwtAuthentication(new Options(), $decoder, $rules);
$app->addMiddleware($authentication); /* Nuevo 4/05/25 */

require 'conexion.php';
require 'routes.php';

$app->run();
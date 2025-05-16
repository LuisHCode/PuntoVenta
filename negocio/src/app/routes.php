<?php
    namespace App\controllers;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    use Slim\Routing\RouteCollectorProxy;

    $app->get('/', function (Request $request, Response $response, $args) {
        $response->getBody()->write("Bienvenido al Servidor de Negocios");
        return $response;
    });

    $app->group('/api',function(RouteCollectorProxy $api){
        $api->group('/artefacto',function(RouteCollectorProxy $artefacto){
            $artefacto->get('/read[/{id}]', Artefacto::class . ':read');
            $artefacto->post('', Artefacto::class . ':create');
            $artefacto->put('/{id}', Artefacto::class . ':update');
            $artefacto->delete('/{id}', Artefacto::class . ':delete');
            $artefacto->get('/filtrar/{pag}/{lim}', Artefacto::class . ':filtrar');
        });
    });


    
<?php
namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class Artefacto {
    protected $container;

    public function __construct(ContainerInterface $c){
        $this->container = $c;
    }

    public function read(Request $request, Response $response, $args){
        $sql = "SELECT * FROM artefacto";
        if(isset($args['id'])){
            $sql .= " WHERE id = :id";
        }
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        if(isset($args['id'])){
            $query->execute(['id' => $args['id']]);
        } else {
            $query->execute();
        }

        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;

        $response->getBody()->write(json_encode($res));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function create(Request $request, Response $response){
        $body = json_decode($request->getBody());

        $sql = "INSERT INTO artefacto (id, idCliente, serie, marca, modelo, categoria, descripcion) 
                VALUES (:id, :idCliente, :serie, :marca, :modelo, :categoria, :descripcion)";

        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $ok = $query->execute([
            'id' => $body->id,
            'idCliente' => $body->idCliente,
            'serie' => $body->serie,
            'marca' => $body->marca,
            'modelo' => $body->modelo,
            'categoria' => $body->categoria,
            'descripcion' => $body->descripcion
        ]);

        $response->getBody()->write(json_encode(["success" => $ok]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());

        $sql = "UPDATE artefacto SET 
                idCliente = :idCliente,
                serie = :serie,
                marca = :marca,
                modelo = :modelo,
                categoria = :categoria,
                descripcion = :descripcion
                WHERE id = :id";

        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $ok = $query->execute([
            'id' => $args['id'],
            'idCliente' => $body->idCliente,
            'serie' => $body->serie,
            'marca' => $body->marca,
            'modelo' => $body->modelo,
            'categoria' => $body->categoria,
            'descripcion' => $body->descripcion
        ]);

        $response->getBody()->write(json_encode(["updated" => $ok]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args){
        $sql = "DELETE FROM artefacto WHERE id = :id";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $ok = $query->execute(['id' => $args['id']]);

        $response->getBody()->write(json_encode(["deleted" => $ok]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

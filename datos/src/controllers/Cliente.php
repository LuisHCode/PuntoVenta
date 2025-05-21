<?php
namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use PDO;

class Cliente extends Persona
{
    protected $container;
    private const ROL = 4;
    private const RECURSO = "Cliente";

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function read(Request $request, Response $response, $args)
    {
        $sql = "SELECT * FROM cliente ";

        if (isset($args['id'])) {
            $sql .= "WHERE id = :id ";
        }

        $sql .= " LIMIT 0,5;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        if (isset($args['id'])) {
            $query->execute(['id' => $args['id']]);
        } else {
            $query->execute();
        }

        $res = $query->fetchAll();

        $status = $query->rowCount() > 0 ? 200 : 204;

        $query = null;
        $con = null;

        $response->getbody()->write(json_encode($res));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function create(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), 1);
        $status = $this->createP(self::RECURSO, self::ROL, $body);
        return $response->withStatus($status);
    }

    public function update(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), 1);
        $status = $this->updateP(self::RECURSO, $body, $args['id']);
        return $response->withStatus($status);
    }


    public function delete(Request $request, Response $response, $args)
    {


        $sql = "SELECT eliminarCliente(:id);";
        $con = $this->container->get('base_datos');

        $query = $con->prepare($sql);

        $query->bindValue('id', $args['id'], PDO::PARAM_INT);
        $query->execute();

        $resp = $query->fetch(PDO::FETCH_NUM)[0];

        $status = $resp > 0 ? 200 : 404;

        $query = null;
        $con = null;

        return $response->withStatus($status);
    }

    public function filtrar(Request $request, Response $response, $args)
    {
        // %serie%&%modelo%&%marca%&%categoria%&

        $datos = $request->getQueryParams();
        $filtro = "%";
        foreach ($datos as $key => $value) {
            $filtro .= "$value%&%";
        }
        $filtro = substr($filtro, 0, -1);

        $sql = "CALL filtrarCliente('$filtro', {$args['pag']},{$args['lim']});";

        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        //die($sql);

        $query->execute();

        $res = $query->fetchAll();

        $status = $query->rowCount() > 0 ? 200 : 204;

        $query = null;
        $con = null;


        $response->getbody()->write(json_encode($res));


        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }



}

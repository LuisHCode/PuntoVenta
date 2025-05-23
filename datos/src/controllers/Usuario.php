<?php
namespace App\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use PDO;

class Usuario
{
    private $container;
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    private function editarUsuario(string $idUsuario, $rol = -1, string $passw = "")
    {
        $sql = $rol == -1 ? "CALL passwUsuario(:idUsuario, :passw)" : "CALL rolUsuario(:idUsuario, :rol)";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        
        $query->bindValue(':idUsuario', $idUsuario, PDO::PARAM_STR);
        if ($rol != -1) {
            $query->bindValue(':rol', $rol, PDO::PARAM_INT);
        }   
        if ($passw != "") {
            $query->bindValue(':passw', $passw, PDO::PARAM_STR);
        }
        $query->execute();
        $afect = $query->rowCount();
        $query = null;  
        $con = null;
        return $afect;
    }
    public function changePassw(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        //Primero hay que autenticar al usuario
        $passwN = password_hash($body->passwN, PASSWORD_BCRYPT, ['cost' => 10]);
        $passwN = $body->passwN;
        $resp = $this->editarUsuario($args['idUsuario'], passw: $passwN);
        $status = 200;

        return $response->withStatus($status);
    }

    public function resetPassw(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        $passwN = password_hash($body->passwN, PASSWORD_BCRYPT, ['cost' => 10]);
        $passwN = $body->passwN;
        $resp = $this->editarUsuario($args['idUsuario'], passw: $passwN);
        $status = 200;

        return $response->withStatus($status);
    }

    public function changeRol(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        $resp = $this->editarUsuario($args['idUsuario'], rol: $body->rol);
        $status = 200;

        return $response->withStatus($status);
    }
}
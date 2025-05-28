<?php
namespace App\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use PDO;

class Usuario extends Autenticar
{
    // No se declara $container ni constructor aquí, se usa el de la clase padre

    private function editarUsuario(string $idUsuario, $rol = -1, string $passw = "")
    {
        // Acceso correcto a la propiedad protegida $container de la clase padre
        $con = $this->container->get('base_datos');
        $sql = $rol == -1 ? "CALL passwUsuario(:idUsuario, :passw)" : "CALL rolUsuario(:idUsuario, :rol)";
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
        if ($this->autenticar($args['idUsuario'], $body->passw, true)) {
            $passwN = password_hash($body->passwN, PASSWORD_BCRYPT, ['cost' => 10]);
            $status = $this->editarUsuario($args['idUsuario'], passw: $passwN)
            == 0 ? 404 : 200;
        } else {
            $status = 401;
            $passwN = "";
        }
        return $response->withStatus($status);
    }

    public function resetPassw(Request $request, Response $response, $args)
    {
        $passwN = password_hash($args['idUsuario'], PASSWORD_BCRYPT, ['cost' => 10]);
        $status = $this->editarUsuario($args['idUsuario'], passw: $passwN)
            == 0 ? 404 : 200;
        return $response->withStatus($status);
    }

    public function changeRol(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        $status = $this->editarUsuario($args['idUsuario'], rol: $body->rol)
            == 0 ? 404 : 200;
        return $response->withStatus($status);
    }
}
<?php
namespace App\controllers;
use Psr\Container\ContainerInterface;

use PDO;

class Autenticar
{
    protected $container;
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    protected function autenticar($idUsuario, $passw, $cambioPassw = false){
        $sql = "SELECT * FROM USUARIO WHERE idUsuario = :idUsuario
            OR correo = :idUsuario;";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        $query->bindValue(':idUsuario', $idUsuario, PDO::PARAM_STR);
        $query->execute();
        $datos = $query->fetch();

        if($datos && password_verify($passw, $datos->passw)){
            $retorno = ["rol" => $datos->rol];

            $recurso = match ($datos->rol){
                1 => "Administrador",
                2 => "Oficinista",
                3 => "Tecnico",
                4 => "Cliente"
            };

            $sql = "SELECT * FROM $recurso WHERE id$recurso = :idUsuario
                OR correo = :idUsuario;";
            $query = $con->prepare($sql);
            $query->bindValue(':idUsuario', $idUsuario, PDO::PARAM_STR);
            $query->execute();
            $datos = $query->fetch();
            if ($datos && isset($datos->nombre)) {
                $retorno['datos'] = $datos->nombre;
            } else {
                $retorno['datos'] = null;
            }
        }
        $query = null;
        $con = null;
        return isset($retorno) ? $retorno : null;
    }
}
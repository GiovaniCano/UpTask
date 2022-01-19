<?php
namespace Controllers;

use Model\Proyecto;
use MVC\Router;

class DashboardController {
    public static function index(Router $router) {
        session_start();
        isAuth();

        $proyectos = Proyecto::belongsTo("propietarioId", $_SESSION["id"]);
        
        $router->render("dashboard/index", [
            "titulo" => "Proyectos",
            "proyectos" => $proyectos
        ]);
    }

    public static function crear_proyectos(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $proyecto = new Proyecto($_POST);
            
            // validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                // url unica
                $proyecto->url = md5(uniqid());

                // creador
                $proyecto->propietarioId = $_SESSION["id"];

                // guardar proyecto
                $proyecto->guardar();

                // redireccionar
                exit(header("location: /proyecto?id=".$proyecto->url));
            }
        }
        
        $router->render("dashboard/crear-proyectos", [
            "titulo" => "Crear Proyectos",
            "alertas" => $alertas
        ]);
    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth();

        $token = $_GET["id"];
        if(!$token) exit(header("location: /dashboard"));
        // revisar que quien visite el proyecto es quien lo creo
        $proyecto = Proyecto::where("url", $token);
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) exit(header("location: /dashboard"));

        $router->render("dashboard/proyecto", [
            "titulo" => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router) {
        session_start();
        isAuth();
        
        $router->render("dashboard/perfil", [
            "titulo" => "Perfil"
        ]);
    }
}
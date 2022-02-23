<?php
namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
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
            $_POST = cleanAssocArray($_POST, ["proyecto"]);
            
            $proyecto = new Proyecto($_POST);
            
            // validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                // url unica
                $proyecto->url = md5(uniqid());

                // creador
                $proyecto->propietarioId = $_SESSION["id"];

                // guardar proyecto
                // $proyecto->guardar();
                $proyecto->crear();

                // redireccionar
                exit(header("location: /proyecto?id=".$proyecto->url));
            }
        }
        
        $router->render("dashboard/crear-proyectos", [
            "titulo" => "Crear Proyectos",
            "alertas" => $alertas
        ]);
    }

    public static function eliminar_proyectos() {
        session_start();
        isAuth();
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $proyectoId = $_POST["proyectoId"] ?? "";
            if(!$proyectoId) exit(header("location: /dashboard"));

            $proyecto = Proyecto::where("url", $proyectoId);
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) exit(header("location: /dashboard"));

            $proyecto->eliminar();
            exit(header("location: /dashboard"));
        }
    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth();

        $token = $_GET["id"];
        if(!$token) exit(header("location: /dashboard"));
        // revisar que quien visite el proyecto es quien lo creo
        $proyecto = Proyecto::where("url", $token);
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) exit(header("location: /dashboard"));

        $alertas = [];
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $_POST = cleanAssocArray($_POST, ["proyecto"]);
            $tituloAnterior = $proyecto->proyecto;
            $proyecto->sincronizar($_POST);

            $alertas = $proyecto->validarProyecto();
            if(!$alertas) {
                $proyecto->actualizar();
                // exit(header("Location: /proyecto?id=" . $token));
            } else {
                $proyecto->proyecto = $tituloAnterior;
            }
        }

        $router->render("dashboard/proyecto", [
            "titulo" => $proyecto->proyecto,
            "proyectoId" => $token,
            "alertas" => $alertas
        ]);
    }

    public static function perfil(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION["id"]);

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $_POST = cleanAssocArray($_POST, ["nombre", "email"]);

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPerfil();

            if(empty($alertas)) {
                $existeUsuario = Usuario::where("email", $usuario->email);
                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    Usuario::setAlerta("error", "Este email ya esta registrado en otra cuenta");
                    $alertas = Usuario::getAlertas();
                } else {
                    // $usuario->guardar();
                    $usuario->actualizar();
                    
                    Usuario::setAlerta("exito", "Guardado Correctamente");
                    $alertas = Usuario::getAlertas();
    
                    // nombre nuevo a la barra
                    $_SESSION["nombre"] = $usuario->nombre;
                }
            }
        }
        
        $router->render("dashboard/perfil", [
            "titulo" => "Perfil",
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }

    public static function cambiar_password(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $_POST = cleanAssocArray($_POST, ["password_actual", "password_nuevo"]);

            $usuario = Usuario::find($_SESSION["id"]);
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevoPassword();

            if(empty($alertas)) {
                $resultado = $usuario->comprobarPassword();
                if($resultado) {
                    // asignar contraseña
                    $usuario->password = $usuario->password_nuevo;

                    // propiedades no necesarias
                    unset($usuario->password2);
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // cambiar password en db
                    $usuario->hashPassword();
                    // $resultado = $usuario->guardar();
                    $resultado = $usuario->actualizar();

                    if($resultado) {
                        Usuario::setAlerta("exito", "Guardado Correctamente");
                        $alertas = $usuario->getAlertas();
                    }                    
                } else {
                    Usuario::setAlerta("error", "Contraseña Incorrecta");
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        $router->render("dashboard/cambiar-password", [
            "titulo" => "Cambiar Contraseña",
            "alertas" => $alertas
        ]);
    }

    public static function eliminar_cuenta(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario = Usuario::find($_SESSION["id"]);
            $inputPassword = $_POST["password"] ?? "";
            $correctPassword = password_verify($inputPassword, $usuario->password);
            if($correctPassword) {
                $usuario->eliminar();
                $_SESSION = [];
                exit(header("location: /"));
            } else {
                Usuario::setAlerta("error", "Contraseña Incorrecta");
                $alertas = $usuario->getAlertas();
            }
        }

        $router->render("dashboard/eliminar-cuenta", [
            "titulo" => "Eliminar Cuenta",
            "alertas" => $alertas
        ]);
    }
}
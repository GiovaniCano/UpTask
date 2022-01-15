<?php
namespace Controllers;

use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            
        }

        // Render a la vista
        $router->render("auth/login", [
            "titulo" => "Iniciar sesión"
        ]);
    }

    public static function logout() {
        echo "desde logout";
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            debuguear($alertas);
        }

        // Render a la vista
        $router->render("auth/crear", [
            "titulo" => "Crea tu cuenta",
            "usuario" => $usuario
        ]);
    }

    public static function olvide(Router $router) {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            
        }

        $router->render("auth/olvide", [
            "titulo" => "Olvide mi contraseña"
        ]);
    }

    public static function restablecer(Router $router) {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            
        }

        $router->render("auth/restablecer", [
            "titulo" => "Restablecer contraseña"
        ]);
    }
        
    public static function mensaje(Router $router) {
        $router->render("auth/mensaje", [
            "titulo" => "Cuenta Creada Exitosamente"
        ]);
    }
        
    public static function confirmar(Router $router) {
        $router->render("auth/confirmar", [
            "titulo" => "Confirma tu Cuenta"
        ]);
    }
}
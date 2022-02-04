<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        session_start();
        if($_SESSION["login"] ?? false) exit(header("location: /dashboard"));

        $alertas = [];
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario = new Usuario($_POST);            
            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                // verificar que el usuario exista
                $usuario = Usuario::where("email", $usuario->email);
                if(!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta("error", "El Usuario no Existe o no está Confirmado");
                } else {
                    // existe y está confirmado
                    if(password_verify($_POST["password"], $usuario->password)) {
                        // Iniciar sesion
                        session_start();
                        $_SESSION["id"] = $usuario->id;
                        $_SESSION["nombre"] = $usuario->nombre;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = true;

                        exit(header("location: /dashboard"));
                    } else {
                        Usuario::setAlerta("error", "Contraseña Incorrecta");
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/login", [
            "titulo" => "Iniciar sesión",
            "alertas" => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        exit(header("location: /"));
    }

    public static function crear(Router $router) {
        session_start();
        if($_SESSION["login"] ?? false) exit(header("location: /dashboard"));
        
        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                $existeUsuario = Usuario::where("email", $usuario->email);
                if($existeUsuario) {
                    Usuario::setAlerta("error", "Este Correo ya está Registrado");
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // eliminar password2
                    unset($usuario->password2);

                    // generar token
                    $usuario->crearToken();

                    // Crear nuevo usuario
                    $resultado = $usuario->guardar();

                    // enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if($resultado) {
                        exit(header("location: /mensaje"));
                    }
                }
            }
        }

        // Render a la vista
        $router->render("auth/crear", [
            "titulo" => "Crea tu cuenta",
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }

    public static function olvide(Router $router) {
        session_start();
        if($_SESSION["login"] ?? false) exit(header("location: /dashboard"));
        
        $alertas = [];
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                // buscar el usuario
                $usuario = Usuario::where("email", $usuario->email);
                if($usuario && $usuario->confirmado) {
                    // Generar nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar usuario
                    $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta
                    $usuario->setAlerta("exito", "Hemos Enviado las Instrucciones a tu Email");
                } else {
                    Usuario::setAlerta("error", "El Usuario no Existe o no está Confirmado");
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render("auth/olvide", [
            "titulo" => "Olvide mi contraseña",
            "alertas" => $alertas
        ]);
    }

    public static function restablecer(Router $router) {
        $token = s($_GET["token"]);
        if(!$token) exit(header("location: /"));

        $mostrarFormulario = true; //formulario

        // identificar al usuario con el token
        $usuario = Usuario::where("token", $token);
        if(empty($usuario)) {
            Usuario::setAlerta("error", "Token no Válido");
            $mostrarFormulario = false;
        }

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            // añadir la nueva contraseña
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                $usuario->hashPassword();
                $usuario->token = null;
                $resultdo = $usuario->guardar();
                if($resultdo) exit(header("location: /"));
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/restablecer", [
            "titulo" => "Restablecer contraseña",
            "alertas" => $alertas,
            "mostrarFormulario" => $mostrarFormulario
        ]);
    }
        
    public static function mensaje(Router $router) {
        $router->render("auth/mensaje", [
            "titulo" => "Cuenta Creada Exitosamente"
        ]);
    }
        
    public static function confirmar(Router $router) {
        $token = s($_GET["token"]);
        if(!$token) exit(header("location: /"));

        // Encontrar al usuario con el token
        $usuario = Usuario::where("token", $token);
        if(empty($usuario)) {
            // token no valido
            Usuario::setAlerta("error", "Token no Válido");
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            unset($usuario->password2);
            $usuario->token = null;
            
            $usuario->guardar();
            Usuario::setAlerta("exito", "Cuenta Confirmada Correctamente");
        }

        $alertas = Usuario::getAlertas();
        $router->render("auth/confirmar", [
            "titulo" => "Confirma tu Cuenta",
            "alertas" => $alertas
        ]);
    }

    public static function p404(Router $router) {
        $router->render("auth/404", [
            "titulo" => "Error 404"
        ]);
    }
}
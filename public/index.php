<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\LoginController;
use Controllers\DashboardController;
$router = new Router();

/* login / logout */
$router->get("/", [LoginController::class, "login"]);
$router->post("/", [LoginController::class, "login"]);
$router->get("/logout", [LoginController::class, "logout"]);

/* crear cuenta */
$router->get("/crear", [LoginController::class, "crear"]);
$router->post("/crear", [LoginController::class, "crear"]);

/* olvidé mi password */
$router->get("/olvide", [LoginController::class, "olvide"]);
$router->post("/olvide", [LoginController::class, "olvide"]);

/* nuevo password */
$router->get("/restablecer", [LoginController::class, "restablecer"]);
$router->post("/restablecer", [LoginController::class, "restablecer"]);

/* confirmacion de cuenta */
$router->get("/mensaje", [LoginController::class, "mensaje"]);
$router->get("/confirmar", [LoginController::class, "confirmar"]);

// proyectos
$router->get("/dashboard", [DashboardController::class, "index"]);
$router->get("/crear-proyectos", [DashboardController::class, "crear_proyectos"]);
$router->get("/perfil", [DashboardController::class, "perfil"]);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
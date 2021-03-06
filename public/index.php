<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\LoginController;
use Controllers\DashboardController;
use Controllers\TareaController;

$router = new Router();

/* login / logout */
$router->get("/", [LoginController::class, "login"]);
$router->post("/", [LoginController::class, "login"]);
$router->get("/logout", [LoginController::class, "logout"]);

$router->get("/404", [LoginController::class, "p404"]);

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
$router->get("/reenviar-confirmacion", [LoginController::class, "reenviarConfirmacion"]);
$router->get("/confirmar", [LoginController::class, "confirmar"]);

// proyectos
$router->get("/dashboard", [DashboardController::class, "index"]);
$router->get("/crear-proyectos", [DashboardController::class, "crear_proyectos"]);
$router->post("/crear-proyectos", [DashboardController::class, "crear_proyectos"]);
$router->post("/eliminar-proyectos", [DashboardController::class, "eliminar_proyectos"]);
$router->get("/proyecto", [DashboardController::class, "proyecto"]);
$router->post("/proyecto", [DashboardController::class, "proyecto"]);
$router->get("/perfil", [DashboardController::class, "perfil"]);
$router->post("/perfil", [DashboardController::class, "perfil"]);
$router->get("/cambiar-password", [DashboardController::class, "cambiar_password"]);
$router->post("/cambiar-password", [DashboardController::class, "cambiar_password"]);
$router->get("/eliminar-cuenta", [DashboardController::class, "eliminar_cuenta"]);
$router->post("/eliminar-cuenta", [DashboardController::class, "eliminar_cuenta"]);

// API Tareas
$router->get("/api/tareas", [TareaController::class, "index"]);
$router->post("/api/tarea", [TareaController::class, "crear"]);
$router->post("/api/tarea/actualizar", [TareaController::class, "actualizar"]);
$router->post("/api/tarea/eliminar", [TareaController::class, "eliminar"]);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
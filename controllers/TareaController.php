<?php
namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController {
    public static function index() {
        session_start();
        isAuthAPI();

        $proyectoId = $_GET["id"];
        if(!$proyectoId) exit(header("location: /dashboard"));

        $proyecto = Proyecto::where("url", $proyectoId);
        if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) exit(header("location: /dashboard"));

        $tareas = Tarea::belongsTo("proyectoId", $proyecto->id);
        echo json_encode($tareas);
    }

    public static function crear() {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            session_start();
            isAuthAPI();

            $proyecto = Proyecto::where("url", $_POST["proyectoId"] ?? "");
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) {
                // error
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un error al agregar la tarea"
                ];
                echo json_encode($respuesta);
                return;
            }
            
            // crear tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            // $resultado = $tarea->guardar();
            $resultado = $tarea->crear();

            $respuesta = [
                "tipo" => "exito",
                "mensaje" => "Tarea agregada correctamente",
                "id" => $resultado["id"],
                "proyectoId" => $proyecto->id
            ];
            echo json_encode($respuesta);
        }
    }

    public static function actualizar() {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            session_start();
            isAuthAPI();

            $proyecto = Proyecto::where("url", $_POST["proyectoId"] ?? "");
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) {
                // error
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un error al actualizar la tarea"
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            // $resultado = $tarea->guardar();
            $resultado = $tarea->actualizar();

            $respuesta = [
                "tipo" => "exito",
                "id" => $tarea->id,
                "proyectoId" => $proyecto->id
            ];
            echo json_encode($respuesta);
        }
    }

    public static function eliminar() {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            session_start();
            isAuthAPI();
            
            $proyecto = Proyecto::where("url", $_POST["proyectoId"] ?? "");
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) {
                // error
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un error al eliminar la tarea"
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $resultado = $tarea->eliminar();

            $respuesta = [
                "tipo" => "exito",
                "id" => $tarea->id,
                "proyectoId" => $proyecto->id,                
                "mensaje" => "Tarea eliminada correctamente"
            ];
            echo json_encode($respuesta);
        }
    }
}
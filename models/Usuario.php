<?php
namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = "usuarios";
    protected static $columnasDB = [
        // "id",
        "nombre",
        "email",
        "password",
        "token",
        "confirmado"
    ];

    public function __construct($args = []) {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->password = $args["password"] ?? "";
        $this->password2 = $args["password2"] ?? ""; //temporal
        $this->password_actual = $args["password_actual"] ?? ""; //temporal
        $this->password_nuevo = $args["password_nuevo"] ?? ""; //temporal
        $this->token = $args["token"] ?? "";
        $this->confirmado = $args["confirmado"] ?? 0;
    }

    public function validarLogin() {
        if(!$this->email) {
            self::$alertas["error"][] = "El Email del Usuario es Obligatorio";
        }
        if(!$this->password) {
            self::$alertas["error"][] = "La Contraseña del Usuario es Obligatoria";
        }
        return self::$alertas;
    }

    public function validarNuevaCuenta() {
        if(!$this->nombre) {
            self::$alertas["error"][] = "El Nombre del Usuario es Obligatorio";
        }
        if(strlen($this->nombre) > 30) {
            self::$alertas["error"][] = "Nombre de Usuario Demasiado Largo (30 Caracteres Máximo)";
        }
        if(!$this->email || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas["error"][] = "El Email del Usuario es Obligatorio";
        }
        if(strlen($this->email) > 30) {
            self::$alertas["error"][] = "Email Demasiado Largo (30 Caracteres Máximo)";
        }

        // password
        if(!$this->password) {
            self::$alertas["error"][] = "La Contraseña del Usuario es Obligatoria";
        }
        if(strlen($this->password) < 6) {
            self::$alertas["error"][] = "La Contraseña debe Contener al menos 6 Caracteres";
        }
        if(strlen($this->password) > 60) {
            self::$alertas["error"][] = "Contraseña Demasiado Larga (60 Caracteres Máximo)";
        }
        if($this->password !== $this->password2) {
            self::$alertas["error"][] = "Las Contraseñas son Diferentes";
        }

        return self::$alertas;
    }

    public function validarPerfil() {
        if(!$this->nombre) {
            self::$alertas["error"][] = "El Nombre es Obligatorio";
        }
        if(strlen($this->nombre) > 30) {
            self::$alertas["error"][] = "Nombre de Usuario Demasiado Largo (30 Caracteres Máximo)";
        }
        if(!$this->email) {
            self::$alertas["error"][] = "El Email es Obligatorio";
        }
        if(strlen($this->email) > 30) {
            self::$alertas["error"][] = "Email Demasiado Largo (30 Caracteres Máximo)";
        }
        return self::$alertas;
    }
    public function nuevoPassword() {
        if(!$this->password_actual) self::$alertas["error"][] = "La contraseña actual es necesaria";
        if(!$this->password_nuevo) self::$alertas["error"][] = "La contraseña nueva es necesaria";
        if(strlen($this->password_nuevo) < 6) self::$alertas["error"][] = "La contraseña nueva debe contener al menos 6 caracteres";
        if(strlen($this->password_nuevo) > 60) self::$alertas["error"][] = "Contraseña Demasiado Larga (60 Caracteres Máximo)";

        return self::$alertas;
    }
    public function comprobarPassword() {
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
        // $this->token = md5(uniqid());
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas["error"][] = "El Email es Obligatorio";
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas["error"][] = "Email no Válido";
        }
        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) {
            self::$alertas["error"][] = "La Contraseña del Usuario es Obligatoria";
        }
        if(strlen($this->password) < 6) {
            self::$alertas["error"][] = "La Contraseña debe Contener al menos 6 Caracteres";
        }
        if(strlen($this->password) > 60) {
            self::$alertas["error"][] = "Contraseña Demasiado Larga (60 Caracteres Máximo)";
        }

        return self::$alertas;
    }
}
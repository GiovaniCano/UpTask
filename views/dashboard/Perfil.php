<?php include_once __DIR__ ."/header-dashboard.php" ?>

    <div class="contenedor-sm">
        <?php include_once __DIR__ . "/../templates/alertas.php" ?>
        
        <form method="POST" class="formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" value="<?php echo $usuario->nombre ?>" name="nombre" placeholder="Tu Nombre">
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?php echo $usuario->email ?>" name="email" placeholder="Tu Email">
            </div>
            
            <input type="submit" value="Guardar Cambios">
        </form>
        
        <div class="acciones-perfil">
            <a href="/cambiar-password" class="enlace">Cambiar Contraseña</a>
            <a href="/eliminar-cuenta" class="enlace">Eliminar Cuenta</a>
        </div>
    </div>

<?php include_once __DIR__ ."/footer-dashboard.php" ?>
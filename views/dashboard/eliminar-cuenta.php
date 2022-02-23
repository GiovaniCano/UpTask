<?php include_once __DIR__ ."/header-dashboard.php" ?>

    <div class="contenedor-sm">
        <?php include_once __DIR__ . "/../templates/alertas.php" ?>
        
        <a href="/perfil" class="enlace">Volver a Perfil</a>

        <form method="POST" class="formulario">
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Confirma tu contraseña">
            </div>

            <input type="submit" value="Eliminar Permanentemente">
        </form>
    </div>

<?php include_once __DIR__ ."/footer-dashboard.php" ?>
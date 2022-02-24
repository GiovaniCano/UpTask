<div class="contenedor mensaje">
    <?php include_once __DIR__ . "/../templates/nombre-sitio.php"?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Hemos enviado las instrucciones a tu email para confirmar tu cuenta de UpTask</p>

        
        <div class="acciones">
            <a href="/reenviar-confirmacion?e=<?php echo $email ?>">Volver a Enviar</a>
        </div>
    </div> <!--contenedor-sm-->

</div>
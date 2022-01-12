<div class="contenedor login">
    <h1 class="uptask">UpTask</h1>
    <p class="tagline">Crea y Administra tus Proyectos</p>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>

        <form action="POST" class="formulario">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu Email" name="email">
            </div>
            <div class="campo">                
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="Tu Contraseña" name="password">
            </div>
            <input type="submit" class="boton" value="Iniciar Sesión">
        </form>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Obtener una</a>
            <a href="/olvide">¿Olvidaste tu contraseña?</a>
        </div>
    </div> <!--contenedor-sm-->
</div>
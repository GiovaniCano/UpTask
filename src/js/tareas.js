(function(){
    obtenerTareas()
    let tareas = [];

    // boton para mostrar el modal de agregar tarea
    const nuevaTareaBtn = document.querySelector("#agregar-tarea")
    nuevaTareaBtn.addEventListener("click", mostrarFormulario)

    async function obtenerTareas() {
        try {
            const url = "/api/tareas?id=" + obtenerProyecto()
            const respuesta = await fetch(url)
            const resultado = await respuesta.json()

            tareas = resultado
            mostrarTareas()
        } catch (error) {
            console.error(error)
        }
    }

    function mostrarTareas() {
        limpiarTareas()
        if(tareas.length === 0) {
            const contenedorTareas = document.querySelector("#listado-tareas")

            const textoNoTareas = document.createElement("LI")
            textoNoTareas.textContent = "No Hay Tareas"
            textoNoTareas.classList.add("no-tareas")

            contenedorTareas.appendChild(textoNoTareas)
            return
        }

        const estados = { //diccionario
            0: "Pendiente",
            1: "Completa"
        }
        tareas.forEach(tarea=>{
            const contenedorTarea = document.createElement("LI")
            contenedorTarea.dataset.tareaId = tarea.id
            contenedorTarea.classList.add("tarea")

            const nombreTarea = document.createElement("P")
            nombreTarea.textContent = tarea.nombre

            const opcionesDiv = document.createElement("DIV")
            opcionesDiv.classList.add("opciones")

            const btnEstadoTarea = document.createElement("BUTTON")
            btnEstadoTarea.classList.add("estado-tarea")
            btnEstadoTarea.classList.add(estados[tarea.estado].toLowerCase())
            btnEstadoTarea.textContent = estados[tarea.estado]
            btnEstadoTarea.dataset.estadoTarea = tarea.estado
            btnEstadoTarea.onclick = function() {
                cambiarEstadoTarea({...tarea})
            }

            const btnEliminarTarea = document.createElement("BUTTON")
            btnEliminarTarea.classList.add("eliminar-tarea")
            btnEliminarTarea.dataset.idTarea = tarea.id
            btnEliminarTarea.textContent = "Eliminar"
            
            opcionesDiv.appendChild(btnEstadoTarea)
            opcionesDiv.appendChild(btnEliminarTarea)

            contenedorTarea.appendChild(nombreTarea)
            contenedorTarea.appendChild(opcionesDiv)

            document.querySelector("#listado-tareas").appendChild(contenedorTarea)
        })
    }

    function mostrarFormulario() {
        const modal = document.createElement("DIV")
        modal.classList.add("modal")
        modal.innerHTML = `
            <form method="POST" class="formulario nueva-tarea">
                <legend>Añade una nueva tarea</legend>
                <div class="campo">
                    <label for="tarea">Tarea</label>
                    <input type="text" name="tarea" placeholder="Añadir tarea al proyecto actual" id="tarea">
                </div>
                <div class="opciones">
                    <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea">
                    <button type="button" class="cerrar-modal">Cerrar</button>
                </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector(".formulario")
            formulario.classList.add("animar");
        }, 0);

        modal.addEventListener("click", function(e){
            e.preventDefault()
            if(e.target.classList.contains("cerrar-modal")) {
                const formulario = document.querySelector(".formulario")
                formulario.classList.add("cerrar");

                setTimeout(() => {
                    modal.remove()                    
                }, 400);
            }
            if(e.target.classList.contains("submit-nueva-tarea")) {
                submitFormularioNuevaTarea()
            }
        })

        document.querySelector(".dashboard").appendChild(modal)
    }

    function submitFormularioNuevaTarea() {
        const tarea = document.querySelector("#tarea").value.trim()
        if(tarea === "") {
            const referencia = document.querySelector(".formulario legend")
            mostrarAlerta("El nombre de la tarea es obligatorio", "error", referencia)
            return
        }

        agregarTarea(tarea)
    }

    async function agregarTarea(tarea) {
        // construir la peticion
        const datos = new FormData();
        datos.append("nombre", tarea);
        datos.append("proyectoId", obtenerProyecto());

        try {
            const url = "http://localhost/api/tarea";
            const respuesta = await fetch(url, {
                method: "POST",
                body: datos
            })
            const resultado = await respuesta.json()
            
            const referencia = document.querySelector(".formulario legend")
            mostrarAlerta(resultado.mensaje, resultado.tipo, referencia)

            if(resultado.tipo === "exito") {
                // vaciar input al agregar tarea
                document.querySelector("#tarea").value = ""

                // agregar el objeto de tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: "0",
                    proyectoId: resultado.proyectoId
                }
                tareas = [...tareas, tareaObj]
                mostrarTareas()
            }

        } catch (error) {
            console.error(error)
        }
    }

    function cambiarEstadoTarea(tarea) {
        // no mutar tareas, usar {...tarea}
        // tareas debe cambiar hasta que se efectue el cambio en el servidor
        const nuevoEstado = tarea.estado === "1" ? "0" : "1";
        tarea.estado = nuevoEstado
        actualizarTarea(tarea)
    }

    async function actualizarTarea(tarea) {
        const {estado, id, nombre, proyectoId} = tarea
        const datos = new FormData()
        datos.append("id", id)
        datos.append("nombre", nombre)
        datos.append("estado", estado)
        datos.append("proyectoId", obtenerProyecto()) //url

        // for(let valor of datos.values()) { //para debugear el formData()
        //     console.log(valor)
        // }

        try {
            const url = "http://localhost/api/tarea/actualizar"
            const respuesta = await fetch(url, {
                method: "POST",
                body: datos
            })
            const resultado = await respuesta.json()

            if(resultado.tipo === "exito") {
                tareas = tareas.map(tareaMemoria=>{
                    if(tareaMemoria.id === id) {
                        tareaMemoria.estado = estado
                    }
                    return tareaMemoria
                })
                mostrarTareas()
            } else {
                // error
                const referencia = document.querySelector(".contenedor-nueva-tarea")
                mostrarAlerta(resultado.mensaje, resultado.tipo, referencia)
            }
        } catch (error) {
            console.error(error)
        }
    }

    function obtenerProyecto() { // ?=
        const proyectoParams = new URLSearchParams(window.location.search)
        const proyecto = Object.fromEntries(proyectoParams.entries())
        return proyecto.id
    }

    function mostrarAlerta(mensaje, tipo, referencia) {
        const alertaPrevia = document.querySelector(".alerta")
        if(alertaPrevia) {
            alertaPrevia.remove()
        }

        const alerta = document.createElement("DIV")
        alerta.classList.add("alerta", tipo)
        alerta.textContent = mensaje
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling)

        setTimeout(() => {
            alerta.remove()
        }, 5000);
    }

    function limpiarTareas() {
        const listadoTareas = document.querySelector("#listado-tareas")
        
        while(listadoTareas.firstChild) { //más rapido que innerHtml = ""
            listadoTareas.removeChild(listadoTareas.firstChild)
        }
    }
})()
(function(){
    obtenerTareas()
    editarNombreProyecto()
    confirmarEliminarProyecto()
    let tareas = [] //tareas en memoria, para virtualDOM
    let filtradas = []

    // boton para mostrar el modal de agregar tarea
    const nuevaTareaBtn = document.querySelector("#agregar-tarea")
    nuevaTareaBtn.addEventListener("click", function() {
        mostrarFormulario()
    })

    // filtros
    const filtros = document.querySelectorAll('#filtros input[type="radio"]')
    filtros.forEach(radio=>radio.addEventListener("input", filtrarTareas))

    function filtrarTareas(e) {
        const filtro = e.target.value
        if(filtro !== "") { //filtrar
            filtradas = tareas.filter(tarea=>tarea.estado === filtro)
        } else { //todas
            filtradas = []
        }
        mostrarTareas()
    }

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
        totalPendientes()
        totalCompletadas()

        const arrayTareas = filtradas.length ? filtradas : tareas ;

        if(arrayTareas.length === 0) {
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
        arrayTareas.forEach(tarea=>{
            const contenedorTarea = document.createElement("LI")
            contenedorTarea.dataset.tareaId = tarea.id
            contenedorTarea.classList.add("tarea")

            const nombreTarea = document.createElement("P")
            nombreTarea.textContent = tarea.nombre
            nombreTarea.ondblclick = function() {
                if (window.getSelection) {
                    window.getSelection().removeAllRanges();
                } else if (document.selection) { 
                    document.selection.empty();
                }
                mostrarFormulario(true, {...tarea})
            }

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
            btnEliminarTarea.onclick = function() {
                confirmarEliminarTarea({...tarea})
            }
            
            opcionesDiv.appendChild(btnEstadoTarea)
            opcionesDiv.appendChild(btnEliminarTarea)

            contenedorTarea.appendChild(nombreTarea)
            contenedorTarea.appendChild(opcionesDiv)

            document.querySelector("#listado-tareas").appendChild(contenedorTarea)
        })
    }
    function totalPendientes() {
        const totalPendientes = tareas.filter(tarea=>tarea.estado==="0")
        const pendientesRadio = document.querySelector("#pendientes")
        if(totalPendientes.length===0) {
            pendientesRadio.disabled = true
        } else {
            pendientesRadio.disabled = false
        }
    }
    function totalCompletadas() {
        const totalCompletadas = tareas.filter(tarea=>tarea.estado==="1")
        const completadasRadio = document.querySelector("#completadas")
        if(totalCompletadas.length===0) {
            completadasRadio.disabled = true
        } else {
            completadasRadio.disabled = false
        }
    }

    function mostrarFormulario(editar = false, tarea = {}) {
        const modal = document.createElement("DIV")
        modal.classList.add("modal")
        modal.innerHTML = `
            <form method="POST" class="formulario nueva-tarea">
                <legend>${editar ? "Editar Tarea" : "A??ade una nueva tarea"}</legend>
                <div class="campo">
                    <label for="tarea">Tarea</label>
                    <input type="text" name="tarea" placeholder="${editar ? 'Edita la tarea' : 'A??adir tarea al proyecto actual'}" id="tarea" value="${editar ? tarea.nombre : ''}">
                </div>
                <div class="opciones">
                    <input type="submit" class="submit-nueva-tarea" value="${editar ? 'Guardar Cambios' : 'A??adir Tarea'}">
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
                // validar
                const nombreTarea = document.querySelector("#tarea").value.trim()
                const referencia = document.querySelector(".formulario legend")
                if(nombreTarea === "") {
                    mostrarAlerta("El nombre de la tarea es obligatorio", "error", referencia)
                    return
                }
                if(nombreTarea.length > 60) {
                    mostrarAlerta("Nombre de la tarea demasiado largo (60 Caracteres M??ximo)", "error", referencia)
                    return
                }
        
                if(editar) {
                    tarea.nombre = nombreTarea //nuevo nombre
                    actualizarTarea(tarea)
                } else { //crear
                    agregarTarea(nombreTarea)
                }
            }
        })

        document.querySelector(".dashboard").appendChild(modal)
    }

    async function agregarTarea(tarea) {
        // construir la peticion
        const datos = new FormData();
        datos.append("nombre", tarea);
        datos.append("proyectoId", obtenerProyecto());

        try {
            const url = "/api/tarea";
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
        const {estado, id, nombre} = tarea
        const datos = new FormData()
        datos.append("id", id)
        datos.append("nombre", nombre)
        datos.append("estado", estado)
        datos.append("proyectoId", obtenerProyecto()) //url

        // for(let valor of datos.values()) { //para debugear el formData()
        //     console.log(valor)
        // }

        try {
            const url = "/api/tarea/actualizar"
            const respuesta = await fetch(url, {
                method: "POST",
                body: datos
            })
            const resultado = await respuesta.json()

            if(resultado.tipo === "exito") {
                // cerrar modal
                const modal = document.querySelector(".modal")
                if(modal) {
                    const formulario = document.querySelector(".formulario")
                    formulario.classList.add("cerrar");
                    setTimeout(() => {
                        modal.remove()                    
                    }, 400);
                }

                // recargar tareas
                tareas = tareas.map(tareaMemoria=>{
                    if(tareaMemoria.id === id) {
                        tareaMemoria.estado = estado
                        tareaMemoria.nombre = nombre
                    }
                    return tareaMemoria
                })
                mostrarTareas()
            } else {
                // error
                const referencia = document.querySelector(".contenedor-nueva-tarea")
                // mostrarAlerta(resultado.mensaje, resultado.tipo, referencia)
                Swal.fire("Error", resultado.mensaje, "error")
            }
        } catch (error) {
            console.error(error)
        }
    }

    function confirmarEliminarTarea(tarea) {
        Swal.fire({
            title: '??Eliminar Tarea?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarTarea(tarea)
            }
        })
    }
    async function eliminarTarea(tarea) {
        const {estado, id, nombre} = tarea
        const datos = new FormData()
        datos.append("id", id)
        datos.append("nombre", nombre)
        datos.append("estado", estado)
        datos.append("proyectoId", obtenerProyecto()) //url

        try {
            const url = "/api/tarea/eliminar"
            const respuesta = await fetch(url, {
                method: "POST",
                body: datos
            })
            const resultado = await respuesta.json()
            
            if(resultado.tipo === "exito") {
                // const referencia = document.querySelector(".contenedor-nueva-tarea")
                // mostrarAlerta(resultado.mensaje, resultado.tipo, referencia)
                Swal.fire("Eliminado", resultado.mensaje, "success")

                tareas = tareas.filter(tareaMemoria=>tareaMemoria.id !== tarea.id)
                mostrarTareas()
            } else {
                // error
                // const referencia = document.querySelector(".contenedor-nueva-tarea")
                // mostrarAlerta(resultado.mensaje, resultado.tipo, referencia)
                
                Swal.fire("Error", resultado.mensaje, "error")
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

        while(listadoTareas.firstChild) { //m??s rapido que innerHtml = ""
            listadoTareas.removeChild(listadoTareas.firstChild)
        }
    }

    function editarNombreProyecto() {
        const titulo = document.querySelector(".nombre-pagina")
        titulo.ondblclick = function(e) {
            if (window.getSelection) {
                window.getSelection().removeAllRanges();
            } else if (document.selection) { 
                document.selection.empty();
            }
            const nombreProyecto = e.target.innerText
            const modal = document.createElement("DIV")
            modal.classList.add("modal")
            modal.innerHTML = `
                <form method="POST" class="formulario nueva-tarea">
                    <legend>Nombre del Proyecto</legend>
                    <div class="campo">
                        <input type="text" name="proyecto" placeholder="Nombre del proyecto" value="${nombreProyecto}">
                    </div>
                    <div class="opciones">
                        <input type="submit" class="submit-nueva-tarea" value="Guardar Cambios">
                        <button type="button" class="cerrar-modal">Cerrar</button>
                    </div>
                </form>
            `;
    
            setTimeout(() => {
                const formulario = document.querySelector(".formulario")
                formulario.classList.add("animar");
            }, 0);
    
            modal.querySelector(".cerrar-modal").addEventListener("click", function(){
                const formulario = document.querySelector(".formulario")
                formulario.classList.add("cerrar");

                setTimeout(() => {
                    modal.remove()                    
                }, 400);
            })
    
            document.querySelector(".dashboard").appendChild(modal)            
        }
    }

    function confirmarEliminarProyecto() {
        const formEliminar = document.getElementById("eliminar-proyecto")
        if(formEliminar) {
            formEliminar.onsubmit = function(e) {
                const form = e.currentTarget
                e.preventDefault()
                Swal.fire({
                    title: '??Eliminar Proyecto?',
                    showCancelButton: true,
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit()
                    }
                })
            }
        }
    }
})()
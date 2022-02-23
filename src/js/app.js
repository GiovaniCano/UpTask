const sidebar = document.querySelector(".sidebar")
const mobileMenuBtn = document.querySelector("#mobile-menu")
const cerrarMenuBtn = document.querySelector("#cerrar-menu")

if(mobileMenuBtn) mobileMenuBtn.addEventListener("click", function() {
    sidebar.classList.add("mostrar")
})

if(cerrarMenuBtn) cerrarMenuBtn.addEventListener("click", function() {
    sidebar.classList.add("ocultar")
    setTimeout(() => {
        sidebar.classList.remove("mostrar")
        sidebar.classList.remove("ocultar")
    }, 500);
})

// elimina la clase de mostrar en un tamaño de tablet y mayor
window.addEventListener("resize", function() {    
    const anchoPantalla = document.body.clientWidth
    if(anchoPantalla >= 768) {
        sidebar.classList.remove("mostrar")
    }
})

/* quitar alertas */
const alertas = document.getElementsByClassName("alerta")
if(alertas) {
    for(let alerta of alertas) {
        setTimeout(() => {
            if(alerta) alerta.remove()
        }, 4000);
    }
}
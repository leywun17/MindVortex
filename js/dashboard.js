// Sidebar
let sidebar = document.querySelector(".sidebar");
let closeBtn = document.querySelector("#btn");
let searchBtn = document.getElementById("btn-enviar");
let navList = document.querySelector(".nav-list");

closeBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    navList.classList.toggle("scroll");
    menuBtnChange(); // Llamando a la función para cambiar el ícono (opcional)
});

searchBtn.addEventListener("click", () => {
    const inputUsuario = document.getElementById("msger-input").value.trim();

    // Solo abrir la sidebar si el input está vacío (para no interferir con el envío de mensajes financieros)
    if (inputUsuario === "") {
        sidebar.classList.toggle("open");
        menuBtnChange(); // Llamando a la función para cambiar el ícono (opcional)
    }
});

// Cambiar el ícono del botón de la sidebar (opcional)
function menuBtnChange() {
    if (sidebar.classList.contains("open")) {
        closeBtn.classList.replace("bx-menu", "bx-menu-alt-right"); // Reemplazar la clase del ícono
    } else {
        closeBtn.classList.replace("bx-menu-alt-right", "bx-menu"); // Reemplazar la clase del ícono
    }
}

// Calculadora (evitando interferencias con la sidebar)
// Si el código de la calculadora está en el mismo archivo, asegúrate de no reutilizar los mismos IDs o clases  
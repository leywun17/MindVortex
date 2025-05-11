document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector(".sidebar");
    const closeBtn = document.querySelector("#btn");
    const container = document.querySelector(".contenedor-principal");
    
    closeBtn.addEventListener("click", function() {
        sidebar.classList.toggle("active");
        container.classList.toggle("shifted");
    });
})
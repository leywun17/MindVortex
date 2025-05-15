document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector(".sidebar");
    const closeBtn = document.querySelector("#btn");
    const container = document.querySelector(".contenedor-principal");

    closeBtn.addEventListener("click", function () {
        sidebar.classList.toggle("active");
        container.classList.toggle("shifted");
    });
})

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('d-none'));
        document.querySelector(btn.dataset.target).classList.remove('d-none');
    });
});
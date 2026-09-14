// Alternar Visibilidad de Contraseñas con Bootstrap Icons
function alternarVisibilidadPass(boton) {
    const inputGroup = boton.closest('.input-group');
    const input = inputGroup.querySelector('input');
    const icono = boton.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icono.classList.remove('bi-eye');
        icono.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icono.classList.remove('bi-eye-slash');
        icono.classList.add('bi-eye');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // 1. Animación de Maye tapándose los ojos al enfocar campos de contraseña
    const mascotWrapper = document.getElementById('mascotWrapper');
    const inputsPassword = document.querySelectorAll('input[type="password"]');

    inputsPassword.forEach(input => {
        input.addEventListener('focus', () => {
            if (mascotWrapper) mascotWrapper.classList.add('covering');
        });
        input.addEventListener('blur', () => {
            if (mascotWrapper) mascotWrapper.classList.remove('covering');
        });
    });

    // 2. Filtros de Servicios (Manicure, Pedicure, Peluquería)
    const filterButtons = document.querySelectorAll('.filter-btn');
    const serviceCards = document.querySelectorAll('.service-card');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filterValue = btn.getAttribute('data-filter');
            serviceCards.forEach(card => {
                if (filterValue === 'todos' || card.getAttribute('data-category') === filterValue) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const navCollapse = document.getElementById('navMayeMenu');
    const iconoMenu = document.getElementById('iconoHamburguesa');

    if (navCollapse && iconoMenu) {
        navCollapse.addEventListener('show.bs.collapse', () => {
            iconoMenu.classList.remove('bi-list');
            iconoMenu.classList.add('bi-x-lg');
        });

        navCollapse.addEventListener('hide.bs.collapse', () => {
            iconoMenu.classList.remove('bi-x-lg');
            iconoMenu.classList.add('bi-list');
        });

        // Cerrar automáticamente al hacer clic en cualquier enlace en móviles
        navCollapse.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            });
        });
    }
});
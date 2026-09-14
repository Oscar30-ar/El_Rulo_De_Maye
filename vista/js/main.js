// Función global para mostrar y ocultar contraseña con icono Bootstrap
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
    // 2. Animación de Maye tapándose los ojos al enfocar inputs de contraseña
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
});
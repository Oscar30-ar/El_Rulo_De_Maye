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


// Función nativa para mostrar y ocultar los horarios de Maye
function alternarHorarioMaye() {
    const seccion = document.getElementById('seccionHorariosMaye');
    const texto = document.getElementById('textoToggleHorario');
    const icono = document.getElementById('iconoToggleHorario');

    if (!seccion) return;

    if (seccion.style.display === 'none') {
        seccion.style.display = 'block';
        if (texto) texto.textContent = 'Ocultar Horarios';
        if (icono) {
            icono.classList.remove('bi-eye');
            icono.classList.add('bi-eye-slash');
        }
    } else {
        seccion.style.display = 'none';
        if (texto) texto.textContent = 'Ver Horarios';
        if (icono) {
            icono.classList.remove('bi-eye-slash');
            icono.classList.add('bi-eye');
        }
    }
}

// ========================================================
// CONTROLADOR UNIVERSAL DE TABLAS: PAGINACIÓN Y FILTRADO
// ========================================================
class TablaPaginada {
    constructor({ tablaId, paginacionId, buscadorId, filasPorPagina = 5 }) {
        this.tabla = document.getElementById(tablaId);
        this.paginacionContainer = document.getElementById(paginacionId);
        this.buscador = document.getElementById(buscadorId);
        this.filasPorPagina = filasPorPagina;
        this.paginaActual = 1;

        if (!this.tabla || !this.paginacionContainer) return;

        this.tbody = this.tabla.querySelector('tbody');
        this.filasOriginales = Array.from(this.tbody.querySelectorAll('tr'));
        this.filasFiltradas = [...this.filasOriginales];

        this.inicializar();
    }

    inicializar() {
        if (this.buscador) {
            this.buscador.addEventListener('input', (e) => {
                const termino = e.target.value.toLowerCase().trim();
                this.filtrar(termino);
            });
        }
        this.renderizar();
    }

    filtrar(termino) {
        this.filasFiltradas = this.filasOriginales.filter(fila => {
            const textoFila = fila.textContent.toLowerCase();
            return textoFila.includes(termino);
        });
        this.paginaActual = 1;
        this.renderizar();
    }

    cambiarPagina(nuevaPagina) {
        this.paginaActual = nuevaPagina;
        this.renderizar();
    }

    renderizar() {
        const totalFilas = this.filasFiltradas.length;
        const totalPaginas = Math.ceil(totalFilas / this.filasPorPagina) || 1;

        if (this.paginaActual > totalPaginas) {
            this.paginaActual = totalPaginas;
        }

        const inicio = (this.paginaActual - 1) * this.filasPorPagina;
        const fin = inicio + this.filasPorPagina;

        // Ocultar todas las filas originales primero
        this.filasOriginales.forEach(f => f.style.display = 'none');

        // Mostrar solo las filas de la página actual
        const filasPagina = this.filasFiltradas.slice(inicio, fin);
        filasPagina.forEach(f => f.style.display = '');

        // Generar botones de paginación Bootstrap
        this.generarBotonesPaginacion(totalPaginas, totalFilas);
    }

    generarBotonesPaginacion(totalPaginas, totalFilas) {
        this.paginacionContainer.innerHTML = '';

        if (totalFilas === 0) {
            this.paginacionContainer.innerHTML = '<span class="text-muted small">No se encontraron resultados coincidentes.</span>';
            return;
        }

        const nav = document.createElement('ul');
        nav.className = 'pagination pagination-sm mb-0 align-items-center gap-1';

        // Botón Anterior
        const btnPrev = document.createElement('li');
        btnPrev.className = `page-item ${this.paginaActual === 1 ? 'disabled' : ''}`;
        btnPrev.innerHTML = `<a class="page-link rounded-circle d-flex align-items-center justify-content-center" href="javascript:void(0)" style="width:32px; height:32px;"><i class="bi bi-chevron-left"></i></a>`;
        if (this.paginaActual > 1) {
            btnPrev.addEventListener('click', () => this.cambiarPagina(this.paginaActual - 1));
        }
        nav.appendChild(btnPrev);

        // Botones de Páginas Numéricas
        for (let i = 1; i <= totalPaginas; i++) {
            const pageItem = document.createElement('li');
            const isActive = i === this.paginaActual;
            pageItem.className = `page-item ${isActive ? 'active' : ''}`;
            pageItem.innerHTML = `<a class="page-link rounded-circle d-flex align-items-center justify-content-center ${isActive ? 'bg-primary text-white border-0' : ''}" href="javascript:void(0)" style="width:32px; height:32px; ${isActive ? 'background-color: var(--primary) !important;' : ''}">${i}</a>`;
            pageItem.addEventListener('click', () => this.cambiarPagina(i));
            nav.appendChild(pageItem);
        }

        // Botón Siguiente
        const btnNext = document.createElement('li');
        btnNext.className = `page-item ${this.paginaActual === totalPaginas ? 'disabled' : ''}`;
        btnNext.innerHTML = `<a class="page-link rounded-circle d-flex align-items-center justify-content-center" href="javascript:void(0)" style="width:32px; height:32px;"><i class="bi bi-chevron-right"></i></a>`;
        if (this.paginaActual < totalPaginas) {
            btnNext.addEventListener('click', () => this.cambiarPagina(this.paginaActual + 1));
        }
        nav.appendChild(btnNext);

        this.paginacionContainer.appendChild(nav);
    }
}

// Inicializar tablas si existen en la vista cargada
document.addEventListener('DOMContentLoaded', () => {
    // Tabla del Panel de Maye (admin.php)
    if (document.getElementById('tablaCitasAdmin')) {
        new TablaPaginada({
            tablaId: 'tablaCitasAdmin',
            paginacionId: 'paginacionAdmin',
            buscadorId: 'buscadorAdmin',
            filasPorPagina: 6
        });
    }

    // Tabla de Citas del Cliente (citas.php)
    if (document.getElementById('tablaMisCitas')) {
        new TablaPaginada({
            tablaId: 'tablaMisCitas',
            paginacionId: 'paginacionCliente',
            buscadorId: 'buscadorCliente',
            filasPorPagina: 4
        });
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const btnHamburguesa = document.getElementById('btnHamburguesaNavbar');
    const navMenu = document.getElementById('navMayeMenu');
    const icono = document.getElementById('iconoHamburguesa');

    if (btnHamburguesa && navMenu) {
        // Al abrirse el menú por completo
        navMenu.addEventListener('show.bs.collapse', () => {
            if (icono) {
                icono.classList.remove('bi-list');
                icono.classList.add('bi-x-lg');
            }
        });

        // Al cerrarse el menú por completo
        navMenu.addEventListener('hide.bs.collapse', () => {
            if (icono) {
                icono.classList.remove('bi-x-lg');
                icono.classList.add('bi-list');
            }
        });

        // Forzar clic nativo si Bootstrap llega a colisionar
        btnHamburguesa.addEventListener('click', (e) => {
            if (navMenu.classList.contains('show')) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navMenu);
                    if (bsCollapse) bsCollapse.hide();
                } else {
                    navMenu.classList.remove('show');
                }
            }
        });
    }
});
// Control manual directo del menú móvil
function toggleMenuMaye() {
    const navMenu = document.getElementById('navMayeMenu');
    const icono = document.getElementById('iconoHamburguesa');

    if (!navMenu) return;

    // Si está abierto, lo cerramos
    if (navMenu.classList.contains('show')) {
        navMenu.classList.remove('show');
        if (icono) {
            icono.className = 'bi bi-list fs-1 color-primary';
        }
    } else {
        // Si está cerrado, lo abrimos
        navMenu.classList.add('show');
        if (icono) {
            icono.className = 'bi bi-x-lg fs-1 color-primary';
        }
    }
}

function filtrarGaleriaCards(idCategoria, btn) {
    // 1. Actualizar estado visual del botón activo
    const botones = document.querySelectorAll('#filtrosGaleriaNav .btn-category-pill');
    botones.forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    // 2. Filtrar elementos de la cuadrícula
    const items = document.querySelectorAll('#contenedorGaleria .item-galeria-card');
    
    items.forEach(item => {
        const catItem = item.getAttribute('data-categoria');
        
        if (idCategoria === 'todas' || catItem === String(idCategoria)) {
            item.classList.remove('d-none'); // Muestra la columna respetando Bootstrap
        } else {
            item.classList.add('d-none');    // Oculta completamente con Bootstrap
        }
    });

    // 3. Recalcular ancho de las imágenes para que no se deforme el slider
    if (typeof sincronizarAnchoImagenesGaleria === 'function') {
        sincronizarAnchoImagenesGaleria();
    }
}
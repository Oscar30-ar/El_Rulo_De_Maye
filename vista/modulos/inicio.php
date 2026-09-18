<?php
// Consultas protegidas y carga de historias activas de 24 horas
try {
    $trabajosGaleria   = method_exists('UsuarioModelo', 'mdlListarGaleriaConCategoria') ? UsuarioModelo::mdlListarGaleriaConCategoria() : [];
    $categoriasGaleria = method_exists('UsuarioModelo', 'mdlListarCategorias') ? UsuarioModelo::mdlListarCategorias() : [];
    $resenasAprobadas  = method_exists('UsuarioModelo', 'mdlListarResenasAprobadas') ? UsuarioModelo::mdlListarResenasAprobadas() : [];
    $historiasActivas  = method_exists('UsuarioModelo', 'mdlListarHistoriasActivas') ? UsuarioModelo::mdlListarHistoriasActivas() : [];
} catch (Exception $e) {
    $trabajosGaleria = [];
    $categoriasGaleria = [];
    $resenasAprobadas = [];
    $historiasActivas = [];
}

$hayHistorias = !empty($historiasActivas);
?>

<!-- ========================================================
     1. HERO PRINCIPAL (ALINEADO, SIMÉTRICO Y RESPONSIVE)
============================================================ -->
<section class="py-4 py-lg-5 position-relative overflow-hidden">
    <div class="container px-4 px-lg-5">
        <div class="row align-items-center justify-content-between g-4 g-lg-5">

            <!-- Columna Izquierda: Mensaje y Cartel de Evolución -->
            <div class="col-12 col-lg-7 text-center text-lg-start">

                <!-- CARTEL NUESTRA EVOLUCIÓN (GRANDE Y PROTAGONISTA) -->
                <div class="rebranding-wrapper-xl mx-auto mx-lg-0 mb-4">
                    <div class="rebranding-pill-tag">
                        <i class="bi bi-stars text-warning me-1"></i> NUESTRA EVOLUCIÓN
                    </div>
                    <div class="brand-card-3d-xl">
                        <div class="brand-card-inner">
                            <!-- Frente: Logo e Identidad Antigua (Uñas Maye) -->
                            <div class="brand-face brand-face-front">
                                <img src="vista/img/logoMayeOld.jpeg" alt="Uñas Maye Logo Antiguo" class="brand-logo-img">
                                <div class="text-end">
                                    <span class="old-brand-title d-block">Uñas Maye</span>
                                    <small class="text-muted fw-semibold" style="font-size: 0.85rem;">Nuestra Primera Etapa</small>
                                </div>
                            </div>
                            <!-- Reverso: Logo Oficial Nuevo (El Rulo De Maye) -->
                            <div class="brand-face brand-face-back">
                                <img src="vista/img/logo_maye.jpeg" alt="El Rulo De Maye Logo Nuevo" class="brand-logo-img">
                                <div class="text-end">
                                    <span class="new-brand-title d-block">El Rulo De Maye</span>
                                    <small class="color-primary fw-bold" style="font-size: 0.85rem;">¡Misma pasión y dedicación! 🌸</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Título y Texto -->
                <h1 class="font-playfair fw-bold text-dark mb-3 hero-title-responsive">
                    Arte, delicadeza y <span class="color-primary fst-italic">cuidado exclusivo</span>
                </h1>
                <p class="text-secondary mb-4 mx-auto mx-lg-0 hero-desc-responsive lh-base">
                    Especialistas en embellecimiento, salud y reconstrucción de uñas. Descubre nuestras técnicas de esmaltado tradicional, semipermanente, nivelación Rubber, Capping y extensiones esculturales en Poligel, Acrílico y Soft Gel.
                </p>

                <!-- Botón de Catálogo -->
                <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                    <a href="index.php?ruta=servicios" class="btn-primary-custom px-4 py-3 shadow">
                        <i class="bi bi-calendar2-heart me-2"></i> Catálogo de Servicios
                    </a>
                </div>
            </div>

            <!-- Columna Derecha: Tarjeta Visual con Historias Integradas -->
            <div class="col-12 col-lg-5 d-flex justify-content-center">
                <div class="hero-logo-card-pro w-100 position-relative shadow-sm p-4 text-center">

                    <!-- Badge Superior de Calidad -->
                    <div class="hero-badge-floating">
                        <i class="bi bi-patch-check-fill text-warning me-1"></i> Calidad Garantizada
                    </div>

                    <div class="d-flex flex-column align-items-center justify-content-center h-100 pt-3">

                        <!-- Logo con aro Instagram si hay historias activas -->
                        <div class="position-relative mb-3">
                            <?php if ($hayHistorias): ?>
                                <div class="story-avatar-wrapper" onclick="abrirVisorHistorias()" title="¡Toca aquí para ver la historia de hoy!">
                                    <div class="story-gradient-ring">
                                        <img src="vista/img/logo_maye.jpeg" alt="El Rulo De Maye" class="hero-center-logo rounded-circle">
                                    </div>
                                    <span class="story-live-pill">
                                        <i class="bi bi-play-fill"></i> HISTORIA
                                    </span>
                                </div>
                            <?php else: ?>
                                <img src="vista/img/logo_maye.jpeg" alt="El Rulo De Maye" class="hero-center-logo rounded-circle shadow-xs">
                            <?php endif; ?>
                        </div>

                        <!-- Título limpio de marca -->
                        <h3 class="font-playfair fw-bold color-primary-dark mb-1">El Rulo De Maye</h3>
                        <p class="text-secondary small mb-2">Salón de Belleza & Nail Spa</p>

                        <!-- Banner interactivo que invita a ver la historia para que las clientas no se la salten -->
                        <?php if ($hayHistorias): ?>
                            <div class="badge-aviso-historia mb-3 shadow-xs" onclick="abrirVisorHistorias()">
                                <i class="bi bi-stars text-danger me-1"></i> ¡Maye subió un nuevo trabajo hoy! <strong>Ver historia</strong>
                            </div>
                        <?php endif; ?>

                        <!-- Calificación 5 Estrellas -->
                        <div class="d-flex align-items-center gap-1 text-warning small bg-white px-3 py-1 rounded-pill shadow-xs border">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="text-dark fw-bold ms-1" style="font-size: 0.8rem;">5.0 / 5.0</span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ========================================================
     VISOR MODAL TIPO INSTAGRAM STORIES (FULLSCREEN + PAUSA)
============================================================ -->
<?php if ($hayHistorias): ?>
    <div id="modalStoriesInstagram" class="stories-overlay d-none">
        <div class="stories-phone-wrapper" id="storyPhoneWrapper">

            <!-- Barras de Progreso Superior -->
            <div class="stories-progress-bar-container" id="storiesBars">
                <?php foreach ($historiasActivas as $idx => $st): ?>
                    <div class="story-progress-segment">
                        <div class="story-progress-fill" id="storyFill_<?= $idx ?>"></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Cabecera de la Historia -->
            <div class="story-header-user">
                <img src="vista/img/logo_maye.jpeg" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                <div class="text-white text-start">
                    <div class="fw-bold small lh-1">El Rulo De Maye</div>
                    <small class="text-white-50" style="font-size: 0.7rem;">Historia de hoy 💅</small>
                </div>
                <button type="button" class="btn text-white ms-auto fs-3 p-0 lh-1" onclick="cerrarVisorHistorias()">&times;</button>
            </div>

            <!-- Contenedor del Medio Multimedia (Foto o Video) -->
            <div class="story-media-box" id="storyMediaBox">
                <img src="" id="storyMainImage" class="story-media-element" style="display: none;">
                <video src="" id="storyMainVideo" class="story-media-element" style="display: none;" playsinline muted></video>

                <!-- Indicador visual cuando está en pausa -->
                <div id="indicadorPausaStory" class="story-pause-indicator d-none">
                    <i class="bi bi-pause-fill"></i> Pausa
                </div>

                <!-- Pie de foto -->
                <div class="story-caption" id="storyCaptionText"></div>
            </div>

            <!-- Zonas táctiles para retroceder / avanzar con clic simple -->
            <div class="story-touch-zone touch-left" onclick="historiaAnterior()"></div>
            <div class="story-touch-zone touch-right" onclick="historiaSiguiente()"></div>

            <!-- Barra Inferior de Reacciones Rápidas -->
            <div class="story-reactions-footer">
                <button type="button" class="btn-story-reaction" onclick="enviarReaccion('💖')">💖</button>
                <button type="button" class="btn-story-reaction" onclick="enviarReaccion('💅')">💅</button>
                <button type="button" class="btn-story-reaction" onclick="enviarReaccion('🔥')">🔥</button>
                <button type="button" class="btn-story-reaction" onclick="enviarReaccion('😍')">😍</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ========================================================
     2. GALERÍA ANTES Y DESPUÉS (CON FILTROS Y ESTADOS VACÍOS)
============================================================ -->
<?php if (!empty($trabajosGaleria)): ?>
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container px-4 px-lg-5">

            <!-- Encabezado de la Sección -->
            <div class="text-center mx-auto mb-4" style="max-width: 650px;">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-2 small fw-bold text-uppercase">
                    Experiencia & Resultados
                </span>
                <h2 class="font-playfair fw-bold text-dark mb-2">Transformaciones Reales</h2>
                <p class="text-muted small mb-0">Desliza la barra central para comparar el estado inicial con el acabado profesional de Maye.</p>
            </div>

            <!-- Filtros por Categoría -->
            <div class="d-flex justify-content-center align-items-center gap-2 mb-4 flex-wrap" id="filtrosGaleriaNav">
                <button type="button"
                    class="btn btn-sm btn-category-pill active"
                    data-estado="activo"
                    data-cat-nombre="Todos los Trabajos"
                    onclick="filtrarGaleria('todas', 'activo', this)">
                    Todos los Trabajos (<?= count($trabajosGaleria) ?>)
                </button>

                <?php foreach ($categoriasGaleria as $cGal):
                    $esProximo = ($cGal['estado'] === 'proximamente');
                ?>
                    <button type="button"
                        class="btn btn-sm btn-category-pill <?= $esProximo ? 'btn-category-proximo' : '' ?>"
                        data-cat-nombre="<?= htmlspecialchars($cGal['nombre'], ENT_QUOTES) ?>"
                        data-estado="<?= $cGal['estado'] ?>"
                        onclick="filtrarGaleria('<?= $cGal['id'] ?>', '<?= $cGal['estado'] ?>', this)">
                        <?= htmlspecialchars($cGal['nombre']) ?>
                        <?php if ($esProximo): ?>
                            <span class="badge-proximo-tag ms-1">Próximamente</span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Contenedor Principal de la Grilla -->
            <div class="row g-4 justify-content-center" id="contenedorGaleria">
                <?php foreach ($trabajosGaleria as $g): ?>
                    <div class="col-12 col-md-6 col-lg-4 item-galeria-card" data-categoria="<?= !empty($g['categoria_id']) ? $g['categoria_id'] : '0' ?>">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden gallery-card-custom">

                            <!-- Comparador Antes (Izq) / Después (Der) -->
                            <div class="ba-wrapper position-relative w-100" id="ba_box_<?= $g['id'] ?>">

                                <!-- Foto DESPUÉS (Base a la derecha) -->
                                <img src="<?= htmlspecialchars($g['foto_despues']) ?>" class="ba-layer-img" alt="Después">
                                <span class="ba-badge-tag tag-after">Después</span>

                                <!-- Foto ANTES (Recortada desde la izquierda) -->
                                <div class="ba-clip-layer" id="clip_<?= $g['id'] ?>">
                                    <img src="<?= htmlspecialchars($g['foto_antes']) ?>" class="ba-layer-img" alt="Antes">
                                    <span class="ba-badge-tag tag-before">Antes</span>
                                </div>

                                <!-- Manija Central -->
                                <div class="ba-line-handle" id="handle_<?= $g['id'] ?>">
                                    <div class="ba-circle-handle">
                                        <i class="bi bi-chevron-left"></i>
                                        <i class="bi bi-chevron-right"></i>
                                    </div>
                                </div>

                                <!-- Slider -->
                                <input type="range" min="0" max="100" value="50" class="ba-slider-input" oninput="moverComparador('<?= $g['id'] ?>', this.value)">
                            </div>

                            <!-- Información de la Tarjeta -->
                            <div class="p-3 p-md-4 d-flex flex-column justify-content-between flex-grow-1 bg-white">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small">
                                            <?= htmlspecialchars($g['categoria'] ?: 'Especialidad') ?>
                                        </span>
                                        <small class="text-muted"><i class="bi bi-check2-circle text-success me-1"></i>Realizado por Maye</small>
                                    </div>
                                    <h5 class="font-playfair fw-bold text-dark mb-1"><?= htmlspecialchars($g['titulo']) ?></h5>
                                    <p class="text-secondary small mb-3 lh-sm"><?= htmlspecialchars($g['descripcion'] ?: 'Técnica profesional garantizada.') ?></p>
                                </div>
                                <div class="pt-2 border-top mt-auto">
                                    <a href="index.php?ruta=citas" class="btn btn-sm btn-outline-primary rounded-pill w-100 py-2 fw-semibold">
                                        Quiero este resultado <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ESTADO VACÍO 1: CATEGORÍA EN "PRÓXIMAMENTE" -->
            <div id="estadoProximamenteGaleria" class="d-none text-center py-5 mx-auto" style="max-width: 500px;">
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5" style="background: linear-gradient(145deg, #fff7f8, #ffffff); border: 1.5px dashed #d48b94 !important;">
                    <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center bg-white shadow-xs" style="width: 70px; height: 70px; border: 1px solid #ebd3d6;">
                        <i class="bi bi-stars color-primary" style="font-size: 2rem;"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 mb-2 small fw-bold text-uppercase mx-auto">
                        En Preparación
                    </span>
                    <h4 class="font-playfair fw-bold text-dark mb-2" id="txtNombreCatProximo">Especialidad</h4>
                    <p class="text-secondary small mb-4 lh-base">
                        Maye está perfeccionando y preparando nuevos diseños y técnicas exclusivas para esta categoría. ¡Muy pronto habilitaremos las citas y la galería de fotos!
                    </p>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-4 py-2" onclick="filtrarGaleria('todas', 'activo', document.querySelector('#filtrosGaleriaNav button'))">
                        <i class="bi bi-arrow-left me-1"></i> Ver trabajos disponibles
                    </button>
                </div>
            </div>

            <!-- ESTADO VACÍO 2: CATEGORÍA ACTIVA PERO SIN FOTOS -->
            <div id="estadoSinFotosGaleria" class="d-none text-center py-5 mx-auto" style="max-width: 500px;">
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white" style="border: 1px solid #f6dfe2 !important;">
                    <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: #fff0f3;">
                        <i class="bi bi-camera color-primary-dark" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="font-playfair fw-bold text-dark mb-2">¡Pronto nuevas fotos!</h4>
                    <p class="text-secondary small mb-4 lh-base">
                        Maye aún no ha subido transformaciones para <strong id="txtNombreCatSinFotos">esta categoría</strong>, pero el servicio ya está disponible en el salón.
                    </p>
                    <a href="index.php?ruta=citas" class="btn-primary-custom px-4 py-2 text-decoration-none shadow-sm">
                        <i class="bi bi-calendar2-heart me-1"></i> Sé la primera en agendar
                    </a>
                </div>
            </div>

        </div>
    </section>
<?php endif; ?>

<!-- ========================================================
     3. TESTIMONIOS Y RESEÑAS
============================================================ -->
<?php if (!empty($resenasAprobadas)): ?>
    <section class="py-5 bg-white">
        <div class="container px-4 px-lg-5">
            <div class="text-center mx-auto mb-4" style="max-width: 650px;">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 mb-2 small fw-bold text-uppercase">
                    Testimonios
                </span>
                <h2 class="font-playfair fw-bold text-dark mb-2">Lo que dicen nuestras clientas</h2>
                <p class="text-muted small mb-0">Experiencias reales de quienes confían su belleza en El Rulo De Maye.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <?php foreach ($resenasAprobadas as $r): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-between" style="background: #fdfaf9;">
                            <div>
                                <div class="d-flex gap-1 text-warning mb-3 fs-6">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?= ($i <= $r['calificacion']) ? 'bi-star-fill' : 'bi-star text-muted opacity-25' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-secondary small fst-italic lh-base mb-4">
                                    "<?= htmlspecialchars($r['comentario']) ?>"
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-3 pt-3 border-top">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm border" style="width: 42px; height: 42px;">
                                    <i class="bi bi-person-fill color-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark small"><?= htmlspecialchars($r['nombre']) ?></h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Clienta Verificada</small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- ========================================================
     SECCIÓN: PREGUNTAS FRECUENTES (FAQ BOUTIQUE)
============================================================ -->
<section class="py-5 bg-white border-top">
    <div class="container px-4 px-lg-5">

        <!-- Encabezado de la Sección -->
        <div class="text-center mx-auto mb-5" style="max-width: 650px;">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-2 small fw-bold text-uppercase">
                <i class="bi bi-question-circle me-1"></i> Resolvemos tus dudas
            </span>
            <h2 class="font-playfair fw-bold text-dark mb-2">Preguntas Frecuentes</h2>
            <p class="text-secondary small mb-0">
                Todo lo que necesitas saber sobre nuestras técnicas, cuidados, agendamiento y políticas de atención.
            </p>
        </div>

        <!-- Contenedor del Acordeón -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="accordion accordion-custom" id="faqMayeAccordion">

                    <!-- Pregunta 1 -->
                    <div class="accordion-item mb-3 rounded-4 overflow-hidden border shadow-xs">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="bi bi-clock-history color-primary me-2 fs-5"></i>
                                ¿Cuánto tiempo dura en promedio una cita y qué pasa si llego tarde?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqMayeAccordion">
                            <div class="accordion-body text-secondary small lh-base pt-0">
                                La duración depende de la técnica seleccionada: un esmaltado semipermanente toma aproximadamente entre <strong>60 y 80 minutos</strong>, mientras que extensiones esculturales o nivelación Rubber pueden tomar de <strong>90 a 120 minutos</strong>. Manejamos una tolerancia máxima de <strong>15 minutos</strong> de espera para garantizar que la siguiente clienta sea atendida a tiempo.
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 2 -->
                    <div class="accordion-item mb-3 rounded-4 overflow-hidden border shadow-xs">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="bi bi-gem color-primary me-2 fs-5"></i>
                                ¿Cuál es la diferencia entre Semipermanente, Rubber y Poligel?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqMayeAccordion">
                            <div class="accordion-body text-secondary small lh-base pt-0">
                                <strong>Semipermanente:</strong> Es un esmaltado de alta duración curado en lámpara (hasta 21 días) sobre tu uña natural.<br>
                                <strong>Rubber Base / Kapping:</strong> Aporta grosor, nivelación y resistencia a uñas débiles o quebradizas para que crezcan sanas sin romperse.<br>
                                <strong>Poligel / Acrílico:</strong> Sistema de extensión y escultura para alargar la uña a la forma y longitud que desees (almendra, cuadrada, ballerina, etc.).
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 3 -->
                    <div class="accordion-item mb-3 rounded-4 overflow-hidden border shadow-xs">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="bi bi-camera-fill color-primary me-2 fs-5"></i>
                                ¿Puedo llevar mi propio diseño o foto de Pinterest/Instagram?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqMayeAccordion">
                            <div class="accordion-body text-secondary small lh-base pt-0">
                                <strong>¡Totalmente bienvenida!</strong> De hecho, al momento de agendar tu cita en nuestra plataforma puedes adjuntar la <strong>Foto de Referencia</strong>. Así Maye prepara con antelación los esmaltes, cristales o accesorios necesarios para recrearlo exactamente a tu gusto.
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 4 -->
                    <div class="accordion-item mb-3 rounded-4 overflow-hidden border shadow-xs">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <i class="bi bi-wallet2 color-primary me-2 fs-5"></i>
                                ¿Qué métodos de pago reciben en el salón?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqMayeAccordion">
                            <div class="accordion-body text-secondary small lh-base pt-0">
                                Aceptamos pagos en <strong>efectivo</strong> directamente en el salón, además de transferencias bancarias inmediatas a través de <strong>Nequi, Daviplata, Dale! y transferencias bancarias</strong>. Al momento de reservar tu cita puedes marcar tu preferencia o registrar tu comprobante.
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 5 -->
                    <div class="accordion-item mb-3 rounded-4 overflow-hidden border shadow-xs">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <i class="bi bi-shield-check color-primary me-2 fs-5"></i>
                                ¿Cómo garantizan la bioseguridad y esterilización de herramientas?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqMayeAccordion">
                            <div class="accordion-body text-secondary small lh-base pt-0">
                                Tu salud y seguridad son prioridad absoluta. Todas las herramientas metálicas (corta cutículas, repujadores, fresas del torno) pasan por un proceso riguroso de lavado, desinfección de grado hospitalario y esterilización. Las limas y esponjas descartables son de uso exclusivo por clienta.
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 6 -->
                    <div class="accordion-item mb-3 rounded-4 overflow-hidden border shadow-xs">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                <i class="bi bi-gift color-primary me-2 fs-5"></i>
                                ¿Cómo funciona el beneficio del Club Maye Lover?
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqMayeAccordion">
                            <div class="accordion-body text-secondary small lh-base pt-0">
                                ¡Premiamos tu fidelidad! Cada cita que finalices acumula un punto en tu barra de progreso en tu perfil. Al completar la meta de visitas activas ganas un <strong>descuento especial o sesión de exfoliación spa de cortesía</strong> en tu siguiente turno.
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>

<!-- ========================================================
     ESTILOS CSS: ALINEACIÓN, HISTORIAS Y COMPARADOR
============================================================ -->
<style>
    .hero-title-responsive {
        font-size: clamp(2.1rem, 4.2vw, 3.4rem);
        line-height: 1.18;
    }

    .hero-desc-responsive {
        font-size: clamp(0.95rem, 1.4vw, 1.08rem);
        max-width: 580px;
    }

    /* Cartel "Nuestra Evolución" XL */
    .rebranding-wrapper-xl {
        width: 100%;
        max-width: 460px;
    }

    .rebranding-pill-tag {
        display: inline-flex;
        align-items: center;
        background: #fdebee;
        color: var(--primary-dark, #b86b75);
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 5px 14px;
        border-radius: 20px;
        margin-bottom: 8px;
        border: 1px solid #f6cfd5;
    }

    .brand-card-3d-xl {
        width: 100%;
        height: 95px;
        perspective: 1000px;
    }

    .brand-card-3d-xl .brand-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.9s cubic-bezier(0.4, 0.2, 0.2, 1);
        transform-style: preserve-3d;
        animation: flipCardEvolution 6.5s infinite ease-in-out;
    }

    @keyframes flipCardEvolution {

        0%,
        42% {
            transform: rotateY(0deg);
        }

        50%,
        92% {
            transform: rotateY(180deg);
        }

        100% {
            transform: rotateY(0deg);
        }
    }

    .brand-card-3d-xl .brand-face {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 24px;
        background: #ffffff;
        box-shadow: 0 10px 25px rgba(212, 139, 148, 0.16);
        border: 1.5px solid rgba(212, 139, 148, 0.22);
    }

    .brand-face-front {
        border-left: 6px solid #b8a6a8;
    }

    .brand-face-back {
        background: linear-gradient(135deg, #fff7f8, #ffffff);
        border-left: 6px solid var(--primary, #d48b94);
        transform: rotateY(180deg);
    }

    .brand-logo-img {
        height: 60px;
        max-width: 100px;
        object-fit: contain;
    }

    .old-brand-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: #7b7375;
        text-decoration: line-through;
    }

    .new-brand-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary-dark, #b86b75);
    }

    /* Tarjeta Visual Derecha */
    .hero-logo-card-pro {
        max-width: 380px;
        background: linear-gradient(145deg, #ffffff, #fff3f5);
        border-radius: 35px;
        border: 2px solid #f6dfe2;
        box-shadow: 0 15px 35px rgba(212, 139, 148, 0.18);
    }

    .hero-center-logo {
        width: 130px;
        height: 130px;
        object-fit: cover;
        background: #ffffff;
        display: block;
    }

    .hero-badge-floating {
        position: absolute;
        top: -12px;
        left: 20px;
        background: #ffffff;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.82rem;
        font-weight: 700;
        border: 1px solid #ebd3d6;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Aro Degradado Animado alrededor del logo */
    .story-avatar-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
        padding: 4px;
        transition: transform 0.25s ease;
    }

    .story-avatar-wrapper:hover {
        transform: scale(1.05);
    }

    .story-gradient-ring {
        padding: 4px;
        background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        border-radius: 50%;
        box-shadow: 0 4px 16px rgba(220, 39, 67, 0.35);
        animation: rotateGradient 4s linear infinite;
    }

    @keyframes rotateGradient {
        0% {
            filter: hue-rotate(0deg);
        }

        100% {
            filter: hue-rotate(360deg);
        }
    }

    .story-live-pill {
        position: absolute;
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%);
        background: #e1306c;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.8px;
        padding: 2px 10px;
        border-radius: 20px;
        border: 2px solid #ffffff;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
        white-space: nowrap;
    }

    .badge-aviso-historia {
        background: #fff0f3;
        color: #b86b75;
        border: 1.5px dashed #d48b94;
        border-radius: 20px;
        padding: 5px 16px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .badge-aviso-historia:hover {
        background: #ffe3e8;
        transform: translateY(-2px);
    }

    /* Modal Visor de Historias Fullscreen */
    .stories-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(10, 10, 10, 0.94);
        backdrop-filter: blur(10px);
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stories-phone-wrapper {
        position: relative;
        width: 100%;
        max-width: 410px;
        height: 90vh;
        max-height: 780px;
        background: #000000;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        display: flex;
        flex-direction: column;
        user-select: none;
        -webkit-user-select: none;
    }

    .stories-progress-bar-container {
        position: absolute;
        top: 12px;
        left: 12px;
        right: 12px;
        display: flex;
        gap: 5px;
        z-index: 25;
    }

    .story-progress-segment {
        flex: 1;
        height: 3px;
        background: rgba(255, 255, 255, 0.35);
        border-radius: 3px;
        overflow: hidden;
    }

    .story-progress-fill {
        width: 0%;
        height: 100%;
        background: #ffffff;
    }

    .story-header-user {
        position: absolute;
        top: 25px;
        left: 15px;
        right: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 25;
    }

    .story-media-box {
        width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #050505;
        cursor: pointer;
    }

    .story-media-element {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .story-pause-indicator {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.65);
        color: #ffffff;
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        backdrop-filter: blur(4px);
        pointer-events: none;
        z-index: 20;
    }

    .story-caption {
        position: absolute;
        bottom: 80px;
        left: 20px;
        right: 20px;
        color: #ffffff;
        font-size: 0.92rem;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.85);
        z-index: 15;
        text-align: center;
    }

    .story-touch-zone {
        position: absolute;
        top: 70px;
        bottom: 85px;
        width: 40%;
        z-index: 10;
    }

    .touch-left {
        left: 0;
    }

    .touch-right {
        right: 0;
    }

    .story-reactions-footer {
        position: absolute;
        bottom: 15px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 14px;
        z-index: 30;
    }

    .btn-story-reaction {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(6px);
        border-radius: 50%;
        width: 46px;
        height: 46px;
        font-size: 1.35rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.15s ease, background 0.15s;
    }

    .btn-story-reaction:hover {
        transform: scale(1.2);
        background: rgba(255, 255, 255, 0.45);
    }

    .floating-reaction-emoji {
        position: absolute;
        bottom: 85px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 3.2rem;
        z-index: 40;
        pointer-events: none;
        animation: floatUpAndFade 1.2s forwards ease-out;
    }

    @keyframes floatUpAndFade {
        0% {
            transform: translate(-50%, 0) scale(0.6);
            opacity: 1;
        }

        100% {
            transform: translate(-50%, -200px) scale(1.6);
            opacity: 0;
        }
    }

    /* Comparador de Galería */
    .gallery-card-custom {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(212, 139, 148, 0.16) !important;
    }

    .gallery-card-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(212, 139, 148, 0.22) !important;
    }

    .ba-wrapper {
        position: relative;
        width: 100%;
        height: 280px;
        overflow: hidden;
        background-color: #f7eded;
    }

    .ba-layer-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .ba-clip-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 50%;
        height: 100%;
        overflow: hidden;
        border-right: 2.5px solid #ffffff;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.25);
    }

    .ba-clip-layer .ba-layer-img {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        object-fit: cover;
    }

    .ba-line-handle {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 2px;
        background: #ffffff;
        pointer-events: none;
        z-index: 10;
        transform: translateX(-50%);
    }

    .ba-circle-handle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 34px;
        height: 34px;
        background: #ffffff;
        color: var(--primary-dark, #b86b75);
        border: 2px solid var(--primary, #d48b94);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        gap: 2px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    }

    .ba-slider-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: ew-resize;
        z-index: 20;
        margin: 0;
    }

    .ba-badge-tag {
        position: absolute;
        bottom: 12px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        pointer-events: none;
        z-index: 5;
        white-space: nowrap;
    }

    .tag-before {
        left: 12px;
        background: rgba(30, 30, 30, 0.75);
        color: #ffffff;
        backdrop-filter: blur(4px);
    }

    .tag-after {
        right: 12px;
        background: rgba(212, 139, 148, 0.95);
        color: #ffffff;
        backdrop-filter: blur(4px);
    }

    .btn-category-pill {
        background: #ffffff;
        border: 1px solid #ebd3d6;
        color: var(--text, #333);
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.25s;
    }

    .btn-category-pill:hover,
    .btn-category-pill.active {
        background: var(--primary, #d48b94);
        color: #ffffff;
        border-color: var(--primary, #d48b94);
        box-shadow: 0 4px 10px rgba(212, 139, 148, 0.3);
    }

    .btn-category-proximo {
        border-style: dashed !important;
        color: #8c8283 !important;
        background: #fdfbfb !important;
    }

    .badge-proximo-tag {
        font-size: 0.65rem;
        background: #ece7e8;
        color: #635b5c;
        padding: 2px 7px;
        border-radius: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    @media (max-width: 576px) {
        .hero-logo-card-pro {
            max-width: 290px;
            border-radius: 26px;
        }

        .hero-center-logo {
            width: 105px;
            height: 105px;
        }

        .brand-card-3d-xl {
            height: 85px;
        }

        .brand-logo-img {
            height: 48px;
            max-width: 80px;
        }

        .old-brand-title {
            font-size: 1.15rem;
        }

        .new-brand-title {
            font-size: 1.2rem;
        }

        .ba-wrapper {
            height: 240px;
        }
    }
</style>

<!-- ========================================================
     SCRIPTS: VISOR DE HISTORIAS CON PAUSA + COMPARADOR
============================================================ -->
<script>
    const historiasData = <?= json_encode($historiasActivas) ?>;
    let indexActual = 0;
    let storyTimer = null;
    const duracionStory = 6000; // 6 segundos por historia
    let tiempoInicio = 0;
    let tiempoRestante = duracionStory;
    let enPausa = false;

    function abrirVisorHistorias() {
        const modal = document.getElementById('modalStoriesInstagram');
        if (!modal || historiasData.length === 0) return;
        modal.classList.remove('d-none');
        cargarHistoria(0);
    }

    function cerrarVisorHistorias() {
        clearTimeout(storyTimer);
        const modal = document.getElementById('modalStoriesInstagram');
        if (modal) modal.classList.add('d-none');
        const vid = document.getElementById('storyMainVideo');
        if (vid) {
            vid.pause();
            vid.currentTime = 0;
        }
    }

    function cargarHistoria(idx) {
        if (idx < 0 || idx >= historiasData.length) {
            cerrarVisorHistorias();
            return;
        }
        clearTimeout(storyTimer);
        indexActual = idx;
        enPausa = false;
        tiempoRestante = duracionStory;

        // Actualizar estado de las barras
        for (let i = 0; i < historiasData.length; i++) {
            const fill = document.getElementById('storyFill_' + i);
            if (fill) {
                fill.style.transition = 'none';
                fill.style.width = (i < idx) ? '100%' : '0%';
            }
        }

        const current = historiasData[idx];
        const imgEl = document.getElementById('storyMainImage');
        const vidEl = document.getElementById('storyMainVideo');
        const capEl = document.getElementById('storyCaptionText');

        if (current.tipo === 'video') {
            if (imgEl) imgEl.style.display = 'none';
            if (vidEl) {
                vidEl.src = current.archivo;
                vidEl.style.display = 'block';
                vidEl.play().catch(() => {});
            }
        } else {
            if (vidEl) {
                vidEl.pause();
                vidEl.style.display = 'none';
            }
            if (imgEl) {
                imgEl.src = current.archivo;
                imgEl.style.display = 'block';
            }
        }

        if (capEl) capEl.textContent = current.pie_foto || '';

        iniciarTemporizador(duracionStory);
    }

    function iniciarTemporizador(duracion) {
        tiempoInicio = Date.now();
        const activeFill = document.getElementById('storyFill_' + indexActual);

        if (activeFill) {
            activeFill.style.transition = `width ${duracion}ms linear`;
            activeFill.style.width = '100%';
        }

        storyTimer = setTimeout(() => {
            historiaSiguiente();
        }, duracion);
    }

    // PAUSA AL MANTENER PRESIONADO (Click o Touch sostenido)
    function pausarHistoria() {
        if (enPausa) return;
        enPausa = true;
        clearTimeout(storyTimer);

        const tiempoTranscurrido = Date.now() - tiempoInicio;
        tiempoRestante = Math.max(200, tiempoRestante - tiempoTranscurrido);

        // Congelar barra visualmente
        const activeFill = document.getElementById('storyFill_' + indexActual);
        if (activeFill) {
            const anchoActual = window.getComputedStyle(activeFill).width;
            activeFill.style.transition = 'none';
            activeFill.style.width = anchoActual;
        }

        // Pausar video si aplica
        const vid = document.getElementById('storyMainVideo');
        if (vid && !vid.paused) vid.pause();

        const indicador = document.getElementById('indicadorPausaStory');
        if (indicador) indicador.classList.remove('d-none');
    }

    // REANUDAR AL SOLTAR
    function reanudarHistoria() {
        if (!enPausa) return;
        enPausa = false;

        const indicador = document.getElementById('indicadorPausaStory');
        if (indicador) indicador.classList.add('d-none');

        const vid = document.getElementById('storyMainVideo');
        if (vid && vid.style.display === 'block') vid.play().catch(() => {});

        iniciarTemporizador(tiempoRestante);
    }

    function historiaSiguiente() {
        if (indexActual + 1 < historiasData.length) {
            cargarHistoria(indexActual + 1);
        } else {
            cerrarVisorHistorias();
        }
    }

    function historiaAnterior() {
        if (indexActual - 1 >= 0) {
            cargarHistoria(indexActual - 1);
        }
    }

    function enviarReaccion(emoji) {
        const current = historiasData[indexActual];
        animarEmojiFlotante(emoji);

        const formData = new FormData();
        formData.append('historia_id', current.id);
        formData.append('emoji', emoji);

        fetch('ajax/reaccionar_historia.php', {
            method: 'POST',
            body: formData
        }).catch(() => {});
    }

    function animarEmojiFlotante(emoji) {
        const floatEl = document.createElement('div');
        floatEl.textContent = emoji;
        floatEl.className = 'floating-reaction-emoji';
        const phone = document.getElementById('storyPhoneWrapper');
        if (phone) phone.appendChild(floatEl);
        setTimeout(() => floatEl.remove(), 1200);
    }

    // Comparador y Filtros de Galería
    function sincronizarAnchoImagenesGaleria() {
        document.querySelectorAll('.ba-wrapper').forEach(wrapper => {
            const widthTotal = wrapper.offsetWidth;
            const imgInterna = wrapper.querySelector('.ba-clip-layer .ba-layer-img');
            if (imgInterna) {
                imgInterna.style.width = widthTotal + 'px';
                imgInterna.style.minWidth = widthTotal + 'px';
            }
        });
    }

    function moverComparador(id, valor) {
        const clip = document.getElementById('clip_' + id);
        const handle = document.getElementById('handle_' + id);
        if (clip) clip.style.width = valor + '%';
        if (handle) handle.style.left = valor + '%';
    }

    function filtrarGaleria(idCategoria, estadoCategoria, btn) {
        const botones = document.querySelectorAll('#filtrosGaleriaNav .btn-category-pill');
        botones.forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const contenedorGrilla = document.getElementById('contenedorGaleria');
        const cajaProximamente = document.getElementById('estadoProximamenteGaleria');
        const cajaSinFotos = document.getElementById('estadoSinFotosGaleria');
        const items = document.querySelectorAll('#contenedorGaleria .item-galeria-card');

        const nombreCategoria = btn ? (btn.getAttribute('data-cat-nombre') || btn.innerText.trim()) : '';

        if (cajaProximamente) cajaProximamente.classList.add('d-none');
        if (cajaSinFotos) cajaSinFotos.classList.add('d-none');
        if (contenedorGrilla) contenedorGrilla.classList.remove('d-none');

        if (estadoCategoria === 'proximamente') {
            items.forEach(item => item.classList.add('d-none'));
            if (contenedorGrilla) contenedorGrilla.classList.add('d-none');
            const txtProximo = document.getElementById('txtNombreCatProximo');
            if (txtProximo) txtProximo.textContent = nombreCategoria;
            if (cajaProximamente) cajaProximamente.classList.remove('d-none');
            return;
        }

        let visibles = 0;
        items.forEach(item => {
            const catItem = item.getAttribute('data-categoria');
            if (idCategoria === 'todas' || catItem === String(idCategoria)) {
                item.classList.remove('d-none');
                visibles++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (visibles === 0 && idCategoria !== 'todas') {
            if (contenedorGrilla) contenedorGrilla.classList.add('d-none');
            const txtSinFotos = document.getElementById('txtNombreCatSinFotos');
            if (txtSinFotos) txtSinFotos.textContent = nombreCategoria;
            if (cajaSinFotos) cajaSinFotos.classList.remove('d-none');
        } else {
            sincronizarAnchoImagenesGaleria();
        }
    }

    // Asignación de eventos de pausa y redimensionamiento
    document.addEventListener('DOMContentLoaded', () => {
        sincronizarAnchoImagenesGaleria();

        const mediaBox = document.getElementById('storyMediaBox');
        if (mediaBox) {
            // En PC: clic sostenido
            mediaBox.addEventListener('mousedown', pausarHistoria);
            mediaBox.addEventListener('mouseup', reanudarHistoria);
            mediaBox.addEventListener('mouseleave', reanudarHistoria);

            // En Móvil: toque sostenido
            mediaBox.addEventListener('touchstart', (e) => {
                pausarHistoria();
            }, {
                passive: true
            });
            mediaBox.addEventListener('touchend', reanudarHistoria);
        }
    });

    window.addEventListener('resize', sincronizarAnchoImagenesGaleria);
</script>
<?php
$servicios = UsuarioModelo::mdlObtenerServicios();
?>
<section class="section">
    <div style="text-align:center; margin-bottom: 30px;">
        <h1 style="font-family:'Playfair Display', serif; font-size:2.6rem;">Catálogo de Belleza</h1>
        <p style="color: var(--text-light);">Cuidado premium para tus manos, pies y estilo integral</p>
    </div>

    <!-- 3. Botones de Filtro por Categorías -->
    <div class="services-filter">
        <button class="filter-btn active" data-filter="todos">Todos los Servicios</button>
        <button class="filter-btn" data-filter="manicure">💅 Manicure</button>
        <button class="filter-btn" data-filter="pedicure">🦶 Pedicure</button>
        <button class="filter-btn" data-filter="peluqueria">💇‍♀️ Peluquería & Spa</button>
    </div>

    <div class="services-grid">
        <?php foreach ($servicios as $s): 
            $catSlug = 'peluqueria';
            if (stripos($s['categoria'], 'manicure') !== false) $catSlug = 'manicure';
            elseif (stripos($s['categoria'], 'pedicure') !== false) $catSlug = 'pedicure';
        ?>
            <div class="service-card" data-category="<?= $catSlug ?>">
                <div>
                    <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--primary-dark);">
                        <?= htmlspecialchars($s["categoria"]) ?>
                    </span>
                    <h3 style="margin: 10px 0;"><?= htmlspecialchars($s["nombre"]) ?></h3>
                    <p style="font-size:0.9rem; color:var(--text-light);"><?= htmlspecialchars($s["descripcion"] ?: 'Consulte a Maye') ?></p>
                </div>
                <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
                    <?php if ($s["categoria_estado"] === "activo"): ?>
                        <span style="font-weight:700; color:var(--primary-dark); font-size:1.15rem;">
                            $<?= number_format($s["precio"], 0, ',', '.') ?> COP
                        </span>
                        <a href="index.php?ruta=citas&servicio=<?= $s['id'] ?>" class="btn-primary" style="padding: 8px 18px; font-size: 0.85rem;">Agendar</a>
                    <?php else: ?>
                        <span style="color:#999; font-weight:600; font-size:0.85rem;">🌸 Próximamente</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
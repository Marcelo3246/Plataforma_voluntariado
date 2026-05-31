<?php
session_start();

// ── Leer mensajes flash y limpiarlos de la sesión ─────────────────────────────
$flash_error  = $_SESSION['flash_error']  ?? null;
$flash_exito  = $_SESSION['flash_exito']  ?? null;
$flash_panel  = $_SESSION['flash_panel']  ?? 'login'; // panel a mostrar al cargar
unset($_SESSION['flash_error'], $_SESSION['flash_exito'], $_SESSION['flash_panel']);

// ── Conexión para cargar ciudades ─────────────────────────────────────────────
try {
    $conexion = new PDO('mysql:host=localhost;dbname=plataforma_voluntariado', 'root', '');
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

$sql_ciudades      = 'SELECT id_ciudad, ciudad FROM ciudad ORDER BY ciudad ASC';
$resultados_ciudades = $conexion->query($sql_ciudades);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Voluntariado</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="login-page">
    <div class="page">
        <h1>Voluntariado</h1>
        <div class="tabs">
            <button type="button" class="tab-button active" data-target="login-panel">Iniciar sesión</button>
            <button type="button" class="tab-button" data-target="register-panel">Registro</button>
        </div>

        <!-- ── Panel: Iniciar sesión ─────────────────────────────────────── -->
        <div id="login-panel" class="form-panel">
            <h2>Iniciar sesión</h2>

            <?php if ($flash_exito && $flash_panel === 'login'): ?>
                <div class="alert alert-exito"><?= htmlspecialchars($flash_exito, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($flash_error && $flash_panel === 'login'): ?>
                <div class="alert alert-error"><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form action="procesar_login.php" method="POST">
                <label for="email_login">Correo electrónico</label>
                <input type="email" id="email_login" name="email" required>

                <label for="password_login">Contraseña</label>
                <input type="password" id="password_login" name="password" required>

                <button type="submit">Iniciar sesión</button>
                <p class="small-text">¿No tienes cuenta? <button type="button" class="tab-button" data-target="register-panel">Regístrate aquí</button></p>
            </form>
        </div>

        <!-- ── Panel: Registro ───────────────────────────────────────────── -->
        <div id="register-panel" class="form-panel hidden">
            <h2>Registro</h2>

            <?php if ($flash_error && $flash_panel === 'register'): ?>
                <div class="alert alert-error"><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form action="procesar_registro.php" method="POST">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="email_register">Correo electrónico</label>
                <input type="email" id="email_register" name="email" required>

                <label for="cedula">Número de Cédula</label>
                <input type="text" id="cedula" name="cedula" required>

                <label for="password_register">Contraseña</label>
                <input type="password" id="password_register" name="password" required>

                <label for="rol">¿Cómo deseas registrarte?</label>
                <select id="rol" name="rol" required>
                    <option value="">Selecciona una opción</option>
                    <option value="voluntario">Voluntario</option>
                    <option value="organizador">Organizador</option>
                </select>

                <div id="city-group">
                    <label for="ciudad">Ciudad</label>
                    <select id="ciudad" name="ciudad_id">
                        <option value="">Selecciona tu ciudad</option>
                        <?php if ($resultados_ciudades && $resultados_ciudades->rowCount() > 0): ?>
                            <?php while ($fila = $resultados_ciudades->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= htmlspecialchars($fila['id_ciudad'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($fila['ciudad'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit">Registrar</button>
                <p class="small-text">¿Ya tienes cuenta? <button type="button" class="tab-button" data-target="login-panel">Inicia sesión</button></p>
            </form>
        </div>
    </div>

    <script>
        const tabs   = document.querySelectorAll('.tab-button');
        const panels = {
            'login-panel':    document.getElementById('login-panel'),
            'register-panel': document.getElementById('register-panel')
        };

        function showPanel(panelId) {
            tabs.forEach(btn => btn.classList.toggle('active', btn.dataset.target === panelId));
            Object.values(panels).forEach(panel => panel.classList.add('hidden'));
            if (panels[panelId]) panels[panelId].classList.remove('hidden');
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => showPanel(tab.dataset.target));
        });

        // ── Mostrar/ocultar campo Ciudad según rol ────────────────────────────
        const roleSelect = document.getElementById('rol');
        const cityGroup  = document.getElementById('city-group');
        const citySelect = document.getElementById('ciudad');

        function toggleCityField() {
            if (!roleSelect || !cityGroup) return;
            const esOrganizador = roleSelect.value === 'organizador';
            cityGroup.style.display = esOrganizador ? 'none' : '';
            if (citySelect) citySelect.required = !esOrganizador;
        }

        roleSelect.addEventListener('change', toggleCityField);
        toggleCityField();

        // ── Panel inicial: respeta flash_panel del servidor ───────────────────
        const initialPanel = <?= json_encode($flash_panel) ?>;
        const hash         = window.location.hash.toLowerCase();
        const params       = new URLSearchParams(window.location.search);

        if (hash === '#register' || params.get('panel') === 'register' || initialPanel === 'register') {
            showPanel('register-panel');
        } else {
            showPanel('login-panel');
        }
    </script>
</body>
</html>

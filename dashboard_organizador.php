<?php
session_start();

// Proteger la página: solo acceso para organizadores autenticados
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'organizador') {
    header('Location: login.php?error=acceso_denegado');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Organizador - Voluntariado</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>👋 Bienvenido, <?= htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="badge badge-organizador">Organizador</span>
            <a href="acciones/logout.php" class="btn-logout">Cerrar sesión</a>
        </header>

        <main>
            <p>Aquí podrás crear y gestionar actividades de voluntariado.</p>
            <!-- Contenido del dashboard del organizador aquí -->
        </main>
    </div>
</body>
</html>

<?php

session_start();

// Conexión a la BD — si falla, detenemos la ejecución
try {
    $conexion = new PDO('mysql:host=localhost;dbname=plataforma_voluntariado', 'root', '');
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

$email_ingresado    = trim($_POST['email']    ?? '');
$password_ingresada = $_POST['password']      ?? '';

if (empty($email_ingresado) || empty($password_ingresada)) {
    header('Location: login.php?error=campos_vacios');
    exit();
}

$usuario_encontrado = false;

try {
    // ── 1. Buscar en voluntarios ──────────────────────────────────────────────
    $sql_voluntarios = "SELECT id_voluntario, password_hash, nombre FROM voluntarios WHERE email = ?";
    $stmt_voluntarios = $conexion->prepare($sql_voluntarios);
    $stmt_voluntarios->execute([$email_ingresado]);
    $fila_voluntarios = $stmt_voluntarios->fetch(PDO::FETCH_ASSOC);

    if ($fila_voluntarios && password_verify($password_ingresada, $fila_voluntarios['password_hash'])) {
        $_SESSION['usuario_id'] = $fila_voluntarios['id_voluntario'];
        $_SESSION['nombre']     = $fila_voluntarios['nombre'];
        $_SESSION['rol']        = 'voluntario';
        $usuario_encontrado     = true;
    }

    // ── 2. Si no estaba en voluntarios, buscar en organizador ─────────────────
    if (!$usuario_encontrado) {
        $sql_org  = "SELECT id_organizador, password_hash, nombre FROM organizador WHERE Email = ?";
        $stmt_org = $conexion->prepare($sql_org);
        $stmt_org->execute([$email_ingresado]);
        $fila_org = $stmt_org->fetch(PDO::FETCH_ASSOC);

        if ($fila_org && password_verify($password_ingresada, $fila_org['password_hash'])) {
            $_SESSION['usuario_id'] = $fila_org['id_organizador'];
            $_SESSION['nombre']     = $fila_org['nombre'];
            $_SESSION['rol']        = 'organizador';
            $usuario_encontrado     = true;
        }
    }

    // ── 3. Redirigir según rol ────────────────────────────────────────────────
    if ($usuario_encontrado) {
        if ($_SESSION['rol'] === 'voluntario') {
            header('Location: dashboard_voluntario.php');
        } else {
            header('Location: dashboard_organizador.php');
        }
        exit();
    } else {
        $_SESSION['flash_error'] = 'Correo electrónico o contraseña incorrectos.';
        $_SESSION['flash_panel'] = 'login';
        header('Location: login.php');
        exit();
    }

} catch (PDOException $e) {
    die('Error en la consulta: ' . $e->getMessage());
}
?>
<?php
session_start();

// ── Helpers de flash ──────────────────────────────────────────────────────────
function redirigir_error(string $mensaje): void {
    $_SESSION['flash_error']  = $mensaje;
    $_SESSION['flash_panel']  = 'register';
    header('Location: login.php');
    exit();
}

function redirigir_exito(string $mensaje): void {
    $_SESSION['flash_exito'] = $mensaje;
    $_SESSION['flash_panel'] = 'login';
    header('Location: login.php');
    exit();
}

// ── Conexión a la BD ──────────────────────────────────────────────────────────
try {
    $conexion = new PDO('mysql:host=localhost;dbname=plataforma_voluntariado', 'root', '');
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    redirigir_error('No se pudo conectar a la base de datos. Inténtalo más tarde.');
}

// ── Recibir datos del formulario ──────────────────────────────────────────────
$nombre    = trim($_POST['nombre']    ?? '');
$email     = trim($_POST['email']     ?? '');
$cedula    = trim($_POST['cedula']    ?? '');
$password  = $_POST['password']       ?? '';
$rol       = $_POST['rol']            ?? '';
$ciudad_id = $_POST['ciudad_id']      ?? null;

// ── Validaciones básicas ──────────────────────────────────────────────────────
if (empty($nombre) || empty($email) || empty($cedula) || empty($password) || empty($rol)) {
    redirigir_error('Por favor completa todos los campos obligatorios.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirigir_error('El formato del correo electrónico no es válido.');
}

// ── Inserción según rol ───────────────────────────────────────────────────────
$password_encriptada = password_hash($password, PASSWORD_DEFAULT);

try {
    if ($rol === 'voluntario') {
        if (empty($ciudad_id)) {
            redirigir_error('Debes seleccionar una ciudad para registrarte como voluntario.');
        }
        $sql  = "INSERT INTO voluntarios (nombre, email, cedula, password_hash, id_ciudad_fk)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $email, $cedula, $password_encriptada, (int)$ciudad_id]);

    } elseif ($rol === 'organizador') {
        $sql  = "INSERT INTO organizador (nombre, Email, cedula, password_hash)
                 VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $email, $cedula, $password_encriptada]);

    } else {
        redirigir_error('El rol seleccionado no es válido.');
    }

    // ── Éxito ─────────────────────────────────────────────────────────────────
    redirigir_exito('¡Registro exitoso! Ya puedes iniciar sesión.');

} catch (PDOException $e) {
    $sqlstate  = (string) $e->getCode();
    $msg       = $e->getMessage();
    $errorCode = '';

    // Extraer el código numérico de MySQL del mensaje (ej: "1062", "1452")
    if (preg_match('/:\s*(\d{4})\s/', $msg, $matches)) {
        $errorCode = $matches[1];
    }

    if ($errorCode === '1062') {
        // Duplicate entry — determinar qué campo está duplicado
        if (str_contains(strtolower($msg), 'email')) {
            redirigir_error('Ese correo electrónico ya está registrado. ¿Quieres iniciar sesión?');
        } elseif (str_contains(strtolower($msg), 'cedula')) {
            redirigir_error('Esa cédula ya está registrada en el sistema.');
        } else {
            redirigir_error('El correo o la cédula que ingresaste ya están registrados.');
        }
    }

    if ($errorCode === '1452') {
        // Foreign key violation — valor referenciado no existe en la tabla padre
        redirigir_error('Error de integridad: un valor ingresado no corresponde a un registro válido (llave foránea). Verifica los datos e inténtalo de nuevo.');
    }

    // Cualquier otro error de BD
    redirigir_error('Ocurrió un error inesperado al procesar tu registro. Inténtalo más tarde.');
}
?>
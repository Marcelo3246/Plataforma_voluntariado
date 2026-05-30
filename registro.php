<?php
try{

 $conexion = new PDO('mysql:host=localhost;dbname=plataforma_voluntariado', 'root', '');
 echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

$sql_ciudades = "SELECT id_ciudad, ciudad FROM ciudad ORDER BY ciudad ASC";
$resultados_ciudades = $conexion->query($sql_ciudades);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Voluntariado</title>
</head>
<body>
    <h2>Registro de Voluntariado</h2>
    <form action="procesar_registro.php" method="POST">

    <label for="nombre">Nombre:</label><br>
    <input type="text" id="nombre" name="nombre" required><br><br>

    <label for="email">Correo Electrónico:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="cedula">Número de Cédula:</label><br>
    <input type="text" id="cedula" name="cedula" required><br><br>

    <label for="password">Contraseña</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <label for="rol">¿Como deseas registrarte?</label><br>
    <select id="rol" name="rol" required>
        <option value="">Selecciona una opción</option>
        <option value="voluntario">Voluntario</option>
        <option value="organizacion">Organizador</option>

    </select><br><br>

    <label for="ciudad">Ciudad:</label><br>
    <select id="ciudad" name="ciudad_id" required>
        <option value="">Selecciona tu ciudad</option>
      <?php
        // En PDO, rowCount() verifica si hay resultados
        if ($resultados_ciudades->rowCount() > 0) {
            // Se usa fetch(PDO::FETCH_ASSOC) en lugar de fetch_assoc()
            while ($fila = $resultados_ciudades->fetch(PDO::FETCH_ASSOC)){
                // También te faltaba el signo '=' en el atributo value del HTML
                echo '<option value="'.$fila["id_ciudad"].'">' .$fila["ciudad"]. '</option>';
            }
        }
        ?>
        </select><br><br>
        <button type="submit">Registrar</button>

    </form>
</body>
</html>

<?php $conexion = null; ?>
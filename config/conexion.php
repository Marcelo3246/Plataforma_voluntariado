<?php
try{

 $conexion = new PDO('mysql:host=localhost;dbname=plataforma_voluntariado', 'root', '');
 echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
<?php

try{

 $conexion = new PDO('mysql:host=localhost;dbname=plataforma_voluntariado', 'root', '');
 echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

//Recibimos los datos del formulario

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$cedula = $_POST['cedula'];
$password = $_POST['password'];
$rol = $_POST['rol'];
$ciudad_id = $_POST['ciudad_id'];

//Acá encriptamos la pass


$password_encriptada = password_hash($password, PASSWORD_DEFAULT);

//Preparamos la consulta SQL para insertar los datos en la tabla "usuarios"

if($rol == 'voluntario'){

$sql = "INSERT INTO voluntarios (nombre, email, cedula, password, id_ciudad_fk) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);

$stmt->execute([$nombre, $email, $cedula, $password_encriptada, $ciudad_id]);

}else if($rol == 'organizador'){

$sql = "INSERT INTO organizadores (nombre, email, cedula, password, id_ciudad_fk) VALUES(?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->execute([$nombre, $email, $cedula, $password_encriptada, $ciudad_id]);

}
echo "Registro exitoso";




?>
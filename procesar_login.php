<?php

session_start();

try{

 $conexion = new PDO('mysql:host=localhost;dbname=plataforma_voluntariado', 'root', '');
 echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

try{

$email_ingresado = $_POST['email'];
$password_ingresada = $_POST['password'];

$usuario_encontrado = false;

$sql_voluntarios = "SELECT id_voluntario, password, nombre FROM voluntarios WHERE email = ?";
$stmt_voluntarios = $conexion->prepare($sql_voluntarios);
$stmt_voluntarios->execute([$email_ingresado]);

$fila_voluntarios = $stmt_voluntarios->fetch(PDO::FETCH_ASSOC);

//Si fila contiene datos, significa que el correo si existe en la tabla de volutnarios

if($fila_voluntarios){

    if(password_verify($password_ingresada, $fila_voluntarios['password'])){

    $_SESSION['usuario_id'] = $fila_voluntarios['id_voluntario'];
    $_SESSION['nombre'] = $fila_voluntarios['nombre'];
    $_SESSION['rol'] = 'voluntario';
    $usuario_encontrado = true;


    }


}

//Si no esta en voluntarios, buscamos en organizador

if(!$usuario_encontrado){

$sql_org = "SELECT id_organizador, password, nombre FROM organizador WHERE email = ?";
$stmt_org = $conexion->prepare($sql_org);
$stmt_org->execute([$email_ingresado]);

$fila_org = $stmt_org->fetch(PDO::FETCH_ASSOC);

if($fila_org){

    if(password_verify($password_ingresada, $fila_org['password'])){

    $_SESSION['usuario_id'] = $fila_org['id_organizador'];
    $_SESSION['nombre'] = $fila_org['nombre'];
    $_SESSION['rol'] = 'organizador';
    $usuario_encontrado = true;

}
}

}

if($usuario_encontrado){
    echo "Login exitoso. Bienvenido, " . $_SESSION['nombre'] . "!";
   //aqui podemos redirigir al user a su respectivo dashboard
} else {
    echo "Correo o contraseña incorrectos.";
}

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}

?>
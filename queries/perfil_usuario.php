<?php
include '../queries/conexion.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../interface/loginvista.html?mensaje=not_logged_in");
    exit;
}

// Obtener el ID de usuario de la sesión
$usuario_id = $_SESSION['user_id'];

// Consultar la base de datos para obtener la información del usuario
$consulta = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$consulta->bind_param("i", $usuario_id);
$consulta->execute();
$resultado = $consulta->get_result();

if ($resultado->num_rows > 0) {
    // Obtener los datos del usuario
    $usuario = $resultado->fetch_assoc();

    // Cerrar la consulta y la conexión
    $consulta->close();
    $conexion->close();

    // Incluir el archivo HTML y pasarle los datos del usuario como variables
    include '../interface/usuario.html';
} else {
    echo "No se encontró información del usuario.";
    $consulta->close();
    $conexion->close();
}
?>

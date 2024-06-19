<?php
include '../queries/conexion.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../interface/loginvista.html?mensaje=not_logged_in");
    exit;
}

// Obtener datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];
$contraseña_actual = $_POST['contraseña_actual'];
$intereses = isset($_POST['intereses']) ? implode(',', $_POST['intereses']) : '';

// Verificar si la contraseña ha cambiado
if ($contraseña !== $contraseña_actual) {
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
} else {
    $contraseña_hash = $contraseña_actual;
}

// Verificar si el nuevo usuario ya existe
$consulta_usuario_existente = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? AND id != ?");
$consulta_usuario_existente->bind_param("si", $usuario, $id);
$consulta_usuario_existente->execute();
$resultado_usuario_existente = $consulta_usuario_existente->get_result();

// Verificar si el nuevo correo electrónico ya existe
$consulta_correo_existente = $conexion->prepare("SELECT id FROM usuarios WHERE correo_electronico = ? AND id != ?");
$consulta_correo_existente->bind_param("si", $correo, $id);
$consulta_correo_existente->execute();
$resultado_correo_existente = $consulta_correo_existente->get_result();

if ($resultado_usuario_existente->num_rows > 0) {
    // El nuevo nombre de usuario ya está en uso
    echo "<script>alert('Nombre de usuario en uso');</script>";
    echo "<script>window.location.href='../interface/listas.html';</script>";
    exit;
} elseif ($resultado_correo_existente->num_rows > 0) {
    // El nuevo correo electrónico ya está en uso
    echo "<script>alert('Correo en uso');</script>";
    echo "<script>window.location.href='../interface/listas.html';</script>";
    exit;
} else {
    // Actualizar la información del usuario en la base de datos
    $consulta_actualizar = $conexion->prepare("UPDATE usuarios SET nombre_completo = ?, correo_electronico = ?, usuario = ?, contraseña = ?, intereses = ? WHERE id = ?");
    $consulta_actualizar->bind_param("sssssi", $nombre, $correo, $usuario, $contraseña_hash, $intereses, $id);

    if ($consulta_actualizar->execute()) {
        // Cerrar las consultas
        $consulta_usuario_existente->close();
        $consulta_correo_existente->close();
        $consulta_actualizar->close();
        
        // Cerrar la conexión
        $conexion->close();
        
        // Mostrar un gif después de actualizar el perfil
        echo "<script>alert('Perfil actualizado correctamente');</script>";
        echo "<script>window.location.href='../interface/listas.html?mensaje=update_success';</script>";
        exit;
    } else {
        echo "<script>alert('Error al actualizar los datos');</script>";
        echo "<script>window.location.href='../interface/listas.html';</script>";
    }
}

// Cerrar la conexión
$conexion->close();
?>

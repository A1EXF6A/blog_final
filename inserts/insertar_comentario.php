<?php
header('Content-Type: application/json');
include 'conexion.php'; // Verifica que este archivo esté correctamente configurado
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Verificar si se reciben los datos del formulario
if (!isset($_POST['postId']) || !isset($_POST['comentario'])) {
    echo json_encode(['error' => 'Datos incompletos enviados']);
    exit;
}

// Obtener datos del comentario desde la solicitud POST
$postId = intval($_POST['postId']);
$usuarioId = intval($_SESSION['user_id']);
$comentario = $_POST['comentario'];

// Preparar la consulta para insertar el comentario
$stmt = $conexion->prepare("INSERT INTO `comentarios` (`post_id`, `usuario_id`, `contenido`, `fecha`) VALUES (?, ?, ?, NOW());");
$stmt->bind_param("iis", $postId, $usuarioId, $comentario);

// Ejecutar la consulta y verificar si se realizó correctamente
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al insertar el comentario: ' . $stmt->error]);
}

// Cerrar la conexión y liberar recursos
$stmt->close();
$conexion->close();
?>

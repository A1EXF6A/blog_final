<?php
header('Content-Type: application/json');
include '../queries/conexion.php';

// Verificar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del cuerpo de la solicitud (JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que se recibieron los datos necesarios
    if (isset($data['postId'])) {
        $postId = $data['postId'];

        // Preparar la consulta SQL para eliminar el post
        $stmt = $conexion->prepare("DELETE FROM blog_ash WHERE id = ?");
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al preparar la consulta']);
            exit;
        }
        $stmt->bind_param("i", $postId);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Post eliminado exitosamente']);
        } else {
            http_response_code(500); // Código de error HTTP 500
            echo json_encode(['error' => 'Error al eliminar el post: ' . $stmt->error]);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        http_response_code(400); // Código de error HTTP 400
        echo json_encode(['error' => 'No se recibieron todos los datos necesarios']);
    }
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>


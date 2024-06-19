<?php
header('Content-Type: application/json');
include '../queries/conexion.php';

// Verificar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del cuerpo de la solicitud (JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que se recibieron los datos necesarios
    if (isset($data['id']) && isset($data['titulo']) && isset($data['contenido']) && isset($data['etiqueta']) && isset($data['categoria']) && isset($data['visibilidad'])) {
        $postId = $data['id'];
        $titulo = $data['titulo'];
        $contenido = $data['contenido'];
        $etiqueta = $data['etiqueta'];
        $categoria = $data['categoria'];
        $visibilidad = $data['visibilidad'];

        // Preparar la consulta SQL para actualizar el post
        $stmt = $conexion->prepare("UPDATE blog_ash SET titulo = ?, contenido = ?, etiqueta = ?, categoria = ?, visibilidad = ? WHERE id = ?");
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al preparar la consulta']);
            exit;
        }
        $stmt->bind_param("sssssi", $titulo, $contenido, $etiqueta, $categoria, $visibilidad, $postId);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Post actualizado exitosamente']);
        } else {
            http_response_code(500); // Código de error HTTP 500
            echo json_encode(['error' => 'Error al actualizar el post']);
        }

        // Cerrar la declaración y la conexión
        $stmt->close();
    } else {
        http_response_code(400); // Código de error HTTP 400
        echo json_encode(['error' => 'No se recibieron todos los datos necesarios']);
    }
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>

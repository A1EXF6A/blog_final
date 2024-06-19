<?php
header('Content-Type: application/json');
include 'conexion.php';

if (isset($_GET['id'])) {
    $postId = intval($_GET['id']);

    // Preparar la consulta SQL para obtener los comentarios
    $sql = "
        SELECT 
            usuarios.usuario, 
            comentarios.contenido AS comentario, 
            comentarios.fecha
        FROM 
            comentarios
        LEFT JOIN 
            usuarios 
        ON 
            comentarios.usuario_id = usuarios.id
        WHERE 
            comentarios.post_id = ?
        ORDER BY 
            comentarios.fecha DESC
    ";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Error en la preparación de la consulta SQL: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        echo json_encode(['error' => 'Error en la ejecución de la consulta SQL: ' . $conexion->error]);
        exit;
    }

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // Formatear la fecha si es necesario (dependiendo del formato en la base de datos)
        $comment = [
            'usuario' => $row['usuario'],
            'comentario' => $row['comentario'],
            'fecha' => $row['fecha'] // Asegúrate de que el formato sea correcto para JS
        ];
        $comments[] = $comment;
    }

    echo json_encode($comments, JSON_UNESCAPED_UNICODE); // Asegura que los caracteres especiales se manejen correctamente

    $stmt->close();
} else {
    echo json_encode(['error' => 'ID de post no proporcionado']);
}

$conexion->close();
?>

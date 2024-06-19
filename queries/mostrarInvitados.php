<?php
header('Content-Type: application/json');
include 'conexion.php';

// Si se proporciona un ID de post específico en la solicitud GET
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "
        SELECT 
            blog_ash.id, 
            blog_ash.titulo, 
            blog_ash.contenido, 
            blog_ash.etiqueta, 
            blog_ash.categoria, 
            blog_ash.visibilidad, 
            blog_ash.fecha, 
            usuarios.usuario 
        FROM 
            blog_ash 
        LEFT JOIN 
            usuarios 
        ON 
            blog_ash.usuario_id = usuarios.id 
        WHERE 
            blog_ash.id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        echo json_encode(['error' => $conexion->error]);
        exit;
    }

    if ($row = $result->fetch_assoc()) {
        $post = [
            'id' => $row['id'],
            'titulo' => $row['titulo'],
            'contenido' => $row['contenido'],
            'etiqueta' => $row['etiqueta'],
            'categoria' => $row['categoria'],
            'visibilidad' => $row['visibilidad'],
            'fecha' => $row['fecha'],
            'usuario' => $row['usuario']
        ];
        echo json_encode($post);
    } else {
        echo json_encode(['error' => 'Post not found']);
    }

    $stmt->close();
} else {
    // Si no se proporciona un ID, devolver todos los posts públicos
    $sql = "
        SELECT 
            id, 
            titulo, 
            contenido, 
            etiqueta, 
            categoria, 
            visibilidad, 
            fecha 
        FROM 
            blog_ash";
    $result = $conexion->query($sql);

    if ($result === false) {
        echo json_encode(['error' => $conexion->error]);
        exit;
    }

    $posts = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $post = [
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'contenido' => $row['contenido'],
                'etiqueta' => $row['etiqueta'],
                'categoria' => $row['categoria'],
                'visibilidad' => $row['visibilidad'],
                'fecha' => $row['fecha']
            ];
            $posts[] = $post;
        }
    }

    echo json_encode($posts);
}

$conexion->close();
?>

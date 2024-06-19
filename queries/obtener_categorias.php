<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    $query = $conexion->prepare("SELECT p.id, p.titulo, p.contenido, p.categoria, p.visibilidad, p.usuario_id, p.fecha, u.username AS usuario FROM blog_ash p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ? AND p.visibilidad = 'publico'");
    $query->bind_param('i', $postId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        // Aquí puedes agregar cualquier otra lógica necesaria antes de enviar la respuesta JSON

        echo json_encode($post);
    } else {
        echo json_encode(['error' => 'Post no encontrado o no tiene permisos para verlo']);
    }

    $query->close();
    $conexion->close();
} else {
    echo json_encode(['error' => 'No se proporcionó un ID de post']);
}
?>

<?php
header('Content-Type: application/json');
include 'conexion.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No se ha iniciado sesión']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, titulo, contenido, etiqueta, categoria, visibilidad, fecha FROM blog_ash WHERE usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

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

$stmt->close();
$conexion->close();

echo json_encode($posts);
?>

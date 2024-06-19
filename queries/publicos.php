<?php
header('Content-Type: application/json');
include 'conexion.php';

$sql = "SELECT id, titulo, contenido, etiqueta, categoria, visibilidad, fecha FROM blog_ash WHERE visibilidad = 'publico'";
$result = $conexion->query($sql);

if ($result === false) {
    echo json_encode(['error' => $conexion->error]);
    header("Location: ../interface/loginvista.html");
    exit;
}

$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

echo json_encode($posts);

$conexion->close();
?>

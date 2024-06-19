<?php
// Conecta a la base de datos
$conn = mysqli_connect("localhost:8081", "root", "ligacampeon", "blog1");

// Verifica la conexión
if (!$conn) {
    die("Error de conexión: ". mysqli_connect_error());
}

// Obtiene el nombre del usuario actual
$query = "SELECT usuario FROM usuarios WHERE id = $_SESSION'id_usuario'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $nombre_usuario = $row['nombre'];
} else {
    $nombre_usuario = "Usuario no encontrado";
}

// Cierra la conexión
mysqli_close($conn);
?>
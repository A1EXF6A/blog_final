<?php
include '../queries/conexion.php';
include 'Publicacion.php'; // Incluye la clase Publicacion
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../interface/loginvista.html?mensaje=not_logged_in");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $categoria = $_POST['categoria'];
    $etiqueta = $_POST['etiqueta'];
    $visibilidad = $_POST['visibilidad'];

    // Obtener el ID del usuario de la sesión
    $usuario_id = $_SESSION['user_id'];

    // Crear una instancia de Publicacion
    $publicacion = new Publicacion($conexion, $titulo, $contenido, $categoria, $etiqueta, $visibilidad, $usuario_id);

    // Guardar la publicación
    $mensaje = $publicacion->guardar();
    $redireccion = $mensaje === "Publicación guardada exitosamente." ? "../interface/listas.html" : "../interface/crearNuevo.html";

    $conexion->close();

    // Mostrar el mensaje y redirigir
    echo "<script>
            alert('$mensaje');
            window.location.href = '$redireccion';
          </script>";
    exit;
}
?>

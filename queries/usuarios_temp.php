<?php
include 'conexion.php'; // Asegúrate de que este archivo tiene los detalles de conexión a tu base de datos

// Verificar si se recibió un usuario por POST
if (isset($_POST['usuario'])) {
    $usuario = $_POST['usuario'];

    // Verificar si el usuario ya existe en la tabla usuarios_temp
    $checkQuery = "SELECT COUNT(*) AS count FROM invitado_temp WHERE nombre_completo = ?";
    
    // Preparar la consulta SQL
    $stmtCheck = $conexion->prepare($checkQuery);
    
    // Bind los parámetros
    $stmtCheck->bind_param("s", $usuario);
    
    // Ejecutar la consulta preparada
    $stmtCheck->execute();
    
    // Obtener el resultado
    $result = $stmtCheck->get_result();
    
    // Obtener el número de filas
    $row = $result->fetch_assoc();
    $userCount = $row['count'];
    
    // Cerrar la consulta preparada
    $stmtCheck->close();

    // Si userCount es mayor que 0, significa que el usuario ya existe
    if ($userCount > 0) {
        // Redireccionar al usuario a la página lectura_invitado.html
        header('Location: ../interface/lectura_invitado.html');
        exit; // Asegurar que se detiene la ejecución del script después de la redirección
    } else {
        // Preparar la consulta SQL para insertar el usuario en la tabla usuarios_temp
        $sql = "INSERT INTO `invitado_temp` (nombre_completo) VALUES (?)";
        
        // Preparar la sentencia SQL
        $stmtInsert = $conexion->prepare($sql);
        
        // Bind los parámetros
        $stmtInsert->bind_param("s", $usuario);
        
        // Ejecutar la sentencia preparada
        if ($stmtInsert->execute()) {
            header('Location: ../interface/lectura_invitado.html');
        } else {
            echo json_encode(['error' => 'Error al añadir el usuario']);
        }
        
        // Cerrar la sentencia preparada
        $stmtInsert->close();
    }
} else {
    echo json_encode(['error' => 'No se recibió el nombre de usuario']);
}

// Cerrar la conexión
$conexion->close();
?>

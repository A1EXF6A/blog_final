<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el correo electrónico de la sesión
    $correo = $_SESSION["correo"];
    $codigo = $_POST["codigo"];

    // Verificar el código de verificación
    $consulta = "SELECT * FROM codigo_verificacion WHERE correo_electronico = ? ORDER BY creado_en DESC LIMIT 1";
    if ($stmt = $conexion->prepare($consulta)) {
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            if ($fila['codigo'] == $codigo) {
                // Si el código es correcto, insertar los datos en la tabla de usuarios
                $consulta_usuario = "INSERT INTO usuarios (nombre_completo, correo_electronico, usuario, contraseña, intereses)
                                     SELECT nombre_completo, correo_electronico, usuario, contraseña, intereses
                                     FROM usuarios_temp
                                     WHERE correo_electronico = ?";
                if ($stmt_usuario = $conexion->prepare($consulta_usuario)) {
                    $stmt_usuario->bind_param("s", $correo);
                    if ($stmt_usuario->execute()) {
                        // Eliminar los datos temporales
                        $consulta_delete = "DELETE FROM usuarios_temp WHERE correo_electronico = ?";
                        if ($stmt_delete = $conexion->prepare($consulta_delete)) {
                            $stmt_delete->bind_param("s", $correo);
                            $stmt_delete->execute();
                            $stmt_delete->close();
                        }

                        // Redirigir a la página de login
                        echo "<script>alert('Registro exitoso');</script>";
                        echo "<script>window.location.href='../interface/loginvista.html';</script>";
        
                        exit;
                    } else {
                        error_log("Error al insertar usuario: " . $stmt_usuario->error);
                        header("Location: ../interface/loginvista.html?mensaje=errorConsulta");
                        exit;
                    }
                    $stmt_usuario->close();
                } else {
                    error_log("Error al preparar la consulta de inserción de usuario: " . $conexion->error);
                    header("Location: ../interface/loginvista.html?mensaje=errorConsulta");
                    exit;
                }
            } else {
                // Código incorrecto
                header("Location: ../interface/ingresar_registro.html?mensaje=errorCodigo");
                exit;
            }
        } else {
            // No se encontró el código
            header("Location: ../interface/loginvista.html?mensaje=errorNoE");
            exit;
        }
        $stmt->close();
    } else {
        error_log("Error al preparar la consulta de selección: " . $conexion->error);
        header("Location: ../interface/loginvista.html?mensaje=errorConsulta");
        exit;
    }
}
?>
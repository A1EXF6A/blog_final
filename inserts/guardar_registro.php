<?php
include '../queries/conexion.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

session_start();

// Verificar si se ha enviado un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $usuario = $_POST["usuario"];
    $contraseña = $_POST["contraseña"];
    $intereses = implode(",", $_POST["intereses"]); // Convertir intereses a una cadena separada por comas

    // Hash de la contraseña
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    // Verificar si el correo electrónico ya existe
    $consulta_correo = "SELECT * FROM usuarios WHERE correo_electronico = ?";
    $correo_existente = false;
    if ($stmt = $conexion->prepare($consulta_correo)) {
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            $correo_existente = true;
        }
        $stmt->close();
    } else {
        error_log("Error al preparar la consulta de verificación de correo: " . $conexion->error);
        header("Location: ../interface/loginvista.html?mensaje=errorConsulta");
        exit;
    }

    // Verificar si el nombre de usuario ya existe
    $consulta_usuario = "SELECT * FROM usuarios WHERE usuario = ?";
    $usuario_existente = false;
    if ($stmt = $conexion->prepare($consulta_usuario)) {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            $usuario_existente = true;
        }
        $stmt->close();
    } else {
        error_log("Error al preparar la consulta de selección: " . $conexion->error);
        header("Location: ../interface/loginvista.html?mensaje=errorConsulta");
        exit;
    }

    // Mostrar mensajes de error personalizados
    if ($correo_existente && $usuario_existente) {
        header("Location: ../interface/registrarvista.html?mensaje=Usados");
        exit;
    } elseif ($correo_existente) {
        header("Location: ../interface/registrarvista.html?mensaje=Ecorreo");
        exit;
    } elseif ($usuario_existente) {
        header("Location: ../interface/registrarvista.html?mensaje=Eusuario");
        exit;
    } else {
        // Generar un código de verificación
        $codigo = rand(100000, 999999);

        // Insertar o actualizar el código en la base de datos
        $consulta_insert = "INSERT INTO codigo_verificacion (correo_electronico, codigo) VALUES (?, ?)
                            ON DUPLICATE KEY UPDATE codigo = ?";
        if ($stmt_insert = $conexion->prepare($consulta_insert)) {
            $stmt_insert->bind_param("sss", $correo, $codigo, $codigo);
            $stmt_insert->execute();
            $stmt_insert->close();

            // Comprobar si ya existe un registro en usuarios_temp para el mismo correo electrónico
            $sql_temp_check = "SELECT * FROM usuarios_temp WHERE correo_electronico = ?";
            $stmt_temp_check = $conexion->prepare($sql_temp_check);
            $stmt_temp_check->bind_param("s", $correo);
            $stmt_temp_check->execute();
            $resultado_temp_check = $stmt_temp_check->get_result();
            if ($resultado_temp_check->num_rows > 0) {
                // Actualizar los datos si ya existe un registro
                $sql_temp = "UPDATE usuarios_temp SET nombre_completo = ?, usuario = ?, contraseña = ?, intereses = ?
                             WHERE correo_electronico = ?";
                $stmt_temp = $conexion->prepare($sql_temp);
                $stmt_temp->bind_param("sssss", $nombre, $usuario, $contraseña_hash, $intereses, $correo);
            } else {
                // Insertar nuevos datos si no existe un registro
                $sql_temp = "INSERT INTO usuarios_temp (nombre_completo, correo_electronico, usuario, contraseña, intereses)
                             VALUES (?, ?, ?, ?, ?)";
                $stmt_temp = $conexion->prepare($sql_temp);
                $stmt_temp->bind_param("sssss", $nombre, $correo, $usuario, $contraseña_hash, $intereses);
            }

            if ($stmt_temp->execute()) {
                // Enviar el código al correo electrónico usando PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Configuración del servidor
                    $mail->SMTPDebug = 0;                                       // Desactivar la depuración SMTP
                    $mail->isSMTP();                                            // Enviar usando SMTP
                    $mail->Host       = 'smtp.gmail.com';                       // Configurar el servidor SMTP
                    $mail->SMTPAuth   = true;                                   // Habilitar autenticación SMTP
                    $mail->Username   = 'stevan02henao@gmail.com';              // SMTP username
                    $mail->Password   = 'uyzpvuyjbcqpevuf';                     // Contraseña SMTP
                    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Habilitar encriptación TLS
                    $mail->Port       = 587;                                    // Puerto TCP para conectarse

                    // Destinatarios
                    $mail->setFrom('stevan02henao@gmail.com', 'Tu Nombre o Empresa');
                    $mail->addAddress($correo);                                 // Añadir destinatario

                    // Contenido del correo
                    $mail->isHTML(true);                                        // Configurar el formato del correo a HTML
                    $mail->Subject = 'Código de Verificación';
                    $mail->Body    = 'Su código de verificación es: <b>' . $codigo . '</b>';
                    $mail->AltBody = 'Su código de verificación es: ' . $codigo;

                    $mail->send();

                    $_SESSION["correo"] = $correo;

                    // Redirigir al usuario a la página de ingreso de código
                    header("Location: ../interface/ingresar_registro.html");
                    exit;
                } catch (Exception $e) {
                    error_log("Error al enviar el correo electrónico: " . $mail->ErrorInfo);
                    header("Location: ../interface/registrarvista.html?mensaje=errorMensaje");
                    exit;
                }
            } else {
                error_log("Error al guardar datos temporales: " . $stmt_temp->error);
                header("Location: ../interface/loginvista.html?mensaje=errorConsulta");
                exit;
            }
            $stmt_temp_check->close();
            $stmt_temp->close();
        } else {
            error_log("Error al preparar la consulta de inserción temporal: " . $conexion->error);
            header("Location: ../interface/loginvista.html?mensaje=errorConsulta");
            exit;
        }
    }
}

// Cerrar conexión
$conexion->close();
?>

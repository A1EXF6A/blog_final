<?php
session_start();

// Check if the guest username is set in the POST request
if (isset($_POST['invitado'])) {
    $guest_user = trim($_POST['invitado']);

    // Save the guest username in a session variable
    $_SESSION['guest_user'] = $guest_user;

    // Redirect to vista_usuario.html
    header("Location: ../interface/vista_usuario.html");
    exit;
} else {
    // If the guest username is not set, redirect back to the login page
    header("Location: ../interface/loginvista.html");
    exit;
}
?>

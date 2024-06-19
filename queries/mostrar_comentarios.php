<?php
// Conexión a la base de datos
$servername = "localhost:3308";
$username = "root";
$password = "";
$dbname = "blog";



// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener el postId desde la URL
$postId = $_GET['postId'];

// Consulta para obtener los comentarios de un post específico con el nombre del usuario y el título del post
$sql = "SELECT comentarios.id, usuarios.usuario AS nombre_usuario, comentarios.contenido, comentarios.fecha, blog_ash.titulo AS titulo_post
        FROM comentarios 
        INNER JOIN usuarios ON comentarios.usuario_id = usuarios.id
        INNER JOIN blog_ash ON comentarios.post_id = blog_ash.id
        WHERE comentarios.post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

// Array para almacenar los comentarios y la información del post
$postData = [];
$comments = [];

// Recorrer los resultados y agregarlos al array de comentarios
while ($row = $result->fetch_assoc()) {
    // Guardar el título del post solo una vez en $postData
    if (empty($postData)) {
        $postData = [
            'titulo_post' => $row['titulo_post']
            // Puedes añadir más campos del post aquí si los necesitas
        ];
    }

    $comments[] = [
        'id' => $row['id'],
        'nombre_usuario' => $row['nombre_usuario'],
        'contenido' => $row['contenido'],
        'fecha' => $row['fecha']
    ];
}

// Cerrar la conexión y devolver los comentarios y la información del post como JSON
$stmt->close();
$conn->close();

// Combinar los datos del post y los comentarios en un solo array
$response = [
    'post' => $postData,
    'comments' => $comments
];

// Devolver los datos como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

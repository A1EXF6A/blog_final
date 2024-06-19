<?php
class Publicacion {
    private $conexion;
    private $titulo;
    private $contenido;
    private $categoria;
    private $etiqueta;
    private $visibilidad;
    private $usuario_id;
    private $fecha;

    public function __construct($conexion, $titulo, $contenido, $categoria, $etiqueta, $visibilidad, $usuario_id) {
        $this->conexion = $conexion;
        $this->titulo = $this->sanitize($titulo);
        $this->contenido = $this->sanitize($contenido);
        $this->categoria = $this->sanitize($categoria);
        $this->etiqueta = $this->sanitize($etiqueta);
        $this->visibilidad = $this->sanitize($visibilidad);
        $this->usuario_id = $usuario_id;
        $this->fecha = date('Y-m-d');
    }

    private function sanitize($input) {
        return trim($input);
    }

    public function guardar() {
        $consulta = $this->conexion->prepare("INSERT INTO blog_ash (titulo, contenido, categoria, etiqueta, visibilidad, usuario_id, fecha) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $consulta->bind_param("sssssis", $this->titulo, $this->contenido, $this->categoria, $this->etiqueta, $this->visibilidad, $this->usuario_id, $this->fecha);
        
        if ($consulta->execute()) {
            return "Publicación guardada exitosamente.";
        } else {
            return "Error: " . $consulta->error;
        }

        $consulta->close();
    }
}
?>

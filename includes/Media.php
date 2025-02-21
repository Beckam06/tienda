<?php
class Media {
    public $errors = array();
    protected $file;

    public function upload($file) {
        $this->file = $file;
        $this->validate_file();
        if (empty($this->errors)) {
            return $this->move_file();
        } else {
            return false;
        }
    }

    protected function validate_file() {
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($this->file['type'], $allowed_types)) {
            $this->errors[] = "Tipo de archivo no permitido.";
        }
        if ($this->file['size'] > 1048576) { // 1MB
            $this->errors[] = "El archivo es demasiado grande.";
        }
    }

    protected function move_file() {
        $target_dir = "uploads/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Crear la carpeta si no existe
        }
        $target_file = $target_dir . basename($this->file['name']);
        if (move_uploaded_file($this->file['tmp_name'], $target_file)) {
            return $this->save_to_db($target_file);
        } else {
            $this->errors[] = "Error al subir el archivo.";
            return false;
        }
    }

    protected function save_to_db($file_path) {
        global $db;
        $file_name = basename($file_path);
        $file_type = $this->file['type'];
        $sql = "INSERT INTO media (file_name, file_type) VALUES ('{$file_name}', '{$file_type}')";
        if ($db->query($sql)) {
            return $db->insert_id(); // Retorna el último ID insertado
        } else {
            $this->errors[] = "Error al guardar en la base de datos.";
            return false;
        }
    }
}
?>
<?php
require_once('includes/load.php');

page_require_level(2);

$id = (int)$_GET['id'];
$product = find_by_id('products', $id);

if (!$product) {
    $session->msg("d", "Producto no encontrado.");
    redirect('productos.php');
}

if (delete_by_id('products', $id)) {
    $session->msg("s", "Producto eliminado exitosamente.");
    redirect('productos.php');
} else {
    $session->msg("d", "Eliminación falló.");
    redirect('productos.php');
}
?>
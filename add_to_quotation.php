<?php
require_once('includes/load.php');
page_require_level(2);

if (isset($_POST['add_product'])) {
    $product_id = (int)$_POST['add_product'];
    $quantity = (int)$_POST['quantity'][$product_id];

    if ($quantity > 0) {
        if (!isset($_SESSION['quotation'])) {
            $_SESSION['quotation'] = [];
        }

        if (isset($_SESSION['quotation'][$product_id])) {
            $_SESSION['quotation'][$product_id] += $quantity;
        } else {
            $_SESSION['quotation'][$product_id] = $quantity;
        }

        $session->msg('s', 'Producto agregado a la cotización.');
    } else {
        $session->msg('d', 'La cantidad debe ser mayor que 0.');
    }

    redirect('quotation.php', false);
}
?>
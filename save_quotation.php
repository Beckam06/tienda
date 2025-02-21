<?php
require_once('includes/load.php');
page_require_level(2);

if (isset($_POST['client_name']) && isset($_SESSION['quotation'])) {
    $client_name = remove_junk($db->escape($_POST['client_name']));
    $client_rtn = remove_junk($db->escape($_POST['client_rtn']));
    $date = make_date();

    // Calcular el total de la cotización
    $total = 0;
    foreach ($_SESSION['quotation'] as $product_id => $quantity) {
        $product = find_by_id('products', $product_id);
        $total += $product['sale_price'] * $quantity;
    }

    // Guardar la cotización en la tabla `quotations`
    $sql = "INSERT INTO quotations (client_name, client_rtn, date, total) VALUES ('{$client_name}', '{$client_rtn}', '{$date}', '{$total}')";
    if ($db->query($sql)) {
        $quotation_id = $db->insert_id(); // Obtener el ID de la cotización recién creada

        // Guardar los ítems de la cotización en la tabla `quotation_items`
        foreach ($_SESSION['quotation'] as $product_id => $quantity) {
            $product = find_by_id('products', $product_id);
            $price = $product['sale_price'];
            $subtotal = $price * $quantity;

            $sql = "INSERT INTO quotation_items (quotation_id, product_id, quantity, price, subtotal) VALUES ('{$quotation_id}', '{$product_id}', '{$quantity}', '{$price}', '{$subtotal}')";
            $db->query($sql);
        }

        // Limpiar la cotización de la sesión
        unset($_SESSION['quotation']);

        $session->msg('s', 'Cotización guardada exitosamente.');
        redirect('list_quotations.php', false);
    } else {
        $session->msg('d', 'Error al guardar la cotización.');
        redirect('quotation.php', false);
    }
} else {
    $session->msg('d', 'No hay productos en la cotización.');
    redirect('quotation.php', false);
}
?>
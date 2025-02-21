<?php

require_once('includes/load.php');
page_require_level(2);

$id = (int)$_GET['id'];
$sql = "DELETE FROM quotations WHERE id='{$id}'";
if ($db->query($sql)) {
    $session->msg('s', "Cotización eliminada con éxito.");
    redirect('list_quotations.php', false);
} else {
    $session->msg('d', 'Lo siento, eliminación falló.');
    redirect('list_quotations.php', false);
}
?>
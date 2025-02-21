<?php

$page_title = 'Editar Cotización';
require_once('includes/load.php');
page_require_level(2);

if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $client_name = remove_junk($db->escape($_POST['client_name']));
    $client_rtn = remove_junk($db->escape($_POST['client_rtn']));
    $total = remove_junk($db->escape($_POST['total']));

    $sql = "UPDATE quotations SET client_name='{$client_name}', client_rtn='{$client_rtn}', total='{$total}' WHERE id='{$id}'";
    if ($db->query($sql)) {
        $session->msg('s', "Cotización actualizada con éxito.");
        redirect('list_quotations.php', false);
    } else {
        $session->msg('d', 'Lo siento, actualización falló.');
        redirect('edit_quotation.php?id=' . $id, false);
    }
}

$quotation = find_by_id('quotations', (int)$_GET['id']);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Editar Cotización</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="edit_quotation.php">
                    <input type="hidden" name="id" value="<?php echo (int)$quotation['id']; ?>">
                    <div class="form-group">
                        <label for="client_name">Nombre del Cliente</label>
                        <input type="text" class="form-control" name="client_name" value="<?php echo remove_junk($quotation['client_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="client_rtn">RTN del Cliente</label>
                        <input type="text" class="form-control" name="client_rtn" value="<?php echo remove_junk($quotation['client_rtn']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="total">Total</label>
                        <input type="text" class="form-control" name="total" value="<?php echo remove_junk($quotation['total']); ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-success">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
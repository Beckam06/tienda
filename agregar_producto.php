<?php
$page_title = 'Agregar Producto';
require_once('includes/load.php');

page_require_level(2);

if (isset($_POST['add_product'])) {
    $req_fields = array('codigo', 'producto', 'precio', 'cantidad', 'ubicacion');
    validate_fields($req_fields);

    if (empty($errors)) {
        $codigo = remove_junk($db->escape($_POST['codigo']));
        $producto = remove_junk($db->escape($_POST['producto']));
        $precio = remove_junk($db->escape($_POST['precio']));
        $cantidad = remove_junk($db->escape($_POST['cantidad']));
        $ubicacion = remove_junk($db->escape($_POST['ubicacion']));

        $query  = "INSERT INTO productos (codigo, producto, precio, cantidad, ubicacion)";
        $query .= " VALUES ('{$codigo}', '{$producto}', '{$precio}', '{$cantidad}', '{$ubicacion}')";

        if ($db->query($query)) {
            $session->msg("s", "Producto agregado exitosamente.");
            redirect('productos.php', false);
        } else {
            $session->msg("d", "Lo siento, registro falló.");
            redirect('add_product.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_product.php', false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Agregar Producto</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_product.php">
                    <div class="form-group">
                        <input type="text" class="form-control" name="codigo" placeholder="Código del Producto" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="producto" placeholder="Nombre del Producto" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="precio" placeholder="Precio" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="cantidad" placeholder="Cantidad" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="ubicacion" placeholder="Ubicación" required>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary">Agregar Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
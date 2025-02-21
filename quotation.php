<?php
$page_title = 'Crear Cotización';
require_once('includes/load.php');
page_require_level(2);

// Obtener todos los productos
$products = join_product_table();
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
                    <span>Seleccionar Productos</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_to_quotation.php">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Agregar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo count_id(); ?></td>
                                <td><?php echo remove_junk($product['name']); ?></td>
                                <td><?php echo remove_junk($product['sale_price']); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $product['id']; ?>]" class="form-control" min="1" value="1">
                                </td>
                                <td>
                                    <button type="submit" name="add_product" class="btn btn-primary" value="<?php echo $product['id']; ?>">Agregar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Productos Seleccionados</span>
                </strong>
            </div>
            <div class="panel-body">
                <?php
                // Mostrar productos seleccionados (si existen)
                if (isset($_SESSION['quotation'])) {
                    foreach ($_SESSION['quotation'] as $product_id => $quantity) {
                        $product = find_by_id('products', $product_id);
                        echo "<p>{$product['name']} - Cantidad: $quantity</p>";
                    }
                }
                ?>
                <form method="post" action="save_quotation.php">
                    <div class="form-group">
                        <label for="client_name">Nombre del Cliente</label>
                        <input type="text" class="form-control" name="client_name" required>
                    </div>
                    <div class="form-group">
                        <label for="client_rtn">RTN del Cliente</label>
                        <input type="text" class="form-control" name="client_rtn">
                    </div>
                    <button type="submit" class="btn btn-success">Guardar Cotización</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
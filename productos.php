<?php
require_once('includes/load.php');

$page_title = 'Lista de Productos';
page_require_level(2);

$products = find_all('productos');


if (isset($_GET['search'])) {
    $search = remove_junk($db->escape($_GET['search']));
    $products = find_all_by_search('productos', $search);
} else {
    $products = find_all('productos');
}
?>

<!-- Agregar un campo de búsqueda en la vista de productos -->
<form method="get" action="productos.php" class="form-inline">
    <div class="form-group">
        <input type="text" class="form-control" name="search" placeholder="Buscar producto">
    </div>
    <button type="submit" class="btn btn-primary">Buscar</button>
</form>
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Lista de Productos</span>
                </strong>
                <div class="pull-right">
                    <a href="add_product.php" class="btn btn-primary">Agregar Producto</a>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th class="text-center" style="width: 10%;">Precio</th>
                            <th class="text-center" style="width: 10%;">Cantidad</th>
                            <th class="text-center" style="width: 10%;">Ubicación</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="text-center"><?php echo count_id(); ?></td>
                            <td><?php echo remove_junk($product['codigo']); ?></td>
                            <td><?php echo remove_junk($product['producto']); ?></td>
                            <td class="text-center"><?php echo remove_junk($product['precio']); ?></td>
                            <td class="text-center"><?php echo remove_junk($product['cantidad']); ?></td>
                            <td class="text-center"><?php echo remove_junk($product['ubicacion']); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <a href="delete_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
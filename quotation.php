<?php
$page_title = 'Crear Cotización';
require_once('includes/load.php');
page_require_level(2);

// Búsqueda
$search = isset($_POST['search']) ? remove_junk($db->escape($_POST['search'])) : (isset($_GET['search']) ? remove_junk($db->escape($_GET['search'])) : '');

// Paginación
$registros_por_pagina = 5; // Número de registros por página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página actual
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta base para productos
$sql = "SELECT p.*, c.name AS categorie, m.file_name AS image 
        FROM products p 
        LEFT JOIN categories c ON c.id = p.categorie_id 
        LEFT JOIN media m ON m.id = p.media_id";

// Aplicar búsqueda si existe
if ($search) {
    $sql .= " WHERE p.name LIKE '%{$search}%'";
}

// Consulta para contar el total de registros
$sql_total = str_replace("SELECT p.*, c.name AS categorie, m.file_name AS image", "SELECT COUNT(*) AS total", $sql);
$resultado_total = $db->query($sql_total);
$fila_total = $db->fetch_assoc($resultado_total);
$total_registros = $fila_total['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta paginada
$sql .= " LIMIT {$registros_por_pagina} OFFSET {$offset}";
$products = find_by_sql($sql);
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
                <form method="post" action="quotation.php" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control" name="search" placeholder="Buscar producto" value="<?php echo $search; ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Buscar</button>
                </form>
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
                <div class="paginacion">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?pagina=<?php echo $pagina_actual - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-primary">Anterior</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?pagina=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-default <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina_actual + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-primary">Siguiente</a>
                    <?php endif; ?>
                </div>
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
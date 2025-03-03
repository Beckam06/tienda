<?php
$page_title = 'Lista de productos';
require_once('includes/load.php');
page_require_level(2);

// Búsqueda
$search = isset($_POST['search']) ? remove_junk($db->escape($_POST['search'])) : (isset($_GET['search']) ? remove_junk($db->escape($_GET['search'])) : '');
$highlight = isset($_GET['highlight']) ? (int)$_GET['highlight'] : '';

// Paginación
$registros_por_pagina = 20; // Número de registros por página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página actual
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta base para productos
$sql = "SELECT p.*, c.name AS categorie, m.file_name AS image 
        FROM products p 
        LEFT JOIN categories c ON c.id = p.categorie_id 
        LEFT JOIN media m ON m.id = p.media_id";

// Aplicar búsqueda si existe
if ($search) {
    $sql .= " WHERE p.name LIKE '%{$search}%' OR p.codigo LIKE '%{$search}%'";
}

// Consulta para contar el total de registros
$sql_total = str_replace("SELECT p.*, c.name AS categorie, m.file_name AS image", "SELECT COUNT(*) AS total", $sql);
$resultado_total = $db->query($sql_total);
$fila_total = $db->fetch_assoc($resultado_total);
$total_registros = $fila_total['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta paginada
$sql .= " LIMIT {$registros_por_pagina} OFFSET {$offset}";
$all_products = find_by_sql($sql);

// Consulta para contar todos los productos con bajo stock
$sql_low_stock = "SELECT COUNT(*) AS total_low_stock FROM products WHERE quantity <= 5";
$resultado_low_stock = $db->query($sql_low_stock);
$fila_low_stock = $db->fetch_assoc($resultado_low_stock);
$total_low_stock = $fila_low_stock['total_low_stock'];
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
        <?php if ($total_low_stock > 0): ?>
            <div class="alert alert-warning">
                <strong>¡Atención!</strong> Hay <?php echo $total_low_stock; ?> productos con bajo stock.
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <div class="pull-right">
                    <a href="add_product.php" class="btn btn-primary">Agregar producto</a>
                </div>
                <form action="product.php" method="post" class="form-inline pull-left" onsubmit="return false;">
                    <div class="form-group">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por nombre o código" value="<?php echo $search; ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Buscar</button>
                </form>
            </div>
            <div class="panel-body" style="overflow-x: auto;">
    <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th> Código </th>
                            <th> Producto </th>
                            <th class="text-center" style="width: 10%;"> Categoría </th>
                            <th class="text-center" style="width: 10%;"> Stock </th>
                            <th class="text-center" style="width: 10%;"> Ubicación </th>
                            <th class="text-center" style="width: 10%;"> Precio </th>
                            <th class="text-center" style="width: 10%;"> Imagen </th>
                            <th class="text-center" style="width: 10%;"> Agregado </th>
                            <th class="text-center" style="width: 100px;"> Acciones </th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
  <?php if (!empty($all_products)): ?>
    <?php foreach ($all_products as $product): ?>
      <tr id="product-<?php echo $product['id']; ?>" class="<?php echo ($product['quantity'] <= 5) ? 'low-stock' : ''; ?>">
        <td class="text-center"><?php echo $offset + count_id(); ?></td>
        <td><?php echo remove_junk($product['codigo']); ?></td>
        <td><?php echo remove_junk($product['name']); ?></td>
        <td class="text-center"><?php echo remove_junk($product['categorie']); ?></td>
        <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
        <td class="text-center"><?php echo remove_junk($product['ubicacion']); ?></td>
        <td class="text-center"><?php echo remove_junk($product['sale_price']); ?></td>
        <td class="text-center">
          <?php if ($product['media_id'] === '0'): ?>
            <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
          <?php else: ?>
            <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
          <?php endif; ?>
        </td>
        <td class="text-center"><?php echo read_date($product['date']); ?></td>
        <td class="text-center">
          <div class="btn-group">
            <a href="edit_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
              <span class="glyphicon glyphicon-edit"></span>
            </a>
            <a href="delete_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
              <span class="glyphicon glyphicon-trash"></span>
            </a>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="10" class="text-center">No se encontraron productos.</td>
    </tr>
  <?php endif; ?>
</tbody>
                </table>
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
</div>
<?php include_once('layouts/footer.php'); ?>

<style>
   /* Estilos para la tabla */
/* Estilos para la tabla */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th, .table td {
    padding: 8px;
    text-align: center;
    border: 1px solid #ddd;
}

.table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

/* Estilos para las imágenes */
.img-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

/* Estilos para productos con bajo stock */
.low-stock {
    background-color: #ffcccc; /* Fondo rojo para productos con bajo stock */
    font-weight: bold; /* Texto en negrita */
}

/* Estilos para el campo de búsqueda */
#search {
    width: 300px;
    transition: width 0.4s ease-in-out;
}

#search:focus {
    width: 400px;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#search').on('input', function() {
        var searchQuery = $(this).val();
        $.ajax({
            url: 'search_products.php', // Archivo PHP que maneja la búsqueda
            method: 'POST',
            data: { search: searchQuery },
            success: function(response) {
                $('#product-table-body').html(response); // Actualiza solo el cuerpo de la tabla
            }
        });
    });
});
</script>
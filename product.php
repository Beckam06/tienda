<?php
  $page_title = 'Lista de productos';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);

  
  $products = join_product_table();

  $search = '';
$highlight = '';
  $low_stock_alert = false;
  $low_stock_products = [];
if (isset($_POST['search']) || isset($_GET['search']) || isset($_GET['highlight'])) {
    $search = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');
    $search = $search ? remove_junk($db->escape($search)) : '';
    $highlight = isset($_GET['highlight']) ? (int)$_GET['highlight'] : '';
    if ($search == '') {
        $products = join_product_table();
    } else {
        $products = search_product_table($search);
    }
} else {
    $products = join_product_table();
}


 
 // Configuración de la paginación
$registros_por_pagina = 5; // Número de registros por página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página actual, por defecto es 1
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta paginada para obtener las categorías
$sql = "SELECT * FROM products LIMIT {$registros_por_pagina} OFFSET {$offset}";
$all_products = find_by_sql($sql);

// Obtener el total de registros para calcular el número total de páginas
$sql_total = "SELECT COUNT(*) AS total FROM products";
$resultado_total = $db->query($sql_total);
$fila_total = $db->fetch_assoc($resultado_total);
$total_registros = $fila_total['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

$low_stock_alert = false;
$low_stock_products = [];
foreach ($products as $product) {
    if ($product['quantity'] <= 5) {
        $low_stock_alert = true;
        $low_stock_products[] = $product;
    }
}



?>
<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
         <div class="pull-right">
           <a href="add_product.php" class="btn btn-primary">Agregar producto</a>
         </div>
         <form action="product.php" method="post" class="form-inline pull-left">
                    <div class="form-group">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por nombre" value="<?php echo $search; ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Buscar</button>
                </form>
        </div>
        <div class="panel-body">
                <?php if ($low_stock_alert): ?>
                    <div class="alert alert-warning">
                        <strong>¡Atención!</strong> Los siguientes productos tienen un stock bajo:
                        <ul>
                            <?php foreach ($low_stock_products as $low_stock_product): ?>
                                <li><?php echo remove_junk($low_stock_product['name']); ?> - Stock: <?php echo remove_junk($low_stock_product['quantity']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
        <div class="panel-body">
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
            <tbody>
            <?php foreach ($products as $product): ?>
              <tr class="<?php echo ($product['quantity'] <= 5) ? 'low-stock' : ''; ?>">
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo remove_junk($product['codigo']); ?></td>
                <td><?php echo remove_junk($product['name']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['categorie']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['ubicacion']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['sale_price']); ?></td>
                <td class="text-center">
                  <?php if($product['media_id'] === '0'): ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                  <?php else: ?>
                    <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                  <?php endif; ?>
                </td>
                <td class="text-center"><?php echo read_date($product['date']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
             <?php endforeach; ?>
            </tbody>
          </table>
          <div class="paginacion">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?pagina=<?php echo $pagina_actual - 1; ?>" class="btn btn-primary">Anterior</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?pagina=<?php echo $i; ?>" class="btn btn-default <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina_actual + 1; ?>" class="btn btn-primary">Siguiente</a>
                    <?php endif; ?>
                </div>
           
        </div>
            </div>
    </div>
  </div>
  <?php include_once('layouts/footer.php'); ?>

  <script>
  // Búsqueda en tiempo real
  document.getElementById('search').addEventListener('input', function() {
    if (this.value.length >= 3) {
      fetch('search_product.php?query=' + this.value)
        .then(response => response.text())
        .then(data => {
          document.getElementById('product-table').innerHTML = data;
          // Resaltar productos con stock bajo
          document.querySelectorAll('#product-table tr').forEach(function(row) {
            var cantidad = parseInt(row.querySelector('td:nth-child(6)').innerText);
            var stock_minimo = parseInt(row.querySelector('td:nth-child(12)').innerText);
            if (cantidad <= stock_minimo) {
              row.classList.add('low-stock');
            }
          });
        });
    } else if (this.value === '') {
      window.location.href = 'product.php';
    }
  });

  // Resaltar producto si hay un highlight
  window.onload = function() {
    var highlight = "<?php echo $highlight; ?>";
    if (highlight) {
      var element = document.getElementById('product-' + highlight);
      if (element) {
        element.classList.add('highlight');
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Eliminar el resaltado y el parámetro de la URL después de 3 segundos
        setTimeout(function() {
          element.classList.remove('highlight');
          window.history.replaceState(null, null, window.location.pathname);
        }, 3000);
      }
    }
  };
</script>

<style>
  .highlight {
    background-color: #ffff99;
  }
  .low-stock {
    background-color: #ffcccc; /* Fondo rojo para productos con bajo stock */
    font-weight: bold; /* Texto en negrita */
  }
</style>
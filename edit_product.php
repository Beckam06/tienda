<?php
$page_title = 'Editar Producto';
require_once('includes/load.php');

// Verificar el nivel de usuario
page_require_level(2);

// Obtener el ID del producto a editar
$product_id = (int)$_GET['id'];
$product = find_by_id('products', $product_id);

if (!$product) {
    $session->msg('d', 'Producto no encontrado.');
    redirect('products.php');
}

$all_categories = find_all('categories');

if (isset($_POST['edit_product'])) {
    $req_fields = array('product-codigo', 'product-title', 'product-categorie', 'product-quantity', 'product-ubicacion', 'saleing-price');
    validate_fields($req_fields);

    if (empty($errors)) {
        $p_code   = remove_junk($db->escape($_POST['product-codigo']));
        $p_name   = remove_junk($db->escape($_POST['product-title']));
        $p_cat    = remove_junk($db->escape($_POST['product-categorie']));
        $p_qty    = remove_junk($db->escape($_POST['product-quantity']));
        $p_ubi    = remove_junk($db->escape($_POST['product-ubicacion']));
        $p_sale   = remove_junk($db->escape($_POST['saleing-price']));

        // Manejo de la imagen
        if (isset($_FILES['product-image']) && $_FILES['product-image']['size'] > 0) {
            $photo = new Media();

            // Subir la nueva imagen
            $media_id = $photo->upload_product_image($_FILES['product-image']);
            if (!$media_id) {
                $session->msg('d', join($photo->errors));
                redirect('edit_product.php?id=' . $product_id, false);
            }

            // Eliminar la imagen anterior (si existe)
            if ($product['media_id'] != 0) {
                $old_media = find_by_id('media', $product['media_id']);
                if ($old_media) {
                    $photo->media_destroy($old_media['id'], $old_media['file_name']);
                }
            }
        } else {
            $media_id = $product['media_id']; // Mantener la imagen actual
        }

        $date = make_date();

        // Actualizar el producto
        $query  = "UPDATE products SET ";
        $query .= "codigo = '{$p_code}', ";
        $query .= "name = '{$p_name}', ";
        $query .= "quantity = '{$p_qty}', ";
        $query .= "ubicacion = '{$p_ubi}', ";
        $query .= "sale_price = '{$p_sale}', ";
        $query .= "categorie_id = '{$p_cat}', ";
        $query .= "media_id = '{$media_id}', ";
        $query .= "date = '{$date}' ";
        $query .= "WHERE id = '{$product_id}'";

        if ($db->query($query)) {
            $session->msg('s', "Producto actualizado exitosamente.");
            redirect('product.php?highlight=' . $product_id, false);
        } else {
            $session->msg('d', 'Lo siento, la actualización falló.');
            redirect('edit_product.php?id=' . $product_id, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_product.php?id=' . $product_id, false);
    }
}

$product = find_by_id('products', (int)$_GET['id']);
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<form method="post" action="edit_product.php?id=<?php echo $product_id; ?>" class="clearfix" enctype="multipart/form-data">
    <!-- Código del producto -->
    <div class="form-group">
        <label for="product-codigo">Código del Producto</label>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-barcode"></i>
            </span>
            <input type="text" class="form-control" name="product-codigo" placeholder="Código del producto" value="<?php echo $product['codigo']; ?>" readonly>
        </div>
    </div>

    <!-- Descripción del producto -->
    <div class="form-group">
        <label for="product-title">Nombre del Producto</label>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-th-large"></i>
            </span>
            <input type="text" class="form-control" name="product-title" placeholder="Producto" value="<?php echo $product['name']; ?>" required>
        </div>
    </div>

    <!-- Categoría -->
    <div class="form-group">
        <label for="product-categorie">Categoría</label>
        <select class="form-control" name="product-categorie" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($all_categories as $cat): ?>
                <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($product['categorie_id'] == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo $cat['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Cantidad -->
    <div class="form-group">
        <label for="product-quantity">Cantidad</label>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-shopping-cart"></i>
            </span>
            <input type="number" class="form-control" name="product-quantity" placeholder="Cantidad" value="<?php echo $product['quantity']; ?>" required>
        </div>
    </div>

    <!-- Ubicación -->
    <div class="form-group">
        <label for="product-ubicacion">Ubicación</label>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-map-marker"></i>
            </span>
            <input type="text" class="form-control" name="product-ubicacion" placeholder="Ubicación" value="<?php echo $product['ubicacion']; ?>" required>
        </div>
    </div>

    <!-- Precio de Venta -->
    <div class="form-group">
        <label for="saleing-price">Precio de Venta</label>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-usd"></i>
            </span>
            <input type="number" class="form-control" name="saleing-price" placeholder="Precio" value="<?php echo $product['sale_price']; ?>" required>
            <span class="input-group-addon">.00</span>
        </div>
    </div>

    <!-- Imagen del Producto -->
    <div class="form-group">
        <label for="product-image">Imagen del Producto</label>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-picture"></i>
            </span>
            <input type="file" name="product-image" id="product-image" class="form-control" accept="image/*">
        </div>
        <?php if ($product['media_id'] != 0): ?>
            <div class="mt-2">
                <img src="uploads/products/<?php echo find_by_id('media', $product['media_id'])['file_name']; ?>" class="img-thumbnail" style="max-width: 200px;">
            </div>
        <?php endif; ?>
    </div>

    <!-- Botón para actualizar producto -->
    <button type="submit" name="edit_product" class="btn btn-primary">Actualizar Producto</button>
</form>

<?php include_once('layouts/footer.php'); ?>

<style>
.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
}

.input-group-addon {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

.img-thumbnail {
    border: 1px solid #ddd;
    padding: 5px;
    background-color: #fff;
}
</style>
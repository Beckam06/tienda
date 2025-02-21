<?php
$page_title = 'Agregar producto';
require_once('includes/load.php');


// Verificar el nivel de usuario
page_require_level(2);

$all_categories = find_all('categories');

if (isset($_POST['add_product'])) {
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
            $media_id = $photo->upload_product_image($_FILES['product-image']); // Subir la imagen y obtener el ID
            if (!$media_id) {
                $session->msg('d', join($photo->errors));
                redirect('add_product.php', false);
            }
        } else {
            $media_id = 0; // Si no se sube una imagen, se asigna 0
        }

        $date = make_date();

        // Verificar si el código ya existe
        $check_code = find_by_field('products', 'codigo', $p_code);
        if ($check_code) {
            $session->msg('d', 'El código del producto ya existe. Por favor, usa un código único.');
            redirect('add_product.php', false);
        }

        // Insertar el producto
        $query  = "INSERT INTO products (";
        $query .= " codigo, name, quantity, ubicacion, sale_price, categorie_id, media_id, date";
        $query .= ") VALUES (";
        $query .= " '{$p_code}', '{$p_name}', '{$p_qty}', '{$p_ubi}', '{$p_sale}', '{$p_cat}', '{$media_id}', '{$date}'";
        $query .= ")";

        if ($db->query($query)) {
            $session->msg('s', "Producto agregado exitosamente.");
            redirect('add_product.php', false);
        } else {
            $session->msg('d', 'Lo siento, registro falló.');
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
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Agregar producto</span>
                </strong>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <form method="post" action="add_product.php" class="clearfix" enctype="multipart/form-data">>
                        <!-- Código del producto -->
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-barcode"></i>
                                </span>
                                <input type="text" class="form-control" name="product-codigo" placeholder="Código del producto" required>
                            </div>
                        </div>

                        <!-- Descripción del producto -->
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th-large"></i>
                                </span>
                                <input type="text" class="form-control" name="product-title" placeholder="Producto" required>
                            </div>
                        </div>

                        <!-- Categoría y Foto -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-control" name="product-categorie" required>
                                        <option value="">Selecciona una categoría</option>
                                        <?php foreach ($all_categories as $cat): ?>
                                            <option value="<?php echo (int)$cat['id']; ?>">
                                                <?php echo $cat['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                <input type="file" name="product-image" id="product-image" class="form-control" accept="image/*" onchange="previewImage(event)">
                <div id="image-preview" style="margin-top: 10px;">
                    <!-- Aquí se mostrará la vista previa de la imagen -->
                </div>
            </div>
        </div>
    </div>
    

<!-- Script para mostrar la vista previa de la imagen -->
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');
        preview.innerHTML = ''; // Limpiar la vista previa anterior

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                preview.appendChild(img);
            };

            reader.readAsDataURL(input.files[0]); // Leer la imagen como URL
        }
    }
</script>
                        <!-- Cantidad, Ubicación, Precio de Compra y Precio de Venta -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                        </span>
                                        <input type="number" class="form-control" name="product-quantity" placeholder="Cantidad" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-map-marker"></i>
                                        </span>
                                        <input type="text" class="form-control" name="product-ubicacion" placeholder="Ubicación" required>
                                    </div>
                                </div>
                              
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-usd"></i>
                                        </span>
                                        <input type="number" class="form-control" name="saleing-price" placeholder="Precio" required>
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón para agregar producto -->
                        <button type="submit" name="add_product" class="btn btn-danger">Agregar producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
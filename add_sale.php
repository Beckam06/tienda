<?php
$page_title = 'Agregar venta';
require_once('includes/load.php');
// Verificar el nivel de usuario que tiene permiso para ver esta página
page_require_level(3);
?>
<?php

if (isset($_POST['add_sale'])) {
    $req_fields = array('s_id', 'quantity', 'price', 'total', 'date');
    validate_fields($req_fields);
    if (empty($errors)) {
        $p_id = $db->escape((int)$_POST['s_id']);
        $s_qty = $db->escape((int)$_POST['quantity']);
        $s_total = $db->escape($_POST['total']);
        $date = $db->escape($_POST['date']);
        $s_date = make_date();

        // Verificar el stock del producto antes de proceder
        $product = find_by_id('products', $p_id);
        if ($product['quantity'] < $s_qty) {
            $session->msg('d', 'No hay suficiente stock disponible para este producto.');
            // Almacenar los datos del formulario en la sesión para repoblar el formulario
            $_SESSION['sale_data'] = $_POST; // Almacenar los datos POST
            redirect('add_sale.php', false); // No limpiar el formulario
            exit; // Detener el procesamiento posterior
        }

        $sql = "INSERT INTO sales (";
        $sql .= " product_id,qty,price,date";
        $sql .= ") VALUES (";
        $sql .= "'{$p_id}','{$s_qty}','{$s_total}','{$s_date}'";
        $sql .= ")";

        if ($db->query($sql)) {
            update_product_qty($s_qty, $p_id);
            $session->msg('s', "Venta agregada ");
            // Limpiar los datos del formulario almacenados después de una venta exitosa
            unset($_SESSION['sale_data']);
            redirect('add_sale.php', false);
        } else {
            $session->msg('d', 'Lo siento, registro falló.');
            // Almacenar los datos del formulario en la sesión para repoblar el formulario
            $_SESSION['sale_data'] = $_POST; // Almacenar los datos POST
            redirect('add_sale.php', false);
        }
    } else {
        $session->msg("d", $errors);
        // Almacenar los datos del formulario en la sesión para repoblar el formulario
        $_SESSION['sale_data'] = $_POST; // Almacenar los datos POST
        redirect('add_sale.php', false);
    }
}

?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
        <form method="post" action="ajax.php" autocomplete="off" id="sug-form">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Búsqueda</button>
                    </span>
                    <input type="text" id="sug_input" class="form-control" name="title" placeholder="Buscar por el nombre del producto">
                </div>
                <div id="result" class="list-group"></div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Agregar venta</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_sale.php">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th> Producto </th>
                                <th> Precio </th>
                                <th> Cantidad </th>
                                <th> Total </th>
                                <th> Agregado</th>
                                <th> Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="product_info">
                            <?php
                            // Repopular el formulario si los datos se almacenan en la sesión
                            if (isset($_SESSION['sale_data'])) {
                                $sale_data = $_SESSION['sale_data'];
                                $product_id = $sale_data['s_id'];

                                // Recuperar el nombre del producto de la base de datos
                                $product = find_by_id('products', $product_id);
                                $product_name = $product ? $product['name'] : '';
                                ?>
                                <tr>
                                    <td>
                                        <input type="text" name="product_name" value="<?php echo $product_name; ?>" readonly>
                                        <input type="hidden" name="s_id" value="<?php echo $sale_data['s_id']; ?>">
                                    </td>
                                    <td><input type="number" name="price" value="<?php echo $sale_data['price']; ?>" readonly></td>
                                    <td><input type="number" name="quantity" value="<?php echo $sale_data['quantity']; ?>"></td>
                                    <td><input type="number" name="total" value="<?php echo $sale_data['total']; ?>" readonly></td>
                                    <td><input type="text" name="date" value="<?php echo $sale_data['date']; ?>"></td>
                                    <td><button type="submit" name="add_sale" class="btn btn-success">Agregar venta</button></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
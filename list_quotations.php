<?php
$page_title = 'Lista de Cotizaciones';
require_once('includes/load.php');
page_require_level(2);

// Búsqueda
$search = isset($_POST['search']) ? remove_junk($db->escape($_POST['search'])) : (isset($_GET['search']) ? remove_junk($db->escape($_GET['search'])) : '');

// Consulta base para cotizaciones
$sql = "SELECT * FROM quotations";

// Aplicar búsqueda si existe
if ($search) {
    $sql .= " WHERE client_name LIKE '%{$search}%'";
}

// Obtener todas las cotizaciones
$quotations = find_by_sql($sql);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Lista de Cotizaciones</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="list_quotations.php" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control" name="search" placeholder="Buscar por nombre" value="<?php echo $search; ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Buscar</button>
                </form>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>RTN</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quotations as $quotation): ?>
                        <tr>
                            <td><?php echo count_id(); ?></td>
                            <td><?php echo remove_junk($quotation['client_name']); ?></td>
                            <td><?php echo remove_junk($quotation['client_rtn']); ?></td>
                            <td><?php echo read_date($quotation['date']); ?></td>
                            <td><?php echo remove_junk($quotation['total']); ?></td>
                            <td>
                                <a href="view_quotation.php?id=<?php echo $quotation['id']; ?>" class="btn btn-info btn-xs">Ver</a>
                                <a href="edit_quotation.php?id=<?php echo $quotation['id']; ?>" class="btn btn-warning btn-xs">Editar</a>
                                <a href="delete_quotation.php?id=<?php echo $quotation['id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('¿Estás seguro de que deseas eliminar esta cotización?');">Eliminar</a>
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
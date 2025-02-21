<?php
$page_title = 'Lista de Cotizaciones';
require_once('includes/load.php');
page_require_level(2);

// Obtener todas las cotizaciones
$quotations = find_all('quotations');
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
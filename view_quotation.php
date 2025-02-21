<?php
$page_title = 'Ver Cotización';
require_once('includes/load.php');
page_require_level(2);

$quotation_id = (int)$_GET['id'];
$quotation = find_by_id('quotations', $quotation_id);
$items = find_items_by_quotation($quotation_id);

if (!$quotation) {
    $session->msg("d", "ID de cotización no válido.");
    redirect('list_quotations.php');
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Cotización #<?php echo $quotation['id']; ?></span>
                </strong>
            </div>
            <div class="panel-body">
                <!-- Botón para imprimir -->
                <button onclick="window.print()" class="btn btn-primary">Imprimir Cotización</button>

                <!-- Contenido de la cotización -->
                <div id="quotation-print" style="width: 100%; max-width: 100%; margin: 0 auto;">
                    <!-- Encabezado de la empresa -->
                    <div class="text-center">
                        <img src="uploads/logo_empresa.png" alt="Logo de la empresa" style="max-width: 100px; height: auto; margin-bottom: 5px;">
                        <h2>A.MW Motorepuesto</h2>
                        <p>Dirección: B°Los graneros esquina Sureste del mercadito verde, esquina opuesta a carwash Cali</p>
                        <p>Teléfono: 9303-7467 | 9353-3842</p>
                    </div>

                    <!-- Información de la cotización -->
                    <h3 class="text-center">Cotización </h3>
                    <p><strong>Cliente:</strong> <?php echo remove_junk($quotation['client_name']); ?></p>
                    <p><strong>RTN:</strong> <?php echo remove_junk($quotation['client_rtn']); ?></p>
                    <p><strong>Fecha:</strong> <?php echo read_date($quotation['date']); ?></p>

                    <!-- Tabla de productos -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo remove_junk($item['name']); ?></td>
                                <td><?php echo remove_junk($item['quantity']); ?></td>
                                <td><?php echo remove_junk($item['price']); ?></td>
                                <td><?php echo remove_junk($item['subtotal']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th><?php echo remove_junk($quotation['total']); ?></th>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Notas adicionales -->
                    <div class="notes">
                        <p><strong>Notas:</strong></p>
                        <p>1. Esta cotización es válida por 30 días a partir de la fecha de emisión.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<style>
    /* Estilos generales para la impresión */
    @media print {
    @page {
        margin: 0.25in;
    }

    body {
        margin: 0;
        padding: 0;
        width: 8.5in;
        height: 11in;
        font-size: 10pt; /* Reduced font size */
    }

    body * {
        visibility: hidden;
    }

    #quotation-print, #quotation-print * {
        visibility: visible;
        box-sizing: border-box; /* Added box-sizing */
    }

    #quotation-print {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        margin: 0 auto;
        padding: 0; /* Removed padding */
        /* margin-left: -0.1in;   /*  Try negative margins cautiously */
        /* margin-right: -0.1in; */ /*  Try negative margins cautiously */
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    .table th {
        background-color: #f2f2f2;
    }

    .btn, .panel-heading, .panel-body > button {
        display: none;
    }

    .text-center {
        text-align: center;
    }

    .text-center img {
        max-width: 100px; /* Reduced max-width */
        height: auto;
        margin-bottom: 10px;
    }

    .notes {
        margin-top: 20px;
        font-size: 12px;
    }
}
</style>
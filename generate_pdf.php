<?php
ob_start(); // Inicia el buffering de salida

error_reporting(E_ALL); // Activa la salida de errores para depuración
ini_set('display_errors', 1); // Muestra los errores en el navegador

require 'vendor/autoload.php'; // Incluye el autoload de Composer
require_once('includes/load.php'); // Incluye tu archivo de configuración

use TCPDF as TCPDF; // Usa la clase TCPDF

$quotation_id = (int)$_GET['id'];
$quotation = find_by_id('quotations', $quotation_id);

// Verifica si la cotización existe
if (!$quotation) {
    die("ID de cotización no válido.");
}

// Obtiene los ítems de la cotización
$items = find_items_by_quotation($quotation_id);

// Si no hay ítems, inicializa un array vacío
if (!$items) {
    $items = [];
}

// Crear una instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar el documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('A.MW Motorepuesto');
$pdf->SetTitle('Cotización ' );
$pdf->SetSubject('Cotización');
$pdf->SetKeywords('Cotización, PDF, A.MW Motorepuesto');

// Establecer márgenes
$pdf->SetMargins(15, 15, 15); // Márgenes de 15mm
$pdf->SetHeaderMargin(0); // Sin margen de encabezado
$pdf->SetFooterMargin(0); // Sin margen de pie de página

// Añadir una página
$pdf->AddPage();

// Contenido del PDF
$html = '
<!-- Logo y información de la empresa -->
<div style="text-align: center;">
    <img src="uploads/logo_empresa.png" style="width: 60px;"> <!-- Logo -->
    <h1>A.MW Motorepuesto</h1>
    <p>Dirección: B°Los graneros esquina Sureste del mercadito verde, esquina opuesta a carwash Cali</p>
    <p>Teléfono: 9303-7467 | 9353-3842</p>
</div>

<!-- Detalles de la cotización -->
<h2 style="text-align: center;">Cotización </h2>
<p><strong>Cliente:</strong> ' . remove_junk($quotation['client_name']) . '</p>
<p><strong>RTN:</strong> ' . remove_junk($quotation['client_rtn']) . '</p>
<p><strong>Fecha:</strong> ' . read_date($quotation['date']) . '</p>

<!-- Tabla de productos -->
<table border="1" cellpadding="5" style="width: 100%; font-size: 10px;">
    <thead>
        <tr>
            <th style="font-size: 10px;">Producto</th>
            <th style="font-size: 10px;">Cantidad</th>
            <th style="font-size: 10px;">""Precio Unitario</th>
            <th style="font-size: 10px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>';

if (!empty($items)) {
    foreach ($items as $item) {
        $html .= '
        <tr>
            <td style="font-size: 10px;">' . remove_junk($item['name']) . '</td>
            <td style="font-size: 10px;">' . remove_junk($item['quantity']) . '</td>
            <td style="font-size: 10px;">L' .number_format( remove_junk($item['price'])) . '</td>
            <td style="font-size: 10px;">L' . number_format (remove_junk($item['subtotal'])) . '</td>
        </tr>';
    }
} else {
    $html .= '
    <tr>
        <td colspan="4" style="font-size: 10px;">No hay productos en esta cotización.</td>
    </tr>';
}

$html .= '
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="font-size: 10px;">Total</th>
            <th style="font-size: 10px;">L' .number_format(remove_junk($quotation['total'])) . '</th>
        </tr>
    </tfoot>
</table>

<!-- Notas -->
<p><strong>Notas:</strong></p>
<p>1. Esta cotización es válida por 30 días a partir de la fecha de emisión.</p>';

// Escribir el contenido en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean(); // Limpia el buffer de salida antes de generar el PDF

// Generar el PDF y descargarlo
$pdf->Output('cotizacion_' . $quotation['id'] . '.pdf', 'I');
?>
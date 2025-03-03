<?php
require_once('includes/load.php');

if (isset($_POST['search'])) {
    $search = remove_junk($db->escape($_POST['search']));
    $sql = "SELECT p.*, c.name AS categorie, m.file_name AS image 
            FROM products p 
            LEFT JOIN categories c ON c.id = p.categorie_id 
            LEFT JOIN media m ON m.id = p.media_id";
    
    if ($search !== '') {
        $sql .= " WHERE p.name LIKE '%{$search}%' OR p.codigo LIKE '%{$search}%'";
    }

    $result = $db->query($sql);

    if ($result && $db->num_rows($result) > 0) {
        while ($product = $db->fetch_assoc($result)) {
            echo '
            <tr id="product-' . $product['id'] . '" class="' . ($product['quantity'] <= 5 ? 'low-stock' : '') . '">
                <td class="text-center">' . count_id() . '</td>
                <td>' . remove_junk($product['codigo']) . '</td>
                <td>' . remove_junk($product['name']) . '</td>
                <td class="text-center">' . remove_junk($product['categorie']) . '</td>
                <td class="text-center">' . remove_junk($product['quantity']) . '</td>
                <td class="text-center">' . remove_junk($product['ubicacion']) . '</td>
                <td class="text-center">' . remove_junk($product['sale_price']) . '</td>
                <td class="text-center">';
            if ($product['media_id'] === '0') {
                echo '<img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">';
            } else {
                echo '<img class="img-avatar img-circle" src="uploads/products/' . $product['image'] . '" alt="">';
            }
            echo '</td>
                <td class="text-center">' . read_date($product['date']) . '</td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="edit_product.php?id=' . (int)$product['id'] . '" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_product.php?id=' . (int)$product['id'] . '" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este producto?\');">
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    </div>
                </td>
            </tr>';
        }
    } else {
        echo '<tr><td colspan="10" class="text-center">No se encontraron productos.</td></tr>';
    }
}
?>
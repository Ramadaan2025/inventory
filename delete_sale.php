<?php
require_once('includes/load.php');
page_require_level(3);

$saleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$sale = $saleId ? find_by_id('sales', $saleId) : null;
if (!$sale) {
    $session->msg('d', 'Missing sale id.');
    redirect('sales.php');
}

$productId = (int)$sale['product_id'];
$qty = (int)$sale['qty'];
$db->begin_transaction();
try {
    $restore = $db->query("UPDATE products SET quantity = quantity + {$qty} WHERE id = {$productId}");
    if (!$restore || $db->affected_rows() !== 1) {
        throw new RuntimeException('Could not restore stock.');
    }
    $deleted = delete_by_id('sales', (int)$sale['id']);
    if (!$deleted) {
        throw new RuntimeException('Could not delete sale.');
    }
    $db->commit();
    $session->msg('s', 'Sale deleted and stock restored.');
} catch (Throwable $e) {
    $db->rollback();
    $session->msg('d', 'Sale deletion failed; no inventory changes were saved.');
}
redirect('sales.php');

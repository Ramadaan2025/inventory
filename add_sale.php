<?php
$page_title = 'Add Sale';
require_once('includes/load.php');
page_require_level(3);

if (isset($_POST['add_sale'])) {
    validate_fields(array('s_id', 'quantity', 'total', 'date'));

    $product_id = filter_input(INPUT_POST, 's_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $total = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_FLOAT);
    $date = $_POST['date'] ?? '';

    if (!$product_id || !$quantity || $quantity < 1 || $total === false || $total < 0 ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $session->msg('d', 'Invalid product, quantity, total, or date.');
        redirect('add_sale.php', false);
    }

    $product = find_by_id('products', $product_id);
    if (!$product || (int)$product['quantity'] < $quantity) {
        $session->msg('d', 'Product not found or insufficient stock.');
        redirect('add_sale.php', false);
    }

    $db->begin_transaction();
    try {
        $pid = (int)$product_id;
        $qty = (int)$quantity;
        $totalValue = (float)$total;
        $safeDate = $db->escape($date);
        $insert = $db->query("INSERT INTO sales (product_id, qty, price, date) VALUES ({$pid}, {$qty}, {$totalValue}, '{$safeDate}')");
        $stock = $db->query("UPDATE products SET quantity = quantity - {$qty} WHERE id = {$pid} AND quantity >= {$qty}");
        if (!$insert || !$stock || $db->affected_rows() !== 1) {
            throw new RuntimeException('Sale or stock update failed.');
        }
        $db->commit();
        $session->msg('s', 'Sale added.');
    } catch (Throwable $e) {
        $db->rollback();
        $session->msg('d', 'Sale was not saved. Please check stock and try again.');
    }
    redirect('add_sale.php', false);
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
    <form method="post" action="ajax.php" autocomplete="off" id="sug-form">
      <div class="form-group">
        <div class="input-group">
          <input type="text" id="sug_input" class="form-control" name="title" placeholder="Search for product name">
          <span class="input-group-btn"><button type="submit" class="btn btn-primary">Find It</button></span>
        </div>
        <div id="result" class="list-group"></div>
      </div>
    </form>
  </div>
</div>
<div class="row"><div class="col-md-12"><div class="panel panel-default">
<div class="panel-heading clearfix"><strong><span class="glyphicon glyphicon-th"></span><span>Sale Edit</span></strong></div>
<div class="panel-body"><form method="post" action="add_sale.php"><table class="table table-bordered">
<thead><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th>Date</th><th>Action</th></thead>
<tbody id="product_info"></tbody></table></form></div></div></div></div>
<?php include_once('layouts/footer.php'); ?>

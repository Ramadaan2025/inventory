<?php
  $page_title = 'Edit sale';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php
$sale = find_by_id('sales',(int)$_GET['id']);
if(!$sale){
  $session->msg("d","Missing product id.");
  redirect('sales.php');
}
?>
<?php $product = find_by_id('products',$sale['product_id']); ?>
<?php

  if (isset($_POST['update_sale'])) {
    validate_fields(array('quantity', 'total', 'date'));
    $newQty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $newTotal = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_FLOAT);
    $newDate = $_POST['date'] ?? '';

    if (!$newQty || $newQty < 1 || $newTotal === false || $newTotal < 0 ||
        !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $newDate)) {
        $session->msg('d', 'Invalid quantity, total, or date.');
        redirect('edit_sale.php?id=' . (int)$sale['id'], false);
    }

    $productId = (int)$sale['product_id'];
    $oldQty = (int)$sale['qty'];
    $newQty = (int)$newQty;
    $safeDate = $db->escape($newDate);
    $db->begin_transaction();
    try {
        // Restore the old sale quantity, then reserve the replacement quantity.
        $restore = $db->query("UPDATE products SET quantity = quantity + {$oldQty} WHERE id = {$productId}");
        $deduct = $db->query("UPDATE products SET quantity = quantity - {$newQty} WHERE id = {$productId} AND quantity >= {$newQty}");
        if (!$restore || !$deduct || $db->affected_rows() !== 1) {
            throw new RuntimeException('Stock adjustment failed.');
        }
        $update = $db->query("UPDATE sales SET qty = {$newQty}, price = " . (float)$newTotal . ", date = '{$safeDate}' WHERE id = " . (int)$sale['id']);
        if (!$update) {
            throw new RuntimeException('Sale update failed.');
        }
        $db->commit();
        $session->msg('s', 'Sale updated.');
    } catch (Throwable $e) {
        $db->rollback();
        $session->msg('d', 'Sale was not updated; inventory was rolled back.');
    }
    redirect('sales.php', false);
  }
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">

  <div class="col-md-12">
  <div class="panel">
    <div class="panel-heading clearfix">
      <strong>
        <span class="glyphicon glyphicon-th"></span>
        <span>All Sales</span>
     </strong>
     <div class="pull-right">
       <a href="sales.php" class="btn btn-primary">Show all sales</a>
     </div>
    </div>
    <div class="panel-body">
       <table class="table table-bordered">
         <thead>
          <th> Product title </th>
          <th> Qty </th>
          <th> Price </th>
          <th> Total </th>
          <th> Date</th>
          <th> Action</th>
         </thead>
           <tbody  id="product_info">
              <tr>
              <form method="post" action="edit_sale.php?id=<?php echo (int)$sale['id']; ?>">
                <td id="s_name">
                  <input type="text" class="form-control" id="sug_input" name="title" value="<?php echo remove_junk($product['name']); ?>">
                  <div id="result" class="list-group"></div>
                </td>
                <td id="s_qty">
                  <input type="text" class="form-control" name="quantity" value="<?php echo (int)$sale['qty']; ?>">
                </td>
                <td id="s_price">
                  <input type="text" class="form-control" name="price" value="<?php echo remove_junk($product['sale_price']); ?>" >
                </td>
                <td>
                  <input type="text" class="form-control" name="total" value="<?php echo remove_junk($sale['price']); ?>">
                </td>
                <td id="s_date">
                  <input type="date" class="form-control datepicker" name="date" data-date-format="" value="<?php echo remove_junk($sale['date']); ?>">
                </td>
                <td>
                  <button type="submit" name="update_sale" class="btn btn-primary">Update sale</button>
                </td>
              </form>
              </tr>
           </tbody>
       </table>

    </div>
  </div>
  </div>

</div>

<?php include_once('layouts/footer.php'); ?>

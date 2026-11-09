<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  $target_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  $current = current_user();
  if (!$target_id || !$current || (int)$target_id === (int)$current['id']) {
      $session->msg('d', 'Invalid user or you cannot delete your own account.');
      redirect('users.php');
  }
  $delete_id = delete_by_id('users',(int)$target_id);
  if($delete_id){
      $session->msg("s","User deleted.");
      redirect('users.php');
  } else {
      $session->msg("d","User deletion failed Or Missing Prm.");
      redirect('users.php');
  }
?>

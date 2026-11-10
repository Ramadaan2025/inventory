<?php
// Function to check if a user with the same name already exists
function user_name_exists($username) {
    global $db;
    $sql = "SELECT id FROM users WHERE username = '{$db->escape($username)}' LIMIT 1";
    $result = $db->query($sql);
    return $db->num_rows($result) === 1;
}

$page_title = 'Add User';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(1);
$groups = find_all('user_groups');

// Check if the form has been submitted
if (isset($_POST['add_user'])) {
    $req_fields = array('full-name', 'username', 'password', 'confirm-password', 'level');
    validate_fields($req_fields);

    if (empty($errors)) {
        $name           = remove_junk($db->escape($_POST['full-name']));
        $username       = remove_junk($db->escape($_POST['username']));
        $password       = (string)($_POST['password'] ?? '');
        $confirm_pass   = (string)($_POST['confirm-password'] ?? '');
        $user_level     = (int)$db->escape($_POST['level']);
        $password_hash  = password_hash($password, PASSWORD_DEFAULT);

        // Check if a user with the same name already exists
        if (user_name_exists($username)) {
            $session->msg('d', 'User with the same username already exists!');
            redirect('add_user.php', false);
        }

        // Check if the password and confirmation password match
        if (!hash_equals($password, $confirm_pass)) {
            $session->msg('d', 'Password and confirmation password do not match!');
            redirect('add_user.php', false);
        }

        $query = "INSERT INTO users (";
        $query .= "name, username, password, user_level, status";
        $query .= ") VALUES (";
        $query .= " '{$name}', '{$username}', '{$db->escape($password_hash)}', '{$user_level}', '1'";
        $query .= ")";

        if ($db->query($query)) {
            // Success
            $session->msg('s', 'User account has been created!');
            redirect('add_user.php', false);
        } else {
            // Failed
            $session->msg('d', 'Sorry, failed to create account!');
            redirect('add_user.php', false);
        }
    } else {
      $session->msg('d', 'User with the same name already exists!');
      redirect('add_user.php', false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<?php echo display_msg($msg); ?>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>
                <span class="glyphicon glyphicon-th"></span>
                <span>Add New User</span>
            </strong>
        </div>
        <div class="panel-body">
            <div class="col-md-6">
                <form method="post" action="add_user.php">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="full-name" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm-password" placeholder="Confirm Password">
                    </div>
                    <div class="form-group">
                        <label for="level">User Role</label>
                        <select class="form-control" name="level">
                            <?php foreach ($groups as $group) : ?>
                                <option value="<?php echo $group['group_level']; ?>"><?php echo ucwords($group['group_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group clearfix">
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

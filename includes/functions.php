<?php
 $errors = array();

 /*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
function real_escape($str){
  global $con;
  $escape = mysqli_real_escape_string($con,$str);
  return $escape;
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str){
  if ($str === null) {
    return '';
  }
  return htmlspecialchars(strip_tags((string)$str), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str){
  $val = str_replace('-'," ",$str);
  $val = ucfirst($val);
  return $val;
}
/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = trim((string)($_POST[$field] ?? ''));
    if ($val === '') {
      $errors = $field . " can't be blank.";
      return $errors;
    }
  }
  return true;
}
/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg =''){
   $output = '';
   if(!empty($msg)) {
      foreach ($msg as $key => $value) {
         $output .= "<div class=\"alert alert-{$key}\">";
         $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
         $output .= remove_junk(first_character(is_array($value) ? implode(', ', $value) : $value));
         $output .= "</div>";
      }
      return $output;
   } else {
     return "" ;
   }
}
/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
    if (headers_sent() === false)
    {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }

    exit();
}
/*--------------------------------------------------------------*/
/* Function for find out total saleing price, buying price and profit
/*--------------------------------------------------------------*/
function total_price($totals){
   $sum = 0;
   $sub = 0;
   foreach($totals as $total ){
     $sum += $total['total_saleing_price'];
     $sub += $total['total_buying_price'];
     $profit = $sum - $sub;
   }
   return array($sum,$profit);
}
/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str){
     if($str)
      return date('F j, Y, g:i:s a', strtotime($str));
     else
      return null;
  }
/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date(){
  return date('Y-m-d H:i:s');
}
/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id(){
  static $count = 1;
  return $count++;
}
/*--------------------------------------------------------------*/
/* Function for Creting random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0, strlen($cha) - 1)];
  return $str;
}

function create_customer($username, $password, $email) {
  global $db;

  // Hash the password for security (you should use a better hashing method in production)
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Escape input to prevent SQL injection
  $username = $db->escape($username);
  $email = $db->escape($email);

  // Check if the username or email already exists in the database
  $query = "SELECT * FROM customers WHERE username = '{$username}' OR email = '{$email}'";
  $result = $db->query($query);

  if ($db->num_rows($result) > 0) {
      // Username or email already exists, return false to indicate failure
      return false;
  } else {
      // Insert the new customer into the database
      $query = "INSERT INTO customers (username, password, email) VALUES ('{$username}', '{$hashed_password}', '{$email}')";
      $insert_result = $db->query($query);

      if ($insert_result) {
          // Customer created successfully, return true
          return true;
      } else {
          // Failed to insert the customer, return false to indicate failure
          return false;
      }
  }
}

function get_customer_info($user_id) {
  global $db;

  // Escape the user_id to prevent SQL injection
  $user_id = $db->escape($user_id);

  // Query to retrieve customer information based on the user ID
  $query = "SELECT id, username, email, name, phone_number, address FROM customers WHERE id = {$user_id} LIMIT 1";
  $result = $db->query($query);

  if ($db->num_Rows($result) > 0) {
      // Fetch the customer information as an associative array
      $customer_info = $db->fetch_Assoc($result);
      return $customer_info;
  } else {
      // Customer not found, return false
      return false; 
    }
    
}
function find_user_by_name($name) {
  global $db;
  $sql = "SELECT * FROM users WHERE name='{$name}' LIMIT 1";
  $result_set = $db->query($sql);
  if ($db->num_rows($result_set) > 0) {
      return $db->fetch_assoc($result_set);
  } else {
      return null;
  }
}
// Function to find a driver by name
/*function find_driver_by_name($name) {
  global $db;
  $sql = "SELECT * FROM drivers WHERE name = '{$name}' LIMIT 1";
  $result = find_by_sql($sql);
  return $result ? array_shift($result) : null;
}
*/





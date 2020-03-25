<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);

?>

<h2>Welcome, <?php echo $user->user_fullname; ?></h2>
<br>
<a href="logout.php">Logout</a>
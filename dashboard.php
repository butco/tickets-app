<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);

include "includes/header.php";
?>
<div class="container-fluid container-bg">
    <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-1 sidebar top-fixed">
            <h2 class="text-white">Welcome, <?php echo $user->user_fullname; ?></h2>
            <br>
            <a href="logout.php">Logout</a>
        </div>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1"></div>
    </div>
</div>
<?php include "includes/footer.php";?>
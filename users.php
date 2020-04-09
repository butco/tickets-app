<?php
require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$allActiveUsers = $users->GetAllUsers("user_is_active", 1);
$allInactiveUsers = $users->GetAllUsers("user_is_active", 2);
include "includes/header.php";
?>
<?php if (isset($_SESSION["msg_error"]) && !empty($_SESSION["msg_error"])): ?>
<div class="alert alert-danger dashboard-alert" role="alert">
    <?php echo $_SESSION["msg_error"];unset($_SESSION["msg_error"]); ?>
</div>
<?php endif;?>
<?php if (isset($_SESSION["msg_success"]) && !empty($_SESSION["msg_success"])): ?>
<div class="alert alert-success dashboard-alert" role="alert">
    <?php echo $_SESSION["msg_success"];unset($_SESSION["msg_success"]); ?>
</div>
<?php endif;?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="page-title">Users</div>
            <div class="row row-users">
                <div class="act-users">
                    <a href="add-user.php" class="users-add-new-link">
                        <div class="card add-new">
                            <div class="card-body">
                                <i class="fas fa-plus"></i>
                                <p>Add New User</p>
                            </div>
                        </div>
                    </a>
                    <?php foreach ($allActiveUsers as $activeUser): ?>
                    <a href="profile.php?profile=<?php echo $activeUser->id; ?>" class="active-user-link">
                        <div class="card active-user">
                            <div class="card-body">
                                <img src="<?php echo $activeUser->user_photo; ?>" alt="" srcset="">
                                <div class="active-user-name"><?php echo $activeUser->user_fullname; ?></div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach;?>
                </div>
                <?php if (!empty($allInactiveUsers)): ?>
                <div class="page-title">Inactive Users</div>
                <div class="inact-users">
                    <?php foreach ($allInactiveUsers as $inactiveUser): ?>
                    <a href="profile.php?profile=<?php echo $inactiveUser->id; ?>" class="inactive-user-link">
                        <div class="card inactive-user">
                            <div class="card-body">
                                <img src="<?php echo $inactiveUser->user_photo; ?>" alt="" srcset="">
                                <div class="inactive-user-name"><?php echo $inactiveUser->user_fullname; ?></div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach;?>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php";?>
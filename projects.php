<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$allProjects = $projects->GetAllProjects();
//if the logged in user is not administrator
//then the user does not have access on this page
// if ($user->user_group_id !== 1) {
//     $_SESSION["msg_error"] = "You don't have permission to access the URL!";
//     header("location:dashboard.php");
//     exit;
// }
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
            <div class="page-title">Projects</div>
            <div class="row row-projects">
                <a href="add-project.php" class="card projects-add-new">
                    <div class="card-body">
                        <i class="fas fa-plus"></i>
                        <p>Add New Project</p>
                    </div>
                </a>
                <?php foreach ($allProjects as $project): ?>
                <a href="project.php?proj_id=<?php echo $project->id; ?>" class="card projects">
                    <div class="card-body">
                        <img src="<?php echo $project->proj_logo; ?>" alt="" srcset="">
                        <div class="proj-name"><?php echo $project->proj_name; ?></div>
                        <div class="proj-company"><?php echo $project->proj_company; ?></div>
                    </div>
                </a>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php";?>
<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$myProjects = $projects->GetProjectsByUser($user->id);

include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <?php if (isset($_SESSION["edit_ticket_error"]) && !empty($_SESSION["edit_ticket_error"])): ?>
            <div class="alert alert-danger dashboard-alert" role="alert">
                <?php echo $_SESSION["edit_ticket_error"];unset($_SESSION["edit_ticket_error"]); ?>
            </div>
            <?php endif;?>
            <?php if (isset($_SESSION["msg_error"]) && !empty($_SESSION["msg_error"])): ?>
            <div class="alert alert-danger dashboard-alert" role="alert">
                <?php echo $_SESSION["msg_error"];unset($_SESSION["msg_error"]); ?>
            </div>
            <?php endif;?>
            <div class="page-title">My Projects</div>
            <?php if (empty($myProjects)): ?>
            <div class="alert alert-info mt-3" role="alert">You are not assigned on any of the projects!</div>
            <?php endif;?>
            <div class="row row-projects">
                <?php foreach ($myProjects as $project): ?>
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
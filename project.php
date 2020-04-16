<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
$edit_errors = "";
$edit_success = "";
$profilePhotoName = "";
$profilePhoto = "images/projects/project_placeholder.png";
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$project = $projects->ProjectDetails($_GET["proj_id"]);
$usersOnProject = $projects->GetAssignedUsers($project->id);
$unassignedUsers = $projects->GetUnassignedUsers($project->id);

//Assign user to project
if (isset($_GET["assign"]) && !empty($_GET["assign"])) {
    if ($projects->AssignUserToProject($project->id, $_GET["assign"])) {
        $edit_success = "User assigned successfully!";
        header("location:project.php?proj_id=" . $project->id);
    } else {
        $edit_errors = "Couldn't assign user to this project!";
    }
}

//Unassign user from project
if (isset($_GET["unassign"]) && !empty($_GET["unassign"])) {
    if ($projects->UnassignUserFromProject($project->id, $_GET["unassign"])) {
        $edit_success = "User unassigned successfully!";
        header("location:project.php?proj_id=" . $project->id);
    } else {
        $edit_errors = "Couldn't unassign user from this project!";
    }
}

//Update button is pressed
if (isset($_POST['btnUpdate'])) {
    if (empty($_POST['inputProjName']) || empty($_POST['inputCompany'])) {
        $edit_errors = "Please fill in all the fields!";
    } else {
        $projName = trim($_POST['inputProjName']);
        $projCompany = trim($_POST['inputCompany']);

        if (!empty($_FILES['profileImage']['name'])) {
            $profilePhotoName = $_FILES['profileImage']['name'];
            $target = "images/projects/" . $profilePhotoName;
            $imageFileType = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));
            // Check file size
            if ($_FILES['profileImage']['size'] > 500000) {
                $edit_errors = "Sorry, your file is too large!";
            } // Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                $edit_errors = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                if (!file_exists($target)) {
                    move_uploaded_file($_FILES['profileImage']['tmp_name'], $target);
                }
                $profilePhoto = $target;
            }
        } else {
            $profilePhoto = $project->proj_logo;
        }

        //do the insert in the DB
        if ($projects->UpdateProject($project->id, $projName, $projCompany, $profilePhoto)) {
            $edit_success = "Project was updated successfully!";
            $_SESSION["msg_error"] = $edit_errors;
            $_SESSION["msg_success"] = $edit_success;
            header("location:projects.php");
        }
    }
}

//DELETE button is pressed
if (isset($_POST["btnDelete"])) {
    if ($projects->DeleteProject($project->id)) {
        $_SESSION["msg_success"] = "The project <strong>" . $project->proj_name . "</strong> was deleted!";
    } else {
        $_SESSION["msg_error"] = "The project <strong>" . $project->proj_name . "</strong> was not deleted!";
    }
    header("location:projects.php");
}

include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <?php if ($edit_errors != ""): ?>
            <div class="alert alert-danger" role="alert">
                <strong><?=$edit_errors;?></strong>
            </div>
            <?php endif;?>
            <?php if ($edit_success != ""): ?>
            <div class="alert alert-success" role="alert">
                <strong><?=$edit_success;?></strong>
            </div>
            <?php endif;?>
            <div class="row add-project">
                <div class="col-12 m-auto">
                    <?php if (!empty($usersOnProject)): ?>
                    <div class="card card-assigned-users">
                        <div class="card-title text-center mt-3 mb-0">
                            <h4>Assigned Users</h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($usersOnProject as $singleUser): ?>
                            <div class="card users">
                                <div class="card-body">
                                    <a href="profile.php?profile=<?php echo $singleUser->id; ?>">
                                        <img src="<?php echo $singleUser->user_photo; ?>" alt="" srcset="">
                                        <div class="user-fullname"><?php echo $singleUser->user_fullname; ?></div>
                                    </a>
                                    <div class="overlay">
                                        <a
                                            href="project.php?proj_id=<?php echo $project->id; ?>&unassign=<?php echo $singleUser->id; ?>"><i
                                                class="fas fa-trash-alt unassign-user"></i></a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <?php endif;?>
                    <?php if (!empty($unassignedUsers)): ?>
                    <div class="card card-assigned-users">
                        <div class="card-title text-center mt-3 mb-0">
                            <h4>Active Users</h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($unassignedUsers as $unassignedUser): ?>
                            <div class="card users">
                                <div class="card-body">
                                    <a href="profile.php?profile=<?php echo $unassignedUser->id; ?>">
                                        <img src="<?php echo $unassignedUser->user_photo; ?>" alt="" srcset="">
                                        <div class="user-fullname"><?php echo $unassignedUser->user_fullname; ?></div>
                                    </a>
                                    <div class="overlay">
                                        <a
                                            href="project.php?proj_id=<?php echo $project->id; ?>&assign=<?php echo $unassignedUser->id; ?>"><i
                                                class="fas fa-check assign-user"></i></a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
                <div class="col-12 m-auto">
                    <div class="card project-details-card">
                        <div class="card-body">
                            <form action="project.php?proj_id=<?php echo $project->id; ?>" method="POST"
                                enctype="multipart/form-data">
                                <div class="project-logo">
                                    <div class="form-group">
                                        <img src="<?php echo (isset($_POST["profileImage"]) ? $_POST["profileImage"] : $project->proj_logo); ?>"
                                            alt="" id="profilePhoto" class="card-img-top" onclick="triggerClick()">
                                        <input type="file" name="profileImage" onchange="previewProfilePhoto(this)"
                                            id="profileImage" style="display:none;">
                                    </div>
                                </div>
                                <div class="proj-inputs">
                                    <div class="form-group">
                                        <label for="projNameInput">Project Name</label>
                                        <input type="text" class="form-control" id="projNameInput" autocomplete="off"
                                            autofocus name="inputProjName"
                                            value="<?php echo (isset($_POST["inputProjName"]) ? $_POST["inputProjName"] : $project->proj_name); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="companyInput">Company</label>
                                        <input type="text" class="form-control" id="companyInput" autocomplete="off"
                                            name="inputCompany"
                                            value="<?php echo (isset($_POST["inputCompany"]) ? $_POST["inputCompany"] : $project->proj_company); ?>">
                                    </div>
                                    <div class="buttons">
                                        <button type="submit" class="btn btn-secondary btn-block btn-update"
                                            name="btnUpdate">UPDATE DETAILS</button>
                                        <button type="submit" class="btn btn-secondary btn-block btn-delete"
                                            name="btnDelete">DELETE PROJECT</button>
                                    </div>
                                    <div class="copyright">2020 &copy; <a href="https://www.ButcoSoft.com"
                                            class="copy-link">ButcoSoft</a>. All
                                        rights reserved.</div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php include "includes/footer.php";?>
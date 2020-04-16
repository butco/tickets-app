<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
$add_errors = "";
$add_success = "";
$profilePhotoName = "";
$profilePhoto = "images/projects/project_placeholder.png";
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);

//Add button is pressed
if (isset($_POST['btnAdd'])) {
    if (empty($_POST['inputProjName']) || empty($_POST['inputCompany'])) {
        $add_errors = "Please fill in all the fields!";
    } else {
        $projName = trim($_POST['inputProjName']);
        $projCompany = trim($_POST['inputCompany']);

        if (!empty($_FILES['profileImage']['name'])) {
            $profilePhotoName = $_FILES['profileImage']['name'];
            $target = "images/projects/" . $profilePhotoName;
            $imageFileType = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));
            // Check file size
            if ($_FILES['profileImage']['size'] > 500000) {
                $add_errors = "Sorry, your file is too large!";
            } // Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                $add_errors = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                if (!file_exists($target)) {
                    move_uploaded_file($_FILES['profileImage']['tmp_name'], $target);
                }
                $profilePhoto = $target;
            }
        }

        //do the insert in the DB
        if ($projects->AddNew($projName, $projCompany, $profilePhoto)) {
            $add_success = "Project was created successfully!";
            $_SESSION["msg_error"] = $add_errors;
            $_SESSION["msg_success"] = $add_success;
            header("location:projects.php");
        }
    }
}

include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="row add-project">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card add-project-card m-auto">
                        <div class="card-title text-center mt-5">
                            <h3>Add New Project</h3>
                        </div>
                        <div class="card-body">
                            <form action="add-project.php" method="POST" enctype="multipart/form-data">
                                <?php if ($add_errors != ""): ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong><?=$add_errors;?></strong>
                                </div>
                                <?php endif;?>
                                <?php if ($add_success != ""): ?>
                                <div class="alert alert-success" role="alert">
                                    <strong><?=$add_success;?></strong>
                                </div>
                                <?php endif;?>
                                <div class="add-project-logo">
                                    <div class="form-group">
                                        <img src="images/projects/project_placeholder.png" alt="" id="profilePhoto"
                                            class="card-img-top mb-3" onclick="triggerClick()">
                                        <input type="file" name="profileImage" onchange="previewProfilePhoto(this)"
                                            id="profileImage" style="display:none;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="projNameInput">Project Name</label>
                                    <input type="text" class="form-control" id="projNameInput" autocomplete="off"
                                        autofocus name="inputProjName"
                                        value="<?php echo (isset($_POST["inputProjName"]) ? $_POST["inputProjName"] : ""); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="companyInput">Company</label>
                                    <input type="text" class="form-control" id="companyInput" autocomplete="off"
                                        name="inputCompany"
                                        value="<?php echo (isset($_POST["inputCompany"]) ? $_POST["inputCompany"] : ""); ?>">
                                </div>
                                <button type="submit" class="btn btn-secondary btn-block btn-add" name="btnAdd">Add
                                    Project</button>
                                <div class="copyright">2020 &copy; <a href="https://www.ButcoSoft.com"
                                        class="copy-link">ButcoSoft</a>. All
                                    rights reserved.</div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php";?>
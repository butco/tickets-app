<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$users_groups = $users->UsersGroups();
$user_active = "";
$edit_errors = "";
$edit_success = "";
$css_class = "";
$profilePhotoName = "";
$newEmail = "";
$newFullName = "";
$newPassword = "";
$newProfilePhoto = "";
$newGroupId = "";
$newActive = "";

//check if user is active
if ($user->user_is_active == 1) {
    $user_active = "YES";
} else if ($user->user_is_active == 2) {
    $user_active = "NO";
}
//Update button is pressed
if (isset($_POST['btnUpdate'])) {
    $newPassword = trim($_POST['inputPassword']);

    //check if values changed
    if (empty($_POST['inputFullName']) || empty($_POST['inputEmail'])) {
        $edit_errors .= "Please fill in your full name and email address!<br>";
        $css_class = "alert-danger";
    } else {
        if (trim($_POST['inputFullName']) !== $user->user_fullname) {
            $newFullName = trim($_POST['inputFullName']);
        } else {
            $newFullName = $user->user_fullname;
        }
        if (trim($_POST['inputEmail']) !== $user->user_email) {
            $newEmail = trim($_POST['inputEmail']);
        } else {
            $newEmail = $user->user_email;
        }
        if (empty($_POST['inputPassword'])) {
            $newPassword = $user->user_password;
        } else {
            $newPassword = password_hash($_POST['inputPassword'], PASSWORD_DEFAULT);
        }
        if (!empty($_FILES['profileImage']['name'])) {
            $profilePhotoName = $_FILES['profileImage']['name'];
            $target = "images/users/" . $profilePhotoName;
            $imageFileType = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));
            // Check file size
            if ($_FILES['profileImage']['size'] > 500000) {
                $edit_errors .= "Sorry, your file is too large!<br>";
                $css_class = "alert-danger";
                $newProfilePhoto = $user->user_photo;
            } // Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                $edit_errors .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
                $css_class = "alert-danger";
                $newProfilePhoto = $user->user_photo;
            } else {
                if (!file_exists($target)) {
                    move_uploaded_file($_FILES['profileImage']['tmp_name'], $target);
                }
                $newProfilePhoto = $target;
            }
        } else {
            $newProfilePhoto = $user->user_photo;
        }
        if ($user->user_group_id == 1) {
            $newGroupId = $_POST['usersGroupsSelect'];
            $newActive = $_POST['usersActiveSelect'];
        }
        //do the update in the DB
        if ($user->user_group_id == 1) {
            if ($users->UpdateByAdmins($user->id, $newEmail, $newPassword, $newFullName, $newProfilePhoto, $newGroupId, $newActive)) {
                $edit_success = "Update successfully!<br>";
                $_SESSION["msg_error"] = $edit_errors;
                $_SESSION["msg_success"] = $edit_success;
                header("location:dashboard.php");
            } else {
                $edit_errors .= "No changes done!<br>";
                $css_class = "alert-danger";
            }
        } else {
            if ($users->UpdateByUsers($user->id, $newEmail, $newPassword, $newFullName, $newProfilePhoto)) {
                $edit_success = "Update successfully!<br>";
                $_SESSION["msg_error"] = $edit_errors;
                $_SESSION["msg"] = $edit_success;
                header("location:dashboard.php");
            } else {
                $edit_errors .= "No changes done!<br>";
                $css_class = "alert-danger";
            }
        }
    }
}
include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">

        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="row edit-profile">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card edit-profile-card m-auto">
                        <div class="card-title text-center mt-5">
                            <h3>Update Details</h3>
                        </div>
                        <div class="card-body">
                            <form action="profile.php" method="POST" enctype="multipart/form-data">
                                <?php if ($edit_errors != ""): ?>
                                <div class="alert <?php echo $css_class; ?>" role="alert">
                                    <strong><?=$edit_errors;?></strong>
                                </div>
                                <?php endif;?>
                                <?php if ($edit_success != ""): ?>
                                <div class="alert alert-success" role="alert">
                                    <strong><?=$edit_success;?></strong>
                                </div>
                                <?php endif;?>
                                <div class="edit-profile-photo">
                                    <div class="form-group">
                                        <img src="<?php echo $user->user_photo; ?>" alt="" id="profilePhoto"
                                            class="card-img-top mb-3" onclick="triggerClick()">
                                        <input type="file" name="profileImage" onchange="previewProfilePhoto(this)"
                                            id="profileImage" style="display:none;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fullNameInput">Full Name</label>
                                    <input type="text" class="form-control" id="fullNameInput" autocomplete="off"
                                        autofocus name="inputFullName"
                                        value="<?php echo (isset($_POST["inputFullName"]) ? $_POST["inputFullName"] : $user->user_fullname); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="emailInput">Email address</label>
                                    <input type="email" class="form-control" id="emailInput" autocomplete="off"
                                        name="inputEmail" aria-describedby="emailHelp"
                                        value="<?php echo (isset($_POST["inputEmail"]) ? $_POST["inputEmail"] : $user->user_email); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="passInput">Password</label>
                                    <input type="password" class="form-control" name="inputPassword" id="passInput">
                                </div>
                                <?php if ($user->user_group_id == 1): ?>
                                <div class="form-group">
                                    <label for="usersGroupsSelect">Group</label>
                                    <select name="usersGroupsSelect" class="form-control">
                                        <option
                                            value="<?php echo (isset($_POST["usersGroupsSelect"]) ? $_POST["usersGroupsSelect"] : $user->user_group_id); ?>">
                                            <?php echo $users->UsersGroupsByID(isset($_POST["usersGroupsSelect"]) ? $_POST["usersGroupsSelect"] : $user->user_group_id)->group_name; ?>
                                            (Now)
                                        </option>
                                        <?php foreach ($users_groups as $group): ;?>
                                        <option value="<?php echo $group->group_id; ?>">
                                            <?php echo $group->group_name; ?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="usersActiveSelect">Active</label>
                                    <select name="usersActiveSelect" class="form-control">
                                        <option
                                            value="<?php echo (isset($_POST["usersActiveSelect"]) ? $_POST["usersActiveSelect"] : $user->user_is_active); ?>">
                                            <?php
if (isset($_POST["usersActiveSelect"]) && $_POST["usersActiveSelect"] == 1) {
    $user_active = "YES";
} else if (isset($_POST["usersActiveSelect"]) && $_POST["usersActiveSelect"] == 2) {
    $user_active = "NO";
}
echo $user_active;?>
                                            (Now)
                                        </option>
                                        <option value="1">YES</option>
                                        <option value="2">NO</option>
                                    </select>
                                </div>
                                <?php endif;?>

                                <button type="submit" class="btn btn-secondary btn-block btn-login"
                                    name="btnUpdate">UPDATE</button>
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
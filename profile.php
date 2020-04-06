<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
$user_active = "";
$edit_errors = "";
$edit_success = "";
// $css_class = "";
$profilePhotoName = "";
$newEmail = "";
$newFullName = "";
$newPassword = "";
$newProfilePhoto = "";
$newGroupId = "";
$newActive = "";
//Save all user details into $user object
$user = $users->UserDetails($_SESSION["user_id"]);
if (isset($_GET["profile"]) && !empty($_GET["profile"])) {
    $_SESSION["userToEdit"] = $_GET["profile"];
}
//if user is admin then has right to update any user
//if user is not admin then has right to update only own details
if (isset($_SESSION["userToEdit"])) {
    if ($users->UserIsAdmin($_SESSION["user_id"])) {
        $userToEdit = $users->UserDetails($_SESSION["userToEdit"]);
    } else {
        // $userToEdit = $users->UserDetails($_SESSION["user_id"]);
        $edit_errors = "You are not allowed to update other\'s profile!";
        $_SESSION["msg_error"] = $edit_errors;
        header("location:dashboard.php");
        exit;
    }
} else {
    $userToEdit = $users->UserDetails($_SESSION["user_id"]);
}

//check if user is active
if ($userToEdit->user_is_active == 1) {
    $user_active = "YES";
} else if ($userToEdit->user_is_active == 2) {
    $user_active = "NO";
}
$users_groups = $users->UsersGroups();

//Update button is pressed
if (isset($_POST['btnUpdate'])) {
    //check if values changed
    if (empty($_POST['inputFullName']) || empty($_POST['inputEmail'])) {
        $edit_errors = "Please fill in your full name and email address!";
    } else {
        if (trim($_POST['inputFullName']) !== $userToEdit->user_fullname) {
            $newFullName = trim($_POST['inputFullName']);
        } else {
            $newFullName = $userToEdit->user_fullname;
        }
        if (trim($_POST['inputEmail']) !== $userToEdit->user_email) {
            $newEmail = trim($_POST['inputEmail']);
        } else {
            $newEmail = $userToEdit->user_email;
        }
        if (empty($_POST['inputPassword'])) {
            $newPassword = $userToEdit->user_password;
        } else {
            $newPassword = password_hash($_POST['inputPassword'], PASSWORD_DEFAULT);
        }
        if (!empty($_FILES['profileImage']['name'])) {
            $profilePhotoName = $_FILES['profileImage']['name'];
            $target = "images/users/" . $profilePhotoName;
            $imageFileType = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));
            // Check file size
            if ($_FILES['profileImage']['size'] > 500000) {
                $edit_errors = "Sorry, your file is too large!";
                $newProfilePhoto = $userToEdit->user_photo;
            } // Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                $edit_errors = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $newProfilePhoto = $userToEdit->user_photo;
            } else {
                if (!file_exists($target)) {
                    move_uploaded_file($_FILES['profileImage']['tmp_name'], $target);
                }
                $newProfilePhoto = $target;
            }
        } else {
            $newProfilePhoto = $userToEdit->user_photo;
        }
        if ($user->user_group_id == 1) {
            $newGroupId = $_POST['usersGroupsSelect'];
            $newActive = $_POST['usersActiveSelect'];
        }
        //do the update in the DB
        if ($user->user_group_id == 1) {
            if ($users->UpdateByAdmins($userToEdit->id, $newEmail, $newPassword, $newFullName, $newProfilePhoto, $newGroupId, $newActive)) {
                $edit_success = "Update successfully!";
                $_SESSION["msg_error"] = $edit_errors;
                $_SESSION["msg_success"] = $edit_success;
                header("location:dashboard.php");
            } else {
                $edit_errors = "No changes done!";
            }
            unset($_SESSION["userToEdit"]);
        } else {
            if ($users->UpdateByUsers($userToEdit->id, $newEmail, $newPassword, $newFullName, $newProfilePhoto)) {
                $edit_success = "Update successfully!";
                $_SESSION["msg_error"] = $edit_errors;
                $_SESSION["msg_success"] = $edit_success;
                header("location:dashboard.php");
            } else {
                $edit_errors = "No changes done!";
            }
        }
    }
} else {
    unset($_SESSION["userToEdit"]);
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
                                <div class="alert alert-danger" role="alert">
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
                                        <img src="<?php echo $userToEdit->user_photo; ?>" alt="" id="profilePhoto"
                                            class="card-img-top mb-3" onclick="triggerClick()">
                                        <input type="file" name="profileImage" onchange="previewProfilePhoto(this)"
                                            id="profileImage" style="display:none;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fullNameInput">Full Name</label>
                                    <input type="text" class="form-control" id="fullNameInput" autocomplete="off"
                                        autofocus name="inputFullName"
                                        value="<?php echo (isset($_POST["inputFullName"]) ? $_POST["inputFullName"] : $userToEdit->user_fullname); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="emailInput">Email address</label>
                                    <input type="email" class="form-control" id="emailInput" autocomplete="off"
                                        name="inputEmail" aria-describedby="emailHelp"
                                        value="<?php echo (isset($_POST["inputEmail"]) ? $_POST["inputEmail"] : $userToEdit->user_email); ?>">
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
                                            value="<?php echo (isset($_POST["usersGroupsSelect"]) ? $_POST["usersGroupsSelect"] : $userToEdit->user_group_id); ?>">
                                            <?php echo $users->UsersGroupsByID(isset($_POST["usersGroupsSelect"]) ? $_POST["usersGroupsSelect"] : $userToEdit->user_group_id)->group_name; ?>
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
                                            value="<?php echo (isset($_POST["usersActiveSelect"]) ? $_POST["usersActiveSelect"] : $userToEdit->user_is_active); ?>">
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
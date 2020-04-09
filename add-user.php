<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
$user_active = "";
$add_errors = "";
$add_success = "";
$profilePhotoName = "";
$email = "";
$fullName = "";
$password = "";
$profilePhoto = "images/users/no_profile.png";
$groupId = "";
$active = "";
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$users_groups = $users->UsersGroups();

//Add button is pressed
if (isset($_POST['btnAdd'])) {
    if (empty($_POST['inputFullName']) || empty($_POST['inputEmail']) || empty($_POST['inputPassword'])) {
        $add_errors = "Please fill in all the fields!";
    } else {
        $fullName = trim($_POST['inputFullName']);
        $email = trim($_POST['inputEmail']);
        $password = password_hash($_POST['inputPassword'], PASSWORD_DEFAULT);

        if (!empty($_FILES['profileImage']['name'])) {
            $profilePhotoName = $_FILES['profileImage']['name'];
            $target = "images/users/" . $profilePhotoName;
            $imageFileType = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));
            // Check file size
            if ($_FILES['profileImage']['size'] > 500000) {
                $add_errors = "Sorry, your file is too large!";
                // $profilePhoto = "";
            } // Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                $add_errors = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                // $profilePhoto = "";
            } else {
                if (!file_exists($target)) {
                    move_uploaded_file($_FILES['profileImage']['tmp_name'], $target);
                }
                $profilePhoto = $target;
            }
        }
        // else {
        //     //if the admin doesn't upload a photo
        //     //then the photo will be the default set in the DB
        //     $profilePhoto = ;
        // }

        $groupId = $_POST['usersGroupsSelect'];
        $active = $_POST['usersActiveSelect'];

        //do the insert in the DB
        if ($users->AddUser($email, $password, $fullName, $profilePhoto, $groupId, $active)) {
            $add_success = "User was created successfully!";
            $_SESSION["msg_error"] = $add_errors;
            $_SESSION["msg_success"] = $add_success;
            header("location:users.php");
        }
    }
}

include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">

        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="row add-user">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card add-user-card m-auto">
                        <div class="card-title text-center mt-5">
                            <h3>Add New User</h3>
                        </div>
                        <div class="card-body">
                            <form action="add-user.php" method="POST" enctype="multipart/form-data">
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
                                <div class="add-user-photo">
                                    <div class="form-group">
                                        <img src="images/users/no_profile.png" alt="" id="profilePhoto"
                                            class="card-img-top mb-3" onclick="triggerClick()">
                                        <input type="file" name="profileImage" onchange="previewProfilePhoto(this)"
                                            id="profileImage" style="display:none;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fullNameInput">Full Name</label>
                                    <input type="text" class="form-control" id="fullNameInput" autocomplete="off"
                                        autofocus name="inputFullName"
                                        value="<?php echo (isset($_POST["inputFullName"]) ? $_POST["inputFullName"] : ""); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="emailInput">Email address</label>
                                    <input type="email" class="form-control" id="emailInput" autocomplete="off"
                                        name="inputEmail" aria-describedby="emailHelp"
                                        value="<?php echo (isset($_POST["inputEmail"]) ? $_POST["inputEmail"] : ""); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="passInput">Password</label>
                                    <input type="password" class="form-control" name="inputPassword" id="passInput">
                                </div>
                                <div class="form-group">
                                    <label for="usersGroupsSelect">Group</label>
                                    <select name="usersGroupsSelect" class="form-control">
                                        <?php foreach ($users_groups as $group): ;?>
                                        <option value="<?php echo $group->group_id; ?>">
                                            <?php echo $group->group_name; ?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="usersActiveSelect">Active</label>
                                    <select name="usersActiveSelect" class="form-control">
                                        <option value="1">YES</option>
                                        <option value="2">NO</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-secondary btn-block btn-add" name="btnAdd">Add
                                    User</button>
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
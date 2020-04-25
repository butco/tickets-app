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
//show only OPEN and IN_PROGRESS tickets
$statuses = array('status' => "'OPEN', 'IN_PROGRESS'");
$allTickets = $tickets->GetAllProjectsTickets($project->id, $statuses['status']);
//
$usersOnProject = $projects->GetAssignedUsers($project->id);
$unassignedUsers = $projects->GetUnassignedUsers($project->id);

//Start ticket
if (isset($_GET["start_ticket"]) && !empty($_GET["start_ticket"])) {
    if ($tickets->StartTicket($_GET["start_ticket"])) {
        $edit_success = "Ticket started successfully!";
        header("location:project.php?proj_id=" . $project->id);
    } else {
        $edit_errors = "Couldn't start the ticket!";
    }
}

//Close ticket
if (isset($_GET["close_ticket"]) && !empty($_GET["close_ticket"])) {
    if ($tickets->CloseTicket($_GET["close_ticket"])) {
        $edit_success = "Ticket closed successfully!";
        header("location:project.php?proj_id=" . $project->id);
    } else {
        $edit_errors = "Couldn't close the ticket!";
    }
}

//Delete ticket
if (isset($_GET["del_ticket"]) && !empty($_GET["del_ticket"])) {
    if ($tickets->DeleteTicket($_GET["del_ticket"])) {
        $edit_success = "Ticket deleted successfully!";
        header("location:project.php?proj_id=" . $project->id);
    } else {
        $edit_errors = "Couldn't delete the ticket!";
    }
}

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
            <div class="row tickets">
                <div class="col-12 m-auto">
                    <div class="card">
                        <div class="card-title text-center mt-3 mb-0">
                            <h4>Tickets</h4>
                        </div>
                        <div class="card-body">
                            <a href="add-ticket.php?proj_id=<?php echo $project->id; ?>" class="btn-new-ticket"><i
                                    class="far fa-plus-square"></i> New Ticket</a>
                            <div class="table-responsive-xl">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th scope="col">Actions</th>
                                            <th scope="col">#</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">User</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Started At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allTickets as $ticket): ?>
                                        <tr
                                            class="<?php echo ($ticket->status == "IN_PROGRESS") ? "table-success" : ""; ?>">
                                            <td>
                                                <?php if ($ticket->status == "OPEN"): ?>
                                                <a href="project.php?proj_id=<?php echo $ticket->project_id; ?>&start_ticket=<?php echo $ticket->id; ?>"
                                                    class="ticket-actions"><i class="fas fa-play start-ticket"></i></a>
                                                <?php elseif ($ticket->status == "IN_PROGRESS"): ?>
                                                <a href="project.php?proj_id=<?php echo $ticket->project_id; ?>&close_ticket=<?php echo $ticket->id; ?>"
                                                    class="ticket-actions"><i class="fas fa-stop close-ticket"></i></a>
                                                <?php endif;?>
                                                <a href="ticket.php?id=<?php echo $ticket->id; ?>"
                                                    class="ticket-actions"><i class="far fa-eye view-ticket"></i></a>
                                                <?php if ($users->UserIsAdmin($user->id)): ?><a
                                                    href="project.php?proj_id=<?php echo $ticket->project_id; ?>&del_ticket=<?php echo $ticket->id; ?>"
                                                    class="ticket-actions"><i
                                                        class="far fa-trash-alt delete-ticket"></i></a><?php endif;?>
                                            </td>
                                            <td><?php echo $ticket->id; ?></td>
                                            <td><?php echo $ticket->title; ?></td>
                                            <td><?php echo $ticket->status; ?></td>
                                            <td><?php echo $ticket->user_fullname; ?></td>
                                            <td><?php echo $ticket->create_date; ?></td>
                                            <td><?php echo ($ticket->start_date !== $ticket->create_date) ? $ticket->start_date : ""; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row add-project mt-0">
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
                                    <?php if ($users->UserIsAdmin($user->id)): ?>
                                    <a href="profile.php?profile=<?php echo $singleUser->id; ?>">
                                        <img src="<?php echo $singleUser->user_photo; ?>" alt="" srcset="">
                                        <div class="user-fullname"><?php echo $singleUser->user_fullname; ?></div>
                                    </a>
                                    <div class="overlay">
                                        <a
                                            href="project.php?proj_id=<?php echo $project->id; ?>&unassign=<?php echo $singleUser->id; ?>"><i
                                                class="fas fa-trash-alt unassign-user"></i></a>
                                    </div>
                                    <?php else: ?>
                                    <img src="<?php echo $singleUser->user_photo; ?>" alt="" srcset="">
                                    <div class="user-fullname"><?php echo $singleUser->user_fullname; ?></div>
                                    <?php endif;?>

                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <?php endif;?>
                    <?php if ($users->UserIsAdmin($user->id)): ?>
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
                    <?php endif;?>
                </div>
                <?php if ($users->UserIsAdmin($user->id)): ?>
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
                                            name="inputProjName"
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
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php include "includes/footer.php";?>
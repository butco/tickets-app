<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
$add_errors = "";
$add_success = "";
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$project = $projects->ProjectDetails($_GET["proj_id"]);
$usersOnProject = $projects->GetAssignedUsers($project->id);

//Add button is pressed
if (isset($_POST['btnAdd'])) {
    if (empty($_POST['inputTicketTitle'])) {
        $add_errors = "Please fill in the ticket's title!";
    } else {
        $ticketProjId = $project->id;
        $ticketUserId = $_POST["assignedUsersSelect"];
        $ticketTitle = trim($_POST['inputTicketTitle']);
        $ticketDetails = trim($_POST['textareaDetails']);
        $ticketStatus = $_POST["ticketStatusSelect"];
        //do the insert in the DB
        if ($tickets->AddNew($ticketProjId, $ticketUserId, $ticketTitle, $ticketDetails, $ticketStatus)) {
            $add_success = "Ticket was created successfully!";
            $_SESSION["msg_error"] = $add_errors;
            $_SESSION["msg_success"] = $add_success;
            header("location:project.php?proj_id=" . $project->id);
        }
    }
}

include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="row add-ticket">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card add-ticket-card m-auto">
                        <div class="card-title text-center mt-5">
                            <h3>Add New Ticket</h3>
                        </div>
                        <div class="card-body">
                            <form action="add-ticket.php?proj_id=<?php echo $_GET["proj_id"]; ?>" method="POST">
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
                                <div class="form-group">
                                    <label for="ticketProjNameInput">Project Name</label>
                                    <input type="text" class="form-control" id="ticketProjNameInput" autocomplete="off"
                                        name="inputProjName" value="<?php echo $project->proj_name; ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="assignedUsersSelect">Assign to User</label>
                                    <select name="assignedUsersSelect" class="form-control">
                                        <?php foreach ($usersOnProject as $u): ;?>
                                        <option value="<?php echo $u->id; ?>">
                                            <?php echo $u->user_fullname; ?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ticketTitleInput">Title</label>
                                    <input type="text" class="form-control" id="ticketTitleInput" autocomplete="off"
                                        autofocus name="inputTicketTitle"
                                        value="<?php echo (isset($_POST["inputTicketTitle"]) ? $_POST["inputTicketTitle"] : ""); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="detailsTextarea">Details</label>
                                    <textarea class="form-control" id="detailsTextarea" autocomplete="off"
                                        name="textareaDetails"
                                        value="<?php echo (isset($_POST["textareaDetails"]) ? $_POST["textareaDetails"] : ""); ?>">
                                    </textarea>
                                </div>
                                <div class="form-group">
                                    <label for="ticketStatusSelect">Status</label>
                                    <select name="ticketStatusSelect" class="form-control">
                                        <option value="OPEN">OPEN</option>
                                        <option value="IN_PROGRESS">IN PROGRESS</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary btn-block btn-add" name="btnAdd">Add
                                    Ticket</button>
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
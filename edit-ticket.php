<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
$edit_errors = null;
$edit_success = null;
//Save all user details into $user object
$user = $users->UserDetails($_SESSION["user_id"]);
$ticket = $tickets->TicketDetails($_GET["id"]);
$project = $projects->ProjectDetails($ticket->project_id);
$usersOnProject = $projects->GetAssignedUsers($project->id);
$assignedUserName = $users->UserDetails($ticket->user_id);

if ($users->UserIsAdmin($user->id) !== true) {
    if ($user->id !== $ticket->user_id) {
        $edit_errors = "You are not allowed to edit Ticket #" . $_GET["id"];
        $_SESSION["edit_ticket_error"] = $edit_errors;
        header("location:project.php?proj_id=" . $project->id);
        exit;
    }
}

//Update button is pressed
if (isset($_POST['btnUpdate'])) {
    //check if values changed
    if (empty($_POST['inputTicketTitle'])) {
        $edit_errors = "Please fill in the ticket's title!";
        $_SESSION["edit_ticket_error"] = $edit_errors;
    } else {
        $projId = $ticket->project_id;
        //the Admin is allowed to reassign the ticket
        //the simple user can't reassign the ticket
        if ($users->UserIsAdmin($user->id)) {
            $userId = $_POST["assignedUsersSelect"];
        } else {
            $userId = $ticket->user_id;
        }
        $title = sanitise_inputs($_POST["inputTicketTitle"]);
        $details = sanitise_inputs($_POST["textareaDetails"]);
        $status = sanitise_inputs($_POST["ticketStatusSelect"]);
        //do the update in the DB
        if (empty($_SESSION["edit_ticket_error"])) {
            if ($tickets->UpdateTicket($ticket->id, $projId, $userId, $title, $details, $status)) {
                $edit_success = "Ticket updated successfully!";
                $_SESSION["edit_ticket_success"] = $edit_success;
                header("location:project.php?proj_id=" . $ticket->project_id);
                exit;
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
                            <h3>Edit Ticket #<?php echo $ticket->id; ?></h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION["edit_ticket_error"]) && !empty($_SESSION["edit_ticket_error"])): ?>
                            <div class="alert alert-danger" role="alert">
                                <strong><?php echo $_SESSION["edit_ticket_error"];unset($_SESSION["edit_ticket_error"]); ?></strong>
                            </div>
                            <?php endif;?>
                            <?php if (isset($_SESSION["edit_ticket_success"]) && !empty($_SESSION["edit_ticket_success"])): ?>
                            <div class="alert alert-success" role="alert">
                                <strong><?php echo $_SESSION["edit_ticket_success"];unset($_SESSION["edit_ticket_success"]); ?></strong>
                            </div>
                            <?php endif;?>
                            <form action="edit-ticket.php?id=<?php echo $ticket->id; ?>" method="POST">
                                <div class="form-group">
                                    <label for="ticketProjNameInput">Project Name</label>
                                    <input type="text" class="form-control" id="ticketProjNameInput" autocomplete="off"
                                        name="inputProjName" value="<?php echo $project->proj_name; ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <?php if ($users->UserIsAdmin($user->id)): ?>
                                    <label for="assignedUsersSelect">Assign to User</label>
                                    <select name="assignedUsersSelect" class="form-control">
                                        <option value="<?php echo $assignedUserName->id; ?>">
                                            <?php echo $assignedUserName->user_fullname . " ( ASSIGNED )"; ?></option>
                                        <?php foreach ($usersOnProject as $u): ;?>
                                        <option value="<?php echo $u->id; ?>">
                                            <?php echo $u->user_fullname; ?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <?php else: ?>
                                    <label for="assignedUserInput">Assigned User</label>
                                    <input type="text" class="form-control" id="assignedUserInput" autocomplete="off"
                                        name="inputUserName" value="<?php echo $assignedUserName->user_fullname; ?>"
                                        disabled>
                                    <?php endif;?>
                                </div>
                                <div class="form-group">
                                    <label for="ticketTitleInput">Title</label>
                                    <input type="text" class="form-control" id="ticketTitleInput" autocomplete="off"
                                        name="inputTicketTitle"
                                        value="<?php echo (isset($_POST["inputTicketTitle"]) ? $_POST["inputTicketTitle"] : $ticket->title); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="detailsTextarea">Details</label>
                                    <textarea class="form-control" id="detailsTextarea" autocomplete="off"
                                        name="textareaDetails"><?php echo (isset($_POST["textareaDetails"]) ? $_POST["textareaDetails"] : $ticket->details); ?>
                                    </textarea>
                                </div>
                                <div class="form-group">
                                    <label for="ticketStatusSelect">Status</label>
                                    <select name="ticketStatusSelect" class="form-control">
                                        <option value="<?php echo $ticket->status; ?>">
                                            <?php echo $ticket->status . " ( NOW )"; ?></option>
                                        <option value="OPEN">OPEN</option>
                                        <option value="IN_PROGRESS">IN PROGRESS</option>
                                    </select>
                                </div>
                                <div class="buttons">
                                    <button type="submit" class="btn btn-secondary btn-block btn-update"
                                        name="btnUpdate">UPDATE</button>
                                </div>
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
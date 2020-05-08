<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
$close_errors = null;
$close_success = null;
//Save all user details into $user object
$user = $users->UserDetails($_SESSION["user_id"]);
$ticket = $tickets->TicketDetails($_GET["id"]);
$project = $projects->ProjectDetails($ticket->project_id);
$usersOnProject = $projects->GetAssignedUsers($project->id);
$assignedUserName = $users->UserDetails($ticket->user_id);

if ($users->UserIsAdmin($user->id) !== true) {
    if ($user->id !== $ticket->user_id) {
        $close_errors = "You are not allowed to close Ticket #" . $ticket->id;
        $_SESSION["proj_msg_error"] = $close_errors;
        header("location:project.php?proj_id=" . $ticket->project_id);
        exit;
    }
}

//Close Ticket button is pressed
if (isset($_POST['btnCloseTicket'])) {
    if (!strlen(sanitise_inputs($_POST['textareaCloseDetails']))) {
        $close_errors = "Please fill in the close details!";
        $_SESSION["proj_msg_error"] = $close_errors;
    } else {
        $closeDetails = sanitise_inputs($_POST["textareaCloseDetails"]);
        //do the update in the DB
        if (empty($_SESSION["proj_msg_error"])) {
            if ($tickets->CloseTicket($ticket->id, $closeDetails)) {
                $close_success = "Ticket closed successfully!";
                $_SESSION["proj_msg_success"] = $close_success;
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
            <div class="row close-ticket">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card close-ticket-card m-auto">
                        <div class="card-title text-center mt-5">
                            <h3>Close Ticket #<?php echo $ticket->id; ?></h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION["proj_msg_error"]) && !empty($_SESSION["proj_msg_error"])): ?>
                            <div class="alert alert-danger" role="alert">
                                <strong><?php echo $_SESSION["proj_msg_error"];unset($_SESSION["proj_msg_error"]); ?></strong>
                            </div>
                            <?php endif;?>
                            <?php if (isset($_SESSION["proj_msg_success"]) && !empty($_SESSION["proj_msg_success"])): ?>
                            <div class="alert alert-success" role="alert">
                                <strong><?php echo $_SESSION["proj_msg_success"];unset($_SESSION["proj_msg_success"]); ?></strong>
                            </div>
                            <?php endif;?>
                            <form action="close-ticket.php?id=<?php echo $ticket->id; ?>" method="POST">
                                <div class="form-group">
                                    <label for="ticketProjNameInput">Project Name</label>
                                    <input type="text" class="form-control" id="ticketProjNameInput" autocomplete="off"
                                        name="inputProjName" value="<?php echo $project->proj_name; ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="assignedUserInput">Assigned User</label>
                                    <input type="text" class="form-control" id="assignedUserInput" autocomplete="off"
                                        name="inputUserName" value="<?php echo $assignedUserName->user_fullname; ?>"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label for="ticketTitleInput">Title</label>
                                    <input type="text" class="form-control" id="ticketTitleInput" autocomplete="off"
                                        name="inputTicketTitle" value="<?php echo $ticket->title; ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="detailsTextarea">Details</label>
                                    <textarea class="form-control" id="detailsTextarea" autocomplete="off"
                                        name="textareaDetails" disabled><?php echo $ticket->details; ?>
                                    </textarea>
                                </div>
                                <div class="form-group">
                                    <label for="closeDetailsTextarea">Close Details</label>
                                    <textarea class="form-control" id="closeDetailsTextarea" autocomplete="off"
                                        name="textareaCloseDetails"><?php echo (isset($_POST["textareaCloseDetails"]) ? $_POST["textareaCloseDetails"] : ""); ?>
                                    </textarea>
                                </div>
                                <div class="buttons">
                                    <button type="submit" class="btn btn-secondary btn-block btn-close-ticket"
                                        name="btnCloseTicket">CLOSE TICKET</button>
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
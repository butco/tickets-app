<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$statuses = array('status' => "'OPEN', 'IN_PROGRESS'");
$allTickets = $tickets->GetAllTicketsForUser($user->id, $statuses['status']);
//show CLOSED tickets
$closedStatus = array('status' => "'CLOSED'");
$allClosedTickets = $tickets->GetAllTicketsForUser($user->id, $closedStatus['status']);

//Start ticket
if (isset($_GET["start_ticket"]) && !empty($_GET["start_ticket"])) {
    $ticket = $tickets->TicketDetails($_GET["start_ticket"]);
    if (($users->UserIsAdmin($user->id) !== true) && ($user->id !== $ticket->user_id)) {
        $msg_errors = "You are not allowed to start Ticket #" . $_GET["start_ticket"];
        $_SESSION["action_ticket_error"] = $msg_errors;
        header("location:my-tickets.php");
        exit;
    } else {
        if ($tickets->StartTicket($_GET["start_ticket"])) {
            $msg_success = "Ticket started successfully!";
            $_SESSION["action_ticket_success"] = $msg_success;
            header("location:my-tickets.php");
            exit;
        } else {
            $msg_errors = "Couldn't start the ticket!";
            $_SESSION["action_ticket_error"] = $msg_errors;
            header("location:my-tickets.php");
            exit;
        }
    }
}

//Delete ticket
if (isset($_GET["del_ticket"]) && !empty($_GET["del_ticket"])) {
    $ticket = $tickets->TicketDetails($_GET["del_ticket"]);
    if (($users->UserIsAdmin($user->id) !== true) && ($user->id !== $ticket->user_id)) {
        $msg_errors = "You are not allowed to delete Ticket #" . $_GET["del_ticket"];
        $_SESSION["action_ticket_error"] = $msg_errors;
        header("location:my-tickets.php");
        exit;
    } else {
        if ($tickets->DeleteTicket($_GET["del_ticket"])) {
            $msg_success = "Ticket deleted successfully!";
            $_SESSION["action_ticket_success"] = $msg_success;
            header("location:my-tickets.php");
            exit;
        } else {
            $msg_errors = "Couldn't delete the ticket!";
            $_SESSION["action_ticket_error"] = $msg_errors;
            header("location:my-tickets.php");
            exit;
        }
    }
}

include "includes/header.php";
?>
<?php if (isset($_SESSION["action_ticket_error"]) && !empty($_SESSION["action_ticket_error"])): ?>
<div class="alert alert-danger dashboard-alert" role="alert">
    <?php echo $_SESSION["action_ticket_error"];unset($_SESSION["action_ticket_error"]); ?>
</div>
<?php endif;?>
<?php if (isset($_SESSION["action_ticket_success"]) && !empty($_SESSION["action_ticket_success"])): ?>
<div class="alert alert-success dashboard-alert" role="alert">
    <?php echo $_SESSION["action_ticket_success"];unset($_SESSION["action_ticket_success"]); ?>
</div>
<?php endif;?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="page-title">My Tickets</div>
            <div class="row tickets">
                <div class="col-12 m-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive-xl">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th scope="col">Actions</th>
                                            <th scope="col">#</th>
                                            <th scope="col">Project</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">User</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Started At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allTickets as $ticket): ?>
                                        <?php $project = $projects->ProjectDetails($ticket->project_id);?>
                                        <tr
                                            class="<?php echo ($ticket->status == "IN_PROGRESS") ? "table-success" : ""; ?>">
                                            <td>
                                                <?php if ($ticket->status == "OPEN"): ?>
                                                <a href="my-tickets.php?start_ticket=<?php echo $ticket->id; ?>"
                                                    title="Start Ticket" class="ticket-actions"><i
                                                        class="fas fa-play start-ticket"></i></a>
                                                <?php elseif ($ticket->status == "IN_PROGRESS"): ?>
                                                <a href="close-ticket.php?id=<?php echo $ticket->id; ?>"
                                                    title="Close Ticket" class="ticket-actions"><i
                                                        class="fas fa-stop close-ticket"></i></a>
                                                <?php endif;?>
                                                <a href="ticket.php?id=<?php echo $ticket->id; ?>" title="View Ticket"
                                                    class="ticket-actions"><i class="far fa-eye view-ticket"></i></a>
                                                <a href="edit-ticket.php?id=<?php echo $ticket->id; ?>"
                                                    title="Edit Ticket" class="ticket-actions"><i
                                                        class="far fa-edit edit-ticket"></i></a>
                                                <?php if ($users->UserIsAdmin($user->id) || $user->id == $ticket->user_id): ?><a
                                                    href="my-tickets.php?del_ticket=<?php echo $ticket->id; ?>"
                                                    title="Delete Ticket" class="ticket-actions"><i
                                                        class="far fa-trash-alt delete-ticket"></i></a><?php endif;?>
                                            </td>
                                            <td><?php echo $ticket->id; ?></td>
                                            <td><?php echo $project->proj_name; ?></td>
                                            <td><?php echo $ticket->title; ?></td>
                                            <td><?php echo $ticket->status; ?></td>
                                            <td><?php echo $ticket->user_fullname; ?></td>
                                            <td><?php echo $ticket->create_date; ?></td>
                                            <td><?php echo $ticket->start_date; ?>
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
            <div class="row closed-tickets">
                <div class="col-12 m-auto">
                    <div class="card">
                        <div class="card-title text-center mt-3 mb-0">
                            <h4>CLOSED Tickets</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-xl">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th scope="col">Actions</th>
                                            <th scope="col">#</th>
                                            <th scope="col">Project</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">User</th>
                                            <th scope="col">Started At</th>
                                            <th scope="col">Closed At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allClosedTickets as $ticket): ?>
                                        <?php $project = $projects->ProjectDetails($ticket->project_id);?>
                                        <tr>
                                            <td>
                                                <a href="ticket.php?id=<?php echo $ticket->id; ?>" title="View Ticket"
                                                    class="ticket-actions"><i class="far fa-eye view-ticket"></i></a>
                                                <a href="edit-ticket.php?id=<?php echo $ticket->id; ?>"
                                                    title="Edit Ticket" class="ticket-actions"><i
                                                        class="far fa-edit edit-ticket"></i></a>
                                                <?php if ($users->UserIsAdmin($user->id) || $user->id == $ticket->user_id): ?><a
                                                    href="my-tickets.php?del_ticket=<?php echo $ticket->id; ?>"
                                                    title="Delete Ticket" class="ticket-actions"><i
                                                        class="far fa-trash-alt delete-ticket"></i></a><?php endif;?>
                                            </td>
                                            <td><?php echo $ticket->id; ?></td>
                                            <td><?php echo $project->proj_name; ?></td>
                                            <td><?php echo $ticket->title; ?></td>
                                            <td><?php echo $ticket->status; ?></td>
                                            <td><?php echo $ticket->user_fullname; ?></td>
                                            <td><?php echo $ticket->start_date; ?>
                                            <td><?php echo $ticket->close_date; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                                <div class="copyright">2020 &copy; <a href="https://www.ButcoSoft.com"
                                        class="copy-link">ButcoSoft</a>. All
                                    rights reserved.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php";?>
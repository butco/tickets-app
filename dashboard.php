<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);
$allProjects = $projects->GetAllProjects();
$allOpenTickets = $tickets->GetTicketsByStatus("OPEN");
$allStartedTickets = $tickets->GetTicketsByStatus("IN_PROGRESS");
$allClosedTickets = $tickets->GetTicketsByStatus("CLOSED");
$statuses = array('status' => "'OPEN', 'IN_PROGRESS'");
$latestTickets = $tickets->GetLatestTickets($statuses['status']);
$countProgressTickets = $tickets->GetCountProgressTickets();
$allActiveUsers = $users->GetAllUsers("user_is_active", 1);
include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <?php if (isset($_SESSION["msg_error"]) && !empty($_SESSION["msg_error"])): ?>
            <div class="alert alert-danger" role="alert">
                <strong><?php echo $_SESSION["msg_error"];unset($_SESSION["msg_error"]); ?></strong>
            </div>
            <?php endif;?>
            <?php if (isset($_SESSION["msg_success"]) && !empty($_SESSION["msg_success"])): ?>
            <div class="alert alert-success" role="alert">
                <strong><?php echo $_SESSION["msg_success"];unset($_SESSION["msg_success"]); ?></strong>
            </div>
            <?php endif;?>
            <div class="page-title">Dashboard</div>
            <div class="dashboard-cards">
                <div class="card dashboard-cards-tickets">
                    <div class="card-title">TICKETS</div>
                    <div class="card-body">
                        <div class="tickets-total">
                            <span class="number"><?php echo $allOpenTickets; ?></span>
                            <p class="text">OPEN</p>
                        </div>
                        <div class="tickets-started">
                            <span class="number"><?php echo $allStartedTickets; ?></span>
                            <p class="text">IN PROGRESS</p>
                        </div>
                        <div class="tickets-closed">
                            <span class="number"><?php echo $allClosedTickets; ?></span>
                            <p class="text">CLOSED</p>
                        </div>
                        <div class="tickets-total">
                            <span
                                class="number"><?php echo $allOpenTickets + $allStartedTickets + $allClosedTickets; ?></span>
                            <p class="text">TOTAL</p>
                        </div>
                    </div>
                </div>
                <div class="card dashboard-cards-projects">
                    <div class="card-title">PROJECTS</div>
                    <div class="card-body">
                        <span class="number"><?php echo count($allProjects); ?></span>
                    </div>
                </div>
                <div class="card dashboard-cards-top">
                    <div class="card-title">TOP 5 IN PROGRESS</div>
                    <div class="card-body">
                        <div class="table-responsive-xl">
                            <table class="table table-hover top5-table">
                                <tbody>
                                    <?php foreach ($countProgressTickets as $proj): ?>
                                    <tr>
                                        <td><?php echo $proj->proj_name; ?></td>
                                        <td><?php echo $proj->NO; ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card dashboard-cards-users">
                    <div class="no-of-users">
                        <div class="card-title">USERS</div>
                        <div class="card-body">
                            <span class="number"><?php echo count($allActiveUsers); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-tables">
                <div class="card latest-tickets">
                    <div class="card-title">LATEST TICKETS</div>
                    <div class="card-body">
                        <div class="table-responsive-xl">
                            <table class="table table-hover">
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
                                    <?php foreach ($latestTickets as $ticket): ?>
                                    <tr>
                                        <td>
                                            <a href="ticket.php?id=<?php echo $ticket->id; ?>" title="View Ticket"
                                                class="ticket-actions"><i class="far fa-eye view-ticket"></i></a>
                                        </td>
                                        <td><?php echo $ticket->id; ?></td>
                                        <td><?php echo $ticket->proj_name; ?></td>
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
    </div>
</div>
<?php include "includes/footer.php";?>
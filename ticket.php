<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION["user_id"]);
$ticket = $tickets->TicketDetails($_GET["id"]);
$project = $projects->ProjectDetails($ticket->project_id);
$assignedUserName = $users->UserDetails($ticket->user_id);

include "includes/header.php";
?>
<div class="container-fluid container-bg container-full-height">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="row view-ticket">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card view-ticket-card m-auto">
                        <div class="card-title text-center mt-5">
                            <h3>View Ticket #<?php echo $ticket->id; ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>#</th>
                                            <td><?php echo $ticket->id; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Project Name</th>
                                            <td><?php echo $project->proj_name; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Assigned User</th>
                                            <td><?php echo $assignedUserName->user_fullname; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Title</th>
                                            <td><?php echo $ticket->title; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Details</th>
                                            <td><?php echo $ticket->details; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td><?php echo $ticket->status; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td><?php echo $ticket->create_date; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Started At</th>
                                            <td><?php echo $ticket->start_date; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Closed At</th>
                                            <td><?php echo $ticket->close_date; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Close Details</th>
                                            <td><?php echo $ticket->close_details; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php";?>
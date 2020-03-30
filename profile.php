<?php

require "config/init.php";
//check if user is logged in. If not then redirect to index
$users->logged_out_redirect();
//Save all user details into $user object
$user = $users->UserDetails($_SESSION['user_id']);

include "includes/header.php";
?>
<div class="container-fluid container-bg">
    <div class="row">
        <?php include "includes/sidebar.php";?>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-1">
            <div class="row">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card">
                        <img src="<?php echo $user->user_photo; ?>" alt="" class="card-img-top edit-profile-photo">

                        <div class="card-body">
                            <div class="card-title">
                                <p>Update Details</p>
                            </div>
                            <div class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                Exercitationem blanditiis error ex soluta quia perferendis quas aperiam consequatur!
                                Labore, cumque!</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php";?>
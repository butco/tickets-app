<div class="col-lg-2 col-md-3 col-sm-4 col-xs-1 sidebar top-fixed">
    <div class="brand text-white text-center pb-3 mt-3">TicketsApp</div>
    <section class="profile">
        <div class="profile-photo">
            <img src="<?php echo $user->user_photo; ?>" alt="profile-photo">
        </div>
        <div class="profile-welcome text-center text-white mb-3">Welcome,
            <strong><?php echo $user->user_fullname; ?></strong></div>
        <div class="profile-links">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php?profile=<?php echo $user->id; ?>">Profile</a></li>
                <li><a href="my-projects.php">My Projects</a></li>
                <li><a href="#">My Tickets</a></li>
                <li><a href="logout.php" class="btn btn-danger mt-3">Logout</a></li>
            </ul>
        </div>
    </section>
    <!-- Show Admin Links only to Admins -->
    <?php if ($user->user_group_id == 1): ?>
    <section class="admin-links">
        <div class="admin-links-title">
            <ul>
                <li>Admin Links</li>
            </ul>
        </div>
        <ul>
            <li><a href="users.php">Users</a></li>
            <li><a href="projects.php">Projects</a></li>
            <li><a href="#">Tickets</a></li>
        </ul>
    </section>
    <?php endif;?>
    <!-- ****** -->
</div>
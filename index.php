<?php

require "config/init.php";

$users->logged_in_redirect();
$login_errors = '';

if (isset($_POST['btnLogin'])) {
    $email = trim($_POST['inputEmail']);
    $password = trim($_POST['inputPassword']);

    if (empty($email) || empty($password)) {
        $login_errors = "Please fill in your email and password!";
    } else if ($users->EmailExists($email) === false) {
        $login_errors = "This email does not exist in the database!";
    } else {
        $user_id = $users->Login($email, $password);
        if ($user_id === false) {
            $login_errors = "ERROR! Wrong password. Please try again!";
        } else {
            $_SESSION["user_id"] = $user_id;
            header("location: dashboard.php");
        }
    }

}

?>

<?php include "includes/header.php";?>

<div class="container-fluid container-bg">
    <div class="row">
        <div class="card card-login">
            <div class="card-body">
                <form method="POST">
                    <h3>TicketsApp</h3>
                    <?php if ($login_errors != ""): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong><?=$login_errors;?></strong>
                    </div>
                    <?php endif;?>
                    <div class="form-group">
                        <label for="emailInput">Email address</label>
                        <input type="email" class="form-control" id="emailInput" name="inputEmail"
                            aria-describedby="emailHelp" value=<?=$email;?>>
                    </div>
                    <div class="form-group">
                        <label for="passInput">Password</label>
                        <input type="password" class="form-control" name="inputPassword" id="passInput">
                    </div>
                    <!-- <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                    <button type="submit" class="btn btn-primary btn-block btn-login" name="btnLogin">Login</button>
                    <div class="copyright">2020 &copy; <a href="https://www.ButcoSoft.com"
                            class="copy-link">ButcoSoft</a>. All
                        rights reserved.</div>
                </form>
            </div>
        </div>
        <div class="illustration">
            <img src="images/dev_illustration.svg" alt="" srcset="">
        </div>
    </div>
</div>
<?php include "includes/footer.php";?>
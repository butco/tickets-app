<?php

require "config/init.php";

$conn = new Db();
$conn->connect();

?>

<?php include "includes/header.php";?>

<div class="container-fluid container-bg">
    <!-- Image and text -->
    <!-- <nav class="navbar navbar-light" style="background-color:rgba(0,0,0,0)">
        <a class="navbar-brand" href="index.php">
            <img src="" width="30" height="30" class="d-inline-block align-top" alt="">
            TicketsApp
        </a>
    </nav> -->
    <div class="row">
        <div class="card card-login">
            <div class="card-body">
                <form>
                    <h3>TicketsApp</h3>
                    <div class="form-group">
                        <label for="emailInput">Email address</label>
                        <input type="email" class="form-control" id="emailInput" aria-describedby="emailHelp">
                    </div>
                    <div class="form-group">
                        <label for="passInput">Password</label>
                        <input type="password" class="form-control" id="passInput">
                    </div>
                    <!-- <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                    <button type="submit" class="btn btn-primary btn-block btn-login">Login</button>
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
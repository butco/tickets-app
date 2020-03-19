<?php

session_start();

require "config/Db.php";
require "classes/Users.php";

$users = new Users($db);
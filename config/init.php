<?php

session_start();

require "config/Db.php";
require "classes/Users.php";
require "classes/Projects.php";

$conn = new Db();
$db = $conn->connect();

$users = new Users($db);
$projects = new Projects($db);
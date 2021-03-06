<?php

session_start();

require "config/Db.php";
require "classes/Users.php";
require "classes/Projects.php";
require "classes/Tickets.php";

$conn = new Db();
$db = $conn->connect();

$users = new Users($db);
$projects = new Projects($db);
$tickets = new Tickets($db);

//Sanitize inputs
function sanitise_inputs($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
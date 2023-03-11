<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include("../include/portal.php");

$portal = new PortalUtility();

$lecturer_name = !empty($_POST['lecturer_name']) ? trim($_POST['lecturer_name']) : "";
$lecturer_email = !empty($_POST['lecturer_email']) ? trim($_POST['lecturer_email']) : "";
$password = !empty($_POST['password']) ? trim($_POST['password']) : "";
$lecturer_address = !empty($_POST['lecturer_address']) ? trim($_POST['lecturer_address']) : "";
$lecturer_title = !empty($_POST['lecturer_title']) ? trim($_POST['lecturer_title']) : "";
$phone_number = !empty($_POST['phone_number']) ? trim($_POST['phone_number']) : "";



$user = $portal->createNewLecturer($conn, $lecturer_name, $lecturer_email, $password, $lecturer_address, $lecturer_title, $phone_number);
echo $user; 


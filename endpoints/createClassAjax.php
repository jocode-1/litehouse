<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once("../include/portal.php");

$portal = new PortalUtility();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405); 
    echo json_encode(array("error" => "Only POST method is allowed"));
    exit;
}

$lecturer_id = !empty($_POST['lecturer_id']) ? trim($_POST['lecturer_id']) : "";
$class_code = !empty($_POST['class_code']) ? trim($_POST['class_code']) : "";
$class_name = !empty($_POST['class_name']) ? trim($_POST['class_name']) : "";
$class_description = !empty($_POST['class_description']) ? trim($_POST['class_description']) : "";

if (empty($lecturer_id) || empty($class_code) || empty($class_name) || empty($class_description)) {
    http_response_code(400); 
    echo json_encode(array("error" => "Missing required input"));
    exit;
}

try {

    $response = $portal->createClass($conn, $lecturer_id, $class_code, $class_name, $class_description);
    http_response_code(200);
    echo json_encode(array("data" => $response));

} catch(Exception $e) {

    http_response_code(500);
    echo json_encode(array("error" => "Error creating class" . $e->getMessage()));
}

<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once("../include/portal.php");

$portal = new PortalUtility();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    http_response_code(405); 
    echo json_encode(array("error" => "Only GET method is allowed"));
    exit;
}

$data = json_decode(@file_get_contents("php://input"), true);
$lecturer_id = trim(mysqli_real_escape_string($conn, !empty($data['lecturer_id']) ? $data['lecturer_id'] : ""));

if (empty($lecturer_id)) {

    http_response_code(400); 
    echo json_encode(array("error" => "Missing required input"));
    exit;
}

try {

    $response = $portal->fetchLectureByLecturerId($conn, $lecturer_id);
    http_response_code(200);
    echo json_encode(array("data" => $response));

} catch(Exception $e) {

    http_response_code(500);
    echo json_encode(array("error" => "Error creating class" . $e->getMessage()));
}
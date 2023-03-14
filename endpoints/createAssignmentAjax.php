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

$data = json_decode(@file_get_contents("php://input"), true);
// $assignment_id = trim(mysqli_real_escape_string($conn, !empty($data['assignment_id']) ? $data['assignment_id'] : ""));
$lecturer_id = trim(mysqli_real_escape_string($conn, !empty($data['lecturer_id']) ? $data['lecturer_id'] : ""));
$course_id = trim(mysqli_real_escape_string($conn, !empty($data['course_id']) ? $data['course_id'] : ""));
$course_code = trim(mysqli_real_escape_string($conn, !empty($data['course_code']) ? $data['course_code'] : ""));
$assignment_title = trim(mysqli_real_escape_string($conn, !empty($data['assignment_title']) ? $data['assignment_title'] : ""));
$submission_date = trim(mysqli_real_escape_string($conn, !empty($data['submission_date']) ? $data['submission_date'] : ""));


if (empty($lecturer_id) || empty($course_id) || empty($course_code) || empty($assignment_title) || empty($submission_date)) {
    http_response_code(400); 
    echo json_encode(array("error" => "Missing required input"));
    exit;
}

try {

    $response = $portal->CreateAssignment($conn, $lecturer_id, $course_id, $course_code, $assignment_title, $submission_date );
    http_response_code(200);
    echo json_encode(array("data" => $response));

} catch(Exception $e) {

    http_response_code(500);
    echo json_encode(array("error" => "Error creating Assignment" . $e->getMessage()));
}

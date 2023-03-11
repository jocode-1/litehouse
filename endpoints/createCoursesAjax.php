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
$course_code = !empty($_POST['course_code']) ? trim($_POST['course_code']) : "";
$course_title = !empty($_POST['course_title']) ? trim($_POST['course_title']) : "";
$course_description = !empty($_POST['course_description']) ? trim($_POST['course_description']) : "";

if (empty($lecturer_id) || empty($course_code) || empty($course_title) || empty($course_description)) {
    http_response_code(400); 
    echo json_encode(array("error" => "Missing required input"));
    exit;
}

try {

    $response = $portal->createCourses($conn, $lecturer_id, $course_code, $course_title, $course_description);
    http_response_code(200);
    echo json_encode(array("data" => $response));

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(array("error" => "Error creating courses" . $e->getMessage()));
}

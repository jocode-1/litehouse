<?php 

include("db_connection.php");
session_start();

require '../vendor/autoload.php';

use Firebase\Jwt\Jwt;
// use Firebase\Key\Key;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new database();
$conn = $database->getConnection();


class Portalutility {

    // public function auth() {

    //     try {

    //         $createdAt = time();
    //         $expireAt = time()* 3600;

    //         $payload = array(

    //             'iss' => 'http://localhost/litehouse',
    //             'aud' => 'http://localhost',
    //             'iat' => $createdAt,
    //             'exp' => $expireAt
    //         );

    //         $jwtGeneratedToken = JWT::encode($payload, $this->key, 'HS256');

    //         var_dump(($jwtGeneratedToken));

    //         return [
    //             'token' => $jwtGeneratedToken,
    //             'expires' => $expireAt
    //         ];

    //     } catch (Exepection $exepection) {


    //     }
    // }

    public function generateRandomIds() {

        $random = substr(str_shuffle(str_repeat("0123456789", 7)), 0, 6);

        return $random;
    }

    public function createUniqueID() {

        $unique = "";

        $unique = "LEC" . $this->generateRandomIds();

        return $unique;
    }


    public function createNewLecturer($conn, $lecturer_name, $lecturer_email, $password, $lecturer_address, $lecturer_title, $phone_number) {

        $data = '';
        $lecturer_id = $this->createUniqueID();

        $query = "INSERT INTO lecturer (`lecturer_id`, `lecturer_name`, `lecturer_email`, `password`, `lecturer_address`, `lecturer_title`, `phone_number`, `status`) 
        VALUES('" . $lecturer_id . "', '" . $lecturer_name . "', '" . $lecturer_email . "', '" . $password . "', '" . $lecturer_address . "', '" . $lecturer_title . "', '" . $phone_number . "', 'A')";

        if (mysqli_query($conn, $query)) {

            $this->sendConfirmationMessage($lecturer_name, $lecturer_email );
            $data = json_encode(array("message" => "success", "lecturer_id"=>$lecturer_id), JSON_PRETTY_PRINT);

        } else {

            $data = json_encode(array("message"=>"error","lecturer_id"=>"null"), JSON_PRETTY_PRINT);
        }

        return $data;

    }

    public function sendConfirmationMessage($lecturer_name, $lecturer_email ) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'akintolajohn41@gmail.com';
            $mail->Password   = 'Cougar@123';
            $mail->SMTPSecure = 'SSL';
            $mail->Port       = 465;

            $mail->setFrom('no-reply@litehouse.com', 'Confirmation Email');
			$mail->addAddress($lecturer_email, $lecturer_name);

            $mail->isHTML(true);
            $mail->Subject = 'Confirmation Email';
            $mail->Body    = '<p>Thank you for registering to our website!</p>';

            $mail->send();

            echo "Message sent Successfully";

        } catch (Exception $e) {
            echo "Message not Sent Successfully: {$mail->ErrorInfo}";
            
        }
    }

    public function createClass($conn, $lecturer_id, $class_code, $class_name, $class_description) {

        $data = "";
        $class_id = $this->generateRandomIds();

        $query = "INSERT INTO class (`class_id`, `lecturer_id`, `class_code`, `class_name`, `class_description`, `status`)
        VALUES ('" . $class_id . "', '" . $lecturer_id . "', '" . $class_code . "', '" . $class_name . "', '" . $class_description . "', 'A')";

        if(mysqli_query($conn, $query)) {

            $data = array("message" => "success", "class_id" => $class_id);

        } else {

            $data = array("message"=>"error", "class_id"=>"null");

        }

        return $data;
    }

    public function createLectures($conn, $lecturer_id, $course_id, $course_code, $lecturer_title, $lecture_description) {

        $data = "";
        $lecture_id = $this->generateRandomIds();
        $lecture_url = $this->createLectureLink($conn, $lecturer_id);

       $query = "INSERT INTO lectures (`lecturer_id`, `lecture_id`, `course_id`, `course_code`, `lecture_title`, `lecture_description`, `lecture_url`, `status`)
       VALUES ('" . $lecturer_id . "', '" . $lecture_id . "', '" . $course_id . "', '" . $course_code . "', '" . $lecturer_title . "', '" . $lecture_description . "', '" . $lecture_url . "', 'A')";

       if(mysqli_query($conn, $query)) {

        $data = array("message" => "success", "lecture_id" => $lecture_id, "lecture_url" => $lecture_url);

       } else {

        $data = array("message" => "error", "lecture_id" => "null");

       }

       return $data;
    } 

    public function createLectureLink($conn, $lecturer_id) {
        $expiration = 2500;
        $expires = time() + $expiration;
    
        $query = "SELECT * FROM lecturer WHERE `lecturer_id` = '$lecturer_id'";
        $result = mysqli_query($conn, $query);
        $data = mysqli_fetch_array($result);

        $url = "http://localhost/litehouse/{$lecturer_id}";
        $link = $url . '&expires=' . $expires;
    
        return $link;
    }

    public function createCourses($conn, $lecturer_id, $course_code, $course_title, $course_description) {

        $data = '';
        $course_id = $this->generateRandomIds();

        $query = "INSERT INTO `courses` (`course_id`, `lecturer_id`, `course_code`, `course_title`, `course_description`, `status`) VALUES ('" . $course_id . "', '" . $lecturer_id . "', '" . $course_code . "', '" . $course_title . "', '" . $course_description . "', 'A')";

        if(mysqli_query($conn, $query)) {
            $data = array("message" => "success", "course_id" => $course_id);
        } else {
            $data = array("message" => "error", "course_id" => "null");
        }

        return $data;
    }

    public function loginLecturer($conn, $lecturer_email, $password) {

        // $data = "";

        $query = "SELECT lecturer_email, password FROM lecturer WHERE lecturer_email = '" . $lecturer_email . "' and password = '" . $password . "'";
        $result = mysqli_query($conn, $query);
        $data = mysqli_fetch_array($result);
        if($data > 0){
            $_SESSION['login_user'] = $lecturer_email;
			$json[] = $data;
        } else {
            $json =[0];
        }

        return json_encode($json);

    }

    public function fetchClassByLecturerId($conn, $lecturer_id) {

        $data = array();
        $query = "SELECT * FROM `class` WHERE `lecturer_id` = '$lecturer_id'";
        $result = mysqli_query($conn, $query);

        while ($a = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = $a;
        }

        return $data;
    }

    public function fetchCourseByLecturerId($conn, $lecturer_id) {

        $data = array();
        $query = "SELECT * FROM `courses` WHERE `lecturer_id` = '$lecturer_id'";
        $result = mysqli_query($conn, $query);

        while ($a = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = $a;
        }

        return $data;

    }

    public function fetchLectureByLecturerId($conn, $lecturer_id) {

        $data = array();
        $query = "SELECT * FROM `lectures` WHERE `lecturer_id` = '$lecturer_id'";
        $result = mysqli_query($conn, $query);

        while ($a = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = $a;
        }

        return $data;

    }

    public function CreateAssignment($conn, $lecturer_id, $course_id, $course_code, $assignment_title, $submission_date ) {

        $data = "";
        $assignment_id = $this->generateRandomIds();
        $assignment_url = "http://localhost/assignment.php?" . $lecturer_id . "&" . $course_id;
        echo $assignment_url;

       $query = "INSERT INTO assignment (`assignment_id`, `lecturer_id`, `course_id`, `course_code`, `assignment_title`, `assignment_url`, `submission_date`, `status`)
       VALUES ('" . $assignment_id . "', '" . $lecturer_id . "', '" . $course_id . "', '" . $course_code . "', '" . $assignment_title . "', '" . $assignment_url . "', '" . $submission_date . "', 'A')";

       if(mysqli_query($conn, $query)) {

        $data = array("message" => "success", "assignment_id" => $assignment_id, "course_id" => $course_id, "lecturer_id" => $lecturer_id);

       } else {

        $data = array("message" => "error", "lecture_id" => "null");

       }

       return $data;
    }



}


$portal = new PortalUtility();

// var_dump($portal->loginLecturer($conn, "akintolajohn41@gmail.com", "11111"));
// $portal->insertLecturer($conn, "test", "test", "test", "test", "test");
// $portal->createLectures($conn, "LEC83593", "test", "test", "test", "test");
// var_dump($portal->createCourses($conn, "LEC83593", "test", "test", "test"));
// var_dump($portal->CreateAssignment($conn, "LEC83593", "test", "test", "test", "test"));


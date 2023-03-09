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

        $random = substr(str_shuffle(str_repeat("abcdefghijk0123456789lmnopqrstuv", 7)), 0, 6);

        return $random;
    }

    public function createUniqueID() {

        $unique = "";

        $unique = "LEC" . $this->generateRandomIds();

        return $unique;
    }

    public function sendConfirmationMessage($lecturer_name, $lecturer_email ) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.example.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'no-reply@litehouse.com';
            $mail->Password   = '1234567';
            $mail->SMTPSecure = 'SSL';
            $mail->Port       = 465;

            $mail->setFrom('no-reply@litehouse.com', 'Confirmation Email');
			$mail->addAddress($lecturer_email, $lecturer_name);

            $mail->isHTML(true);
            $mail->Subject = 'Confirmation Email';
            $mail->Body    = '<p>Thank you for registering to our website!</p>';

            $mail->send();

            echo "Message sent Successfully";

        } catch (\Throwable $th) {
            
        }
    }

    public function createNewLecturer($conn, $lecturer_name, $lecturer_email, $lecturer_address, $lecturer_title, $phone_number) {

        $data = '';
        $lecturer_id = $this->createUniqueID();

        $query = "INSERT INTO lecturer (`lecturer_id`, `lecturer_name`, `lecturer_email`, `lecturer_address`, `lecturer_title`, `phone_number`, `status`) 
        VALUES('" . $lecturer_id . "', '" . $lecturer_name . "', '" . $lecturer_email . "', '" . $lecturer_address . "', '" . $lecturer_title . "', '" . $phone_number . "', 'A')";

        if (mysqli_query($conn, $query)) {

            $this->sendConfirmationMessage($lecturer_name, $lecturer_email );
            $data = json_encode(array("message" => "success", "lecturer_id"=>$lecturer_id), JSON_PRETTY_PRINT);

        } else {

            $data = json_encode(array("message"=>"error","lecturer_id"=>"null"), JSON_PRETTY_PRINT);
        }

        return $data;

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

        echo $lecture_url;

       $query = "INSERT INTO lectures (`lecturer_id`, `lecture_id`, `course_id`, `course_code`, `lecture_title`, `lecture_description`, `lecture_url`, `status`)
       VALUES ('" . $lecturer_id . "', '" . $lecture_id . "', '" . $course_id . "', '" . $course_code . "', '" . $lecturer_title . "', '" . $lecture_description . "', '" . $lecture_url . "', 'A')";

       if(mysqli_query($conn, $query)) {

        $data = array("message" => "success", "lecture_id" => $lecture_id, "lecture_url" => $lecture_url);

       } else {

        $data = array("message" => "error", "lecture_id" => "null");

       }
    } 


    // public function createLectureLink($conn, $lecturer_id, $expiration = 3600) {
        
    //     $expires = time() + $expiration;
    //     $url = "http://localhost/litehouse/.{$lecturer_id}.3600 .'";

    //     $query = "SELECT * FROM lecturer WHERE `lecturer_id` = '$lecturer_id'";
    //     $result = mysqli_query($conn, $query);
	// 	$data  = mysqli_fetch_array($result);

    //     // $lecturer = $data["lecturer_id"];

    //     $link = $url . '?lecturer_id=' . $lecturer_id . '&expires=' . $expires;
          
    //         return $link;
          
    // }

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
    

}


$portal = new PortalUtility();

// $portal->insertLecturer($conn, "test", "test", "test", "test", "test");
// $portal->createLectures($conn, "LEC83593", "test", "test", "test", "test");


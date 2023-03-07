<?php 

include("db_connection.php");
session_start();

use Firebase\Jwt;
use Firebase\Key;

require 'vendor/autoload.php';

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
    //             'nbf' => $expireAt
    //         );

    //         $jwtGeneratedToken = JWT::encode($payload, $this->key, 'HS256');

    //         // print_r($jwtGeneratedToken);

    //         return [
    //             'token' => $jwtGeneratedToken,
    //             'expires' => $expireAt
    //         ];

    //     } catch (Exepection $exepection) {


    //     }
    // }

    public function createRandomIds() {

        $random = substr(str_shuffle(str_repeat("0123456789", 5)), 0, 5);

        return $random;
    }

    public function createUniqueID() {

        $unique = "";

        $unique = "LEC" . $this->createRandomIds();

        return $unique;
    }


    public function insertLecturer($conn, $lecturer_name, $lecturer_email, $lecturer_address, $lecturer_title, $phone_number) {

        $data = '';
        $lecturer_id = $this->createUniqueID();

        $query = "INSERT INTO lecturer (`lecturer_id`, `lecturer_name`, `lecturer_email`, `lecturer_address`, `lecturer_title`, `phone_number`, `status`) 
        VALUES('" . $lecturer_id . "', '" . $lecturer_name . "', '" . $lecturer_email . "', '" . $lecturer_address . "', '" . $lecturer_title . "', '" . $phone_number . "', 'A')";

        if (mysqli_query($conn, $query)) {

            $data = json_encode(array("message" => "success", "lecturer_id" => $lecturer_id), JSON_PRETTY_PRINT);

        } else {

            $status = json_encode(array("message"=>"error","lecturer_id"=>"null"), JSON_PRETTY_PRINT);
        }

        return $data;

    }

}


$portal = new PortalUtility();

var_dump($portal->insertLecturer($conn, "test", "test", "test", "test", "test"));


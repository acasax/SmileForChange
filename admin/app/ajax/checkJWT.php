<?php
   /**
    * Created by PhpStorm.
    * User: comp
    * Date: 11/30/2019
    * Time: 5:09 PM
    */
   session_set_cookie_params(0);
   session_start();
   include '../../../app/ajax/db.php';
   require "../../../app/component/vendor/autoload.php";
   use \Firebase\JWT\JWT;
   require_once('../../../app/ajax/User.php');

   header("Access-Control-Allow-Origin: *");
   header("Content-Type: x-www-form-urlencoded; charset=UTF-8");
   header("Access-Control-Allow-Methods: POST");
   header("Access-Control-Max-Age: 3600");
   header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");



   $user = new User();
   Firebase\JWT\JWT::$leeway = 5;
   $jwt = $_POST['jwt'];
      $jwtData;

      $attempt = 0;
      try {
        $jwtData =(array) JWT::decode($jwt, $user->getSecretKey(), array('HS256'));
      } catch (Firebase\JWT\ExpiredException $e) {
         $msg = $e->getMessage();
         if($msg === "Expired token") {
            echo json_encode(array('type'=>'LOGOUT'));
            return;
         }
      }

   $userData = (array) $jwtData['data'];
   $account_id = $userData['id'];
   $stmt = $connection->prepare("SELECT * FROM accounts WHERE id='$account_id'");
   $stmt->execute();
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   $data= array();

   $data['userName'] = $row['username'];
   $data['userSrc'] = 'dist/img/kreativeLab/employees/' . $row['username'].'.png';

   echo json_encode($data);
   return;


?>






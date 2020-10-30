<?php
   /**
    * Created by PhpStorm.
    * User: comp
    * Date: 11/30/2019
    * Time: 3:04 PM
    */
   include './db.php';
   require "../component/vendor/autoload.php";
   use \Firebase\JWT\JWT;

   require_once('./User.php');
   $user = new User();
   Firebase\JWT\JWT::$leeway = 5;

 

  // $user->register('admin@gmail.com','admin123','admin');


   if(isset($_POST['loginusername']) && isset($_POST['loginpassword'])) {

      $username = $_POST['loginusername'];
      $password = $_POST['loginpassword'];

      $query_select = "SELECT * FROM accounts WHERE username = '$username'";
      $check_query = $connection->prepare($query_select);
      $check_query->execute();

      if($check_query->rowCount() > 0) {
         $row = $check_query->fetch();
         $id = $row['id'];
         $username = $row['username'];
        // $email = $row['email'];
         $password2 = $row['password'];
         if(password_verify($password, $password2)){
            $secret_key = $user->getSecretKey();
            $issuedat_claim = time(); // issued at
            $date = date('Y-m-d HH:ii',$issuedat_claim);
            $notbefore_claim = $issuedat_claim ; //not before in seconds
            $expire_claim = $issuedat_claim + 60*60; // expire time in seconds
            $token = array(
             "iat" => $issuedat_claim,
             "nbf" => $notbefore_claim,
             "exp" => $expire_claim,
             "date" => $date,
             "data" => array(
              "id" => $id,
              "username" => $username
             ));
            http_response_code(200);
            $jwt = JWT::encode($token, $secret_key);
            echo json_encode(
             array(
              "type" => "OK",
              "message" => "Successful login.",
              "jwt" => $jwt
             ));

         }else{
            http_response_code(401);
            echo json_encode(array("type"=>"ERROR","message" => "Username or password are not good."));
            }

      }
   }

   ?>
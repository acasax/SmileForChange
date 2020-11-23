<?php
/**
 * Created by PhpStorm.
 * User: Sale
 * Date: 10/3/2019
 * Time: 10:57
 */
require_once 'dbconfig.php';
require SITE_ROOT."/../component/vendor/autoload.php";
   require_once SITE_ROOT.'/../mailer/PHPMailer-master/class.phpmailer.php' ;
   include SITE_ROOT.'/../mailer/PHPMailer-master/class.smtp.php';

   use  \Firebase\JWT\JWT;
class User extends \Firebase\JWT\JWT
{
   private $conn;
   private $role;

   public function __construct()
   {
      $database = new Database();
      $db = $database->dbConnection();
      $this->conn = $db;
   }

   public function getSecretKey(){
      return "ASIFCVJKIO1U89TGU123#$2#$!$!$!HB9Q27Y019UT80YHIQ2J311";
   }

   public function CloseCon()
   {
      $this->conn = null;
   }


    function send_mail($email, $message, $subject)
    {


       $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        //$mail->SMTPDebug  = 2;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->AddAddress("smile4changedd@gmail.com"); //email unesi tvoj email
        $mail->Username = "smile4changedd@gmail.com"; //email
        $mail->Password = "dusan2912"; //password
        $mail->SetFrom($email, 'Kretive Dent Lab');
        $mail->AddReplyTo($email, "Kretive Dent Lab");
        $mail->Subject = $subject;
        $mail->MsgHTML($message);

        if(!$mail->Send()){
            throw new Exception ("Nije moguce poslati email. Pokusajte ponovo.");
            return false;
        }else{
            return true;
        }
    }

   function send_custom_email($email, $message, $subject)
   {

      $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        //$mail->SMTPDebug  = 2;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
      $mail->AddAddress($email); //email from customer
      $mail->Username = "smile4changedd@gmail.com"; //email
      $mail->Password = "dusan2912"; //password
      $mail->SetFrom("smile4changedd@gmail.com", 'Kretive Dent Lab'); // duletov email kreative lab
      //$mail->AddReplyTo("", "Kretive Dent Lab"); // add to cc
      $mail->Subject = $subject;
      $mail->MsgHTML($message);

      if(!$mail->Send()){
         throw new Exception("Nije moguce poslati email. Pokusajte ponovo.");
         return false;
      }else{
         return true;
      }
   }


   public function register($username, $upass, $role)
   {

      try {
         $options = [
          'cost' => 12,
         ];
         $password = password_hash($upass, PASSWORD_BCRYPT);
         $stmt = $this->conn->prepare("INSERT INTO accounts(username,password,role)VALUES(:username, :user_pass,:role)");
         $stmt->bindparam(":username", $username);
         $stmt->bindparam(":user_pass", $password);
         $stmt->bindparam(":role", $role);
         $stmt->execute();
         return $stmt;
      } catch (PDOException $ex) {
         echo $ex->getMessage();
      }
   }

   public function login($id)
   {
      try
      {
         $_SESSION['userSession'] = $id;
         return true;

      }
      catch(PDOException $ex)
      {
         echo $ex->getMessage();
      }
   }

   public function is_logged_in()
   {
      if (isset($_SESSION['userSession'])) {
         return true;
      }

   }

   public function returnJSON($type, $data)
   {
      $array = [];
      $array['type'] = $type;


      $array['data'] = $data;
      echo json_encode($array);
   }

   public function redirect($url)
   {
      header("Location: $url");
   }


   public function get_type_of_account($accId)
   {

      try {
         $role = $this->conn->query("SELECT role FROM accounts WHERE account_id ='$accId'");
         $lvl = $role->fetch();
      } catch (PDOException $ex) {
         echo $ex->getMessage();
      }
      return $lvl['role'];
   }


   public function check_jwt ($jwt) {
      $data= array();
      try {
         $jwtData = (array)JWT::decode($jwt, $this->getSecretKey(), array('HS256'));
      } catch (Firebase\JWT\ExpiredException $e) {
         $msg = $e->getMessage();
         if ($msg === "Expired token") {
            $data['type'] = "ERROR";
            $data['expired'] = true;
            return $data;
         }
      }
      if(!empty($data) && $data['expired'] == true) {
         return $data;
      }
      $userData = (array) $jwtData['data'];
      $account_id = $userData['id'];
      $stmt = $this->conn->prepare("SELECT * FROM accounts WHERE id='$account_id'");
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($stmt->rowCount() <= 0){
         $data['type'] = "ERROR";
         $data['notValid'] = true;
         return $data;
      }
      return true;
   }

   public function getMinMaxTimeForDateId($id){
     $query = "SELECT reservation_time FROM reservation_time WHERE fk_reservation_date_id = '$id'";
     $stmt = $this->conn->prepare($query);
     $stmt->execute();
     $row = $stmt->fetchAll();
     if($stmt->rowCount() > 1) {
        $minTime = $row[0]['reservation_time'];
        $maxTime = $row[$stmt->rowCount()-1]['reservation_time'];
     } else{
        $minTime = $row[0]['reservation_time'];
        $maxTime = $minTime;
     }
     return array('minTime'=>$minTime,'maxTime'=>$maxTime);
   }

   public function getBookingReservationTimeId($bookingId){
      $query = "SELECT fk_reservation_time_id FROM booking_records WHERE id = '$bookingId'";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      $row = $stmt->fetch();
      return $row['fk_reservation_time_id'];
   }



   public function getBookingDataForId($bookingId) {
      $query = "SELECT t1.booking_email, t2.reservation_time, t3.reservation_date, t1.booking_first_name, t1.booking_last_name FROM booking_records t1 INNER JOIN reservation_time t2 ON t2.id = t1.fk_reservation_time_id INNER JOIN reservation_date t3 ON t3.id = t2.fk_reservation_date_id  WHERE t1.id = '$bookingId'";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      $row = $stmt->fetch();
      return $row;
   }



}
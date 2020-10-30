<?php
   /**
    * Created by PhpStorm.
    * User: comp
    * Date: 11/30/2019
    * Time: 10:19 PM
    */


   session_set_cookie_params(0);
   session_start();
   include '../../../app/ajax/db.php';
   require_once('../../../app/ajax/User.php');




   $user = new User();

   $jwt = $_POST['jwt'];
   $checkJWT= $user->check_jwt($jwt);

   if($checkJWT && is_array($checkJWT)){
      return $checkJWT;
   }

   $year = date('Y');
   $status = 'C';
   $date = date('Y-m-d');

   $query = "SELECT 
    t1.id as id,
    t3.reservation_date as date,
    t2.reservation_time as resTime,
    t1.booking_first_name as firstName,
    t1.booking_last_name as lastName,
    t1.booking_email as email,
    t1.booking_phone as phone,
    t1.booking_comment as comment,
    t4.appointnment_my_comment as myComment,
    t4.appointment_email_body as myEmailBody FROM  booking_records t1 INNER JOIN reservation_time t2 ON t2.id = t1.fk_reservation_time_id INNER JOIN reservation_date t3 ON t3.id = t2.fk_reservation_date_id LEFT JOIN booking_appointment_data t4 ON t4.fk_booking_records_id = t1.id WHERE t1.booking_record_year = '$year' AND t1.booking_recrod_status = '$status'";



   $statement = $connection->prepare($query);
   $statement->execute();
   $result = $statement->fetchAll();
   $data= array();
   foreach($result as $row){
      $rservationDate =   $myDateTime = DateTime::createFromFormat('Y-m-d',$row['date']);
      $termDate = $myDateTime->format('d.m.Y');
      $termTime = str_pad($row['resTime'],2,"0",STR_PAD_LEFT) . ":00 h";
      $firstLastName = $row['firstName'] . " " . $row['lastName'];
      $sub_array= array();
      $sub_array["id"] =$row['id'];
      $sub_array["date"] = $termDate;
      $sub_array["time"] = $termTime;
      $sub_array["firstLastName"] = $firstLastName;
      $sub_array["email"] = $row['email'];
      $sub_array["phone"] = $row['phone'];
      $sub_array["comment"] = $row['comment'];
      $sub_array["myComment"] = $row['myComment'];
      $sub_array["emailBody"] = $row['myEmailBody'];
      $data[] =$sub_array;
   }


   echo json_encode($data);
?>


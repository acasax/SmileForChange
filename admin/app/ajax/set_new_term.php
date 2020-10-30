<?php
   /**
    * Created by PhpStorm.
    * User: comp
    * Date: 12/1/2019
    * Time: 9:02 AM
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

   $currentYear = date('Y');
   $currentDate = date("Y-m-d");
   $termDate= $_POST['termDate'];
   $myDateTime = DateTime::createFromFormat('d-m-Y',$termDate);
   $termDate = $myDateTime->format('Y-m-d');
   $startTime = $_POST['startTime'];
   $endTime = $_POST['endTime'];

   $intStartTime = (int)$startTime;
   $intEndTime = (int)$endTime;

   if($intStartTime < 8 || $intStartTime > 17 || $intEndTime - $intStartTime <= 0 || $currentDate > $termDate) {
      $user->returnJSON('ERROR',"Data for insert is not valid!");
      return;
   }

   try{
      $connection->beginTransaction();
      $count = (int)$endTime - (int)$startTime;
      $insert_date = "INSERT INTO `reservation_date`(`reservation_date`, `reservation_date_status`, `reservation_date_year`)VALUES('$termDate','A','$currentYear')";
      $query_insert_term_date = $connection->prepare($insert_date);
      $result = $query_insert_term_date->execute();

      $reservationDateId = $connection->lastInsertId();

      for($i =0; $i<$count+1;$i++){
         $time = $intStartTime+$i;
         if($time > $intEndTime) break;
         $insert_term = "INSERT INTO `reservation_time`( `fk_reservation_date_id`, `reservation_time`, `reservation_time_status`)VALUES ('$reservationDateId','$time','N')";
         $query_insert_term_times = $connection->prepare($insert_term);
         $query_insert_term_times->execute();
      }

      $connection->commit();
      if(!empty($result)) {
         $user->returnJSON('OK', "Successfully added terms for this date.");
         return;
      }
   }catch(Exception $e){
      $msg = $e->getMessage();
      $connection->rollBack();
      $user->returnJSON('ERROR', $msg);
      return;
   }



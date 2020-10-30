<?php
   /**
    * Created by PhpStorm.
    * User: comp
    * Date: 12/1/2019
    * Time: 5:41 PM
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



   $date = date("Y-m-d");
   $currentYear = date("Y");

   $query_get_terms_by_date = "SELECT 
      t1.reservation_date as reservationDate,
      t2.reservation_time as reservationTime,
      t3.booking_first_name as bookingFirstName,
       t3.booking_last_name as bookingLastName, 
       t3.booking_email as bookingEmail, 
       t3.booking_phone as bookingPhone, 
       t3.booking_comment as bookingComment,
       t3.booking_recrod_status as bookingStatus,
       t3.id as bookingId FROM reservation_date t1 INNER JOIN reservation_time t2 ON t2.fk_reservation_date_id = t1.id INNER JOIN booking_records t3 ON t3.fk_reservation_time_id = t2.id WHERE t1.reservation_date >= '2019-12-01' AND t1.reservation_date_status = 'A' AND t3.booking_recrod_status not in ('A','C')";
   $query_execute = $connection->prepare($query_get_terms_by_date);
   $query_execute->execute();
   $result = $query_execute->fetchAll();
   $output=array();

   foreach($result as $row){
      $startTime = str_pad((int) $row['reservationTime'], 2, '0', STR_PAD_LEFT) .':00';
      $endTime = str_pad((int) $row['reservationTime']+1, 2, '0', STR_PAD_LEFT) .':00';

      $color = '#cbcdd1';
      switch($row['bookingStatus']){
         case "R":
            $color = '#026c99'; break;
         case "S":
            $color = '#ffe066'; break;
      }


      $data = array();
      $data['reservationDate'] = $row['reservationDate'];
      $data['reservationTime'] = $row['reservationTime'];
      $data['bookingFirstName'] = $row['bookingFirstName'];
      $data['bookingLastName'] = $row['bookingLastName'];
      $data['bookingEmail'] = $row['bookingEmail'];
      $data['bookingPhone'] = $row['bookingPhone'];
      $data['bookingComment'] = $row['bookingComment'];
      $data['bookingStatus'] = $row['bookingStatus'];
      $data['bookingId'] = $row['bookingId'];
      $data['eventList'] = array();
       $data['eventList']['backgroundColor'] = $color;
       $data['eventList']['start'] = $row['reservationDate'] .' '.$startTime;
       $data['eventList']['end'] = $row['reservationDate'] .' '.$endTime;
       $data['eventList']['title'] = str_pad($row['reservationTime'], 2, '0', STR_PAD_LEFT) . 'h - ' .  $row['bookingFirstName'] .' ' . $row['bookingLastName'];
      $output[] = $data;
   }

   echo json_encode($output);
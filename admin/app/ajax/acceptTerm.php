<?php
   /**
    * Created by PhpStorm.
    * User: comp
    * Date: 12/1/2019
    * Time: 9:35 PM
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

   $bookingId = $_POST['bookingId'];
   $bookingStatus = $_POST['bookingStatus'];
   $bookingEmailBody =  $_POST['mailMessage'];
   $bookingMyComment =  $_POST['myComment'];

      $dataBooking = $user->getBookingDataForId($bookingId);
      $bookingEmail = $dataBooking['booking_email'];
      $bookingName = $dataBooking['booking_first_name'];
      $bookingSurname = $dataBooking['booking_last_name'];
      $bookingTime = $dataBooking['reservation_time'];
      $bookingDate  = $dataBooking['reservation_date'];
      $myDateTime = DateTime::createFromFormat('Y-m-d',$bookingDate);
      $bookingDate = $myDateTime->format('d.m.Y.');

      try{
         $connection->beginTransaction();
         $query = "UPDATE booking_records SET booking_recrod_status ='$bookingStatus' WHERE id='$bookingId'";
         $update_booking_record =$connection->prepare($query);
         $result = $update_booking_record->execute();

         $query_booking_appointment_data = "INSERT INTO booking_appointment_data (fk_booking_records_id,appointment_email_body,appointnment_my_comment)VALUES('$bookingId','$bookingEmailBody','$bookingMyComment')";
         $query_booking_app =$connection->prepare($query_booking_appointment_data);
         $res_booking_data = $query_booking_app->execute();
      
         $mailBody = $bookingEmailBody;

      
         if($bookingStatus == 'C') {
            $res_time_query ="SELECT fk_reservation_time_id FROM booking_records WHERE id = '$bookingId'";

            $restTimeRes =$connection->prepare($res_time_query);
            $restTimeRes->execute();
            $resTimeResult = $restTimeRes->fetch();

            $fkReaservationTimeId = $resTimeResult['fk_reservation_time_id'];
            $qury_reservation_time = "UPDATE reservation_time SET reservation_time_status ='N' WHERE id = '$fkReaservationTimeId'";
            $update_reservation_time = $connection->prepare($qury_reservation_time);
            $update_reservation_time->execute();
         }
         $emailSubject = "Kreative Dent Lab - Termin za " . $bookingName . " " . $bookingSurname ." - " . str_pad($bookingTime,2,"0",STR_PAD_LEFT) . " h" ;


        $bookingEmail = 'acasax@gmail.com';
         $send = $user->send_custom_email($bookingEmail,$mailBody,$emailSubject);

         if(!$send) {
          throw new Exception("Nije moguce poslati email. Pokusajte ponovo.");
         }

         $connection->commit();
        if(!empty($result) && $send) {
            $msg = 'Successfuly accepted appoinment';
            if ($bookingStatus == 'C') $msg = 'Successfuly rejected appoinment';
            $user->returnJSON("OK", $msg);
            return;
        }
      }catch (Exception $e) {
         $msg = $e->getMessage();
         var_dump($msg);
         $connection->rollBack();
         $user->returnJSON("ERROR", "ERROR BRE");
         return;
      }
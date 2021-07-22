<?php
/**
 * Created by PhpStorm.
 * User: comp
 * Date: 11/24/2019
 * Time: 8:16 PM
 */

include './db.php';
require_once('./User.php');
$user = new User();

if(isset($_POST["bookingname"]) && isset($_POST["bookinglastname"]) && isset($_POST["bookingemail"]) && isset($_POST["bookingphone"]) && isset($_POST["bookingdate"]) && isset($_POST["bookingtime"])){
    $time = $_POST["bookingtime"];
    $date = $_POST["bookingdate"];
    $first_name = $_POST['bookingname'];
    $last_name = $_POST['bookinglastname'];
    $email = $_POST['bookingemail'];
    $phone = $_POST['bookingphone'];
    $message = $_POST['bookingmessage'];
    $year = date('Y');
    $myDateTime = DateTime::createFromFormat('d-m-Y', $date);
    $date = $myDateTime->format('Y-m-d');


    $checkTimeForDate_query = "SELECT t2.reservation_time_status FROM reservation_date t1 LEFT JOIN reservation_time t2 ON t2.fk_reservation_date_id = t1.id WHERE t1.reservation_date = '$date' AND t1.reservation_date_year = '$year' AND t1.reservation_date_status = 'A' AND t2.reservation_time = '$time'";
    $time_for_date_query = $connection->prepare($checkTimeForDate_query);
    $time_for_date_query->execute();
    $timeForDateResult = $time_for_date_query->fetch();

    if($timeForDateResult['reservation_time_status'] != 'N' ){
        $array=[];
        $array['type']="ERROR";
        $array['data']= "Ovaj termin je već zauzet. Odaberite drugi slobodan.";
        echo json_encode($array);
        return;
    }

    $query = "SELECT t1.id  FROM  reservation_time t1 INNER JOIN reservation_date t2 ON t2.id = t1.fk_reservation_date_id
    WHERE t1.reservation_time = '$time' AND t2.reservation_date = '$date' AND t1.reservation_time_status = 'N'";
    $statement = $connection->prepare($query);
    $statement->execute();
    $statementResult = $statement->fetch();
    $reservation_time_id = $statementResult['id'];


    try{
        $connection->beginTransaction();
        $connection ->exec("set names utf8");
        $query_booking = "INSERT booking_records( `fk_reservation_time_id`, `booking_first_name`, `booking_last_name`, `booking_email`, `booking_phone`,`booking_comment`, `booking_recrod_status`, `booking_record_year`)VALUES ('$reservation_time_id','$first_name','$last_name','$email','$phone','$message','S','$year')";
        $query_insert_booking = $connection->prepare($query_booking);
        $result = $query_insert_booking->execute();


        $query_update_time = $connection->prepare(
            "UPDATE reservation_time SET reservation_time_status = 'A' WHERE id = '$reservation_time_id'"
        );
        $query_update_time->execute();

       $query_date_no_term = "SELECT *  FROM  reservation_time t1 INNER JOIN reservation_date t2 ON t2.id = t1.fk_reservation_date_id WHERE t2.reservation_date = '$date' AND t1.reservation_time_status = 'N'";
       $check_date_no_term = $connection->prepare($query_date_no_term);
       $check_date_no_term->execute();

       if($check_date_no_term->rowCount() <= 0) {
          $query_update_date_status = "UPDATE reservation_date SET reservation_date_status = 'N' WHERE reservation_date = '$date'";
          $query_update_date_status_exec = $connection->prepare($query_update_date_status);
          $query_update_date_status_exec->execute();
       }



       $myDateTime = DateTime::createFromFormat('Y-m-d',$date);
       $formatedDate = $myDateTime->format('d.m.Y.');

       $myTime = DateTime::createFromFormat('HH:ii',$time);

       $emailSubject = "Zahtev za termnin Kreativ Dent Lab";

       $mailBody = "<div>
        Poštovani, <br/>
        Primili smo zahtev za termina - dana ".$formatedDate." sa početkom u  ".$myTime." h. <br/>
        Termin se obrađuje. Uskoro ćemo Vas kontaktirati. <br/><br/>
        Srdačan pozdrav, <br/>
        Tim Kreativ Dent Lab
        </div>";


       //$bookingEmail = 'acasax@gmail.com';

       $adminEmailBody = "<div>
        Poštovani, <br/>
        Imate novi zahtev za termin - dana ".$formatedDate." sa početkom u  ".$myTime." h. <br/><br/>
        <table>
          <tr>
            <td style='width: 200px;'><b>Ime i prezime</b> </td><td style='width: 300px;'> ".$first_name." ".$last_name."</td>
          </tr>
          <tr>
            <td><b> E adresa </b></td><td> ".$email."</td>
          </tr>
          <tr>
            <td><b>Telefon</b> </td><td> ".$phone."</td>
          </tr>
          <tr>
            <td><b> Komentar</b> </td><td> ".$message."</td>
          </tr>
        </table>
        </div>";

       $send = $user->send_custom_email($email,$mailBody,$emailSubject);
       $sendAdmin = $user->send_custom_email('acasax@gmail.com',$adminEmailBody,$emailSubject);


        $connection->commit();
        if(!empty($result)) {
          $array=[];
          $array['type']="OK";
          $array['data']= "bookingSuccess";
          echo json_encode($array);
          return;
        }


    }catch (Exception $e) {
        $array=[];
        $array['type']="ERROR";
        $array['data']= $e->getMessage();
        $connection->rollBack();
        echo json_encode($array);
        return;
    }




}
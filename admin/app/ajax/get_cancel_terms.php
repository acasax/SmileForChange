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
    t4.appointment_email_body as myEmailBody FROM  booking_records t1 INNER JOIN reservation_time t2 ON t2.id = t1.fk_reservation_time_id INNER JOIN reservation_date t3 ON t3.id = t2.fk_reservation_date_id LEFT JOIN booking_appointment_data t4 ON t4.fk_booking_records_id = t1.id WHERE  t1.booking_recrod_status = '$status'  ";



   if(isset($_POST["search"]["value"]) && $_POST['search']['value'] !== '')
   {
      $search = $_POST["search"]["value"];
      $search = str_replace(".", "-", $search);
      $query .= "  AND ( t1.booking_email LIKE '%".$search."%' ";
      $query .= "  OR t1.booking_phone  LIKE  '%".$search."%'  ";
      $query .= "  OR t3.reservation_date  LIKE  '%".$search."%' ) ";
   }

   $query .= " AND t1.booking_record_year = '$year'";

   if(isset($_POST["order"])){
      $query .= ' ORDER BY reservation_date DESC ';
   }else{
      $query .= ' ORDER BY reservation_date ASC ';
   }

   if($_POST["length"] != -1) {
      $query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
   }



   //if($_POST['search']['value'] !== '')   var_dump($query);

   $statement = $connection->prepare($query);
   $statement->execute();
   $result = $statement->fetchAll();

   $filtered_rows = $statement->rowCount();
   $fetchAll =  $statement->rowCount();
   $data = array();
   $sub_array= array();

   foreach($result as $row){
      $termTime = str_pad($row['resTime'],2,"0",STR_PAD_LEFT) . ":00 h";
      $firstLastName = $row['firstName'] . " " . $row['lastName'];
      $sub_array= array();
      $sub_array[] = $row['date'];
      $sub_array[] = $termTime;
      $sub_array[] = $firstLastName;
      $sub_array[] = $row['email'];
      $sub_array[] = $row['phone'];
      $sub_array[] = '<div style="padding-left: 15%;"><button class="btn preview" aria-hidden="true"  id="'.$row["id"].'"    title="Pogledajte"></button>';
      $data[] =$sub_array;

   }


   $output = array(
    "draw"				=>	intval($_POST["draw"]),
    "recordsTotal"		=> 	$filtered_rows,
    "recordsFiltered"	=>	$fetchAll,
    "data"				=>	$data
   );


   echo json_encode($output);
?>


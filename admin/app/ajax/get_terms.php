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
   $status = 'A';
   $date = date('Y-m-d');

   $query = "SELECT id, reservation_date  FROM  reservation_date   WHERE reservation_date_year = '$year' AND reservation_date_status = '$status' AND reservation_date >= '$date'";

   if(isset($_POST["order"])){
      $query .= ' ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
   }else{
      $query .= ' ORDER BY reservation_date ASC ';
   }

   if($_POST["length"] != -1)
   {
      $query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
   }


   $statement = $connection->prepare($query);
   $statement->execute();
   $result = $statement->fetchAll();

   $filtered_rows = $statement->rowCount();
   $fetchAll =  $statement->rowCount();
   $data = array();
   $sub_array= array();

   foreach($result as $row){

      $res = $user->getMinMaxTimeForDateId($row['id']);
      //var_dump("SELECT reservation_time FROM reservation_time WHERE fk_reservation_date_id = '".$row['id']."' AND reservation_time_status = 'A'");
      $rservationDate =   $myDateTime = DateTime::createFromFormat('Y-m-d',$row['reservation_date']);
      $termDate = $myDateTime->format('d.m.Y');
      $sub_array= array();

      $sub_array[] = $termDate;
      $sub_array[] = $res['minTime'];
      $sub_array[] = $res['maxTime'];
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


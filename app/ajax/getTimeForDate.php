<?php
/**
 * Created by PhpStorm.
 * User: comp
 * Date: 11/24/2019
 * Time: 11:34 PM
 */

include './db.php';

if(isset($_POST['date'])){
    $date = $_POST["date"];
    $myDateTime = DateTime::createFromFormat('d-m-Y', $date);
    $date = $myDateTime->format('Y-m-d');
    $year = date('Y');
    $status = 'A';
    $query = "SELECT t2.reservation_time as reservationTime FROM  reservation_date t1 INNER JOIN reservation_time t2 ON t2.fk_reservation_date_id = t1.id WHERE t1.reservation_date = '$date' AND t1.reservation_date_year = '$year' AND t1.reservation_date_status = '$status' AND t2.reservation_time_status ='N'";
    $statement = $connection->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();

    $data = array();
    foreach ($result as $row){
        $data[] = $row['reservationTime'];
    }

    echo json_encode($data);

}
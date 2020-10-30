<?php
/**
 * Created by PhpStorm.
 * User: comp
 * Date: 11/25/2019
 * Time: 7:28 PM
 */


include './db.php';


    $year = date('Y');
    $status = 'A';
    $query = "SELECT reservation_date as reservationDate FROM  reservation_date WHERE reservation_date_year = '$year' AND reservation_date_status = '$status'";
    $statement = $connection->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();

    $data = array();
    foreach ($result as $row){
        $myDateTime = DateTime::createFromFormat('Y-m-d', $row['reservationDate']);
        $date = $myDateTime->format('d-m-Y');
        $data[] =$date ;
    }

    echo json_encode($data);
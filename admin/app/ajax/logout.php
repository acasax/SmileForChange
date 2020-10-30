<?php
   /**
    * Created by PhpStorm.
    * User: comp
    * Date: 11/30/2019
    * Time: 5:44 PM
    */
   include '../../../app/ajax/db.php';
   session_set_cookie_params(0);
   session_start();
   require_once('../../../app/ajax/User.php');
   $user = new USER();


   session_destroy();
   header("Location: ../../../login.html");
   $user->CloseCon();
   exit();
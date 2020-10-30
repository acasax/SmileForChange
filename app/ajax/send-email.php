<?php
   if($_POST)
   {
      include './db.php';
      require('./constant.php');
      require_once('./User.php');
      $user = new User();

      $user_name      = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
      $user_email     = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
      $user_phone     = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);
      $content   = filter_var($_POST["message"], FILTER_SANITIZE_STRING);

      if(empty($user_name)) {
         $empty[] = "<b>Ime i prezime</b>";
      }
      if(empty($user_email)) {
         $empty[] = "<b>E adresa</b>";
      }
      if(empty($user_phone)) {
         $empty[] = "<b>Telefon</b>";
      }
      if(empty($content)) {
         $empty[] = "<b>Poruka</b>";
      }

      if(!empty($empty)) {
         $output = json_encode(array('type'=>'error', 'text' => implode(", ",$empty) . ' Obavezno!'));
         die($output);
      }

      if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){ //email validation
         $output = json_encode(array('type'=>'error', 'text' => '<b>'.$user_email.'</b> je neispravan email, ispravite ga.'));
         die($output);
      }


      //reCAPTCHA validation
      if (isset($_POST['g-recaptcha-response'])) {

         require('../component/recaptcha/src/autoload.php');

         $recaptcha = new \ReCaptcha\ReCaptcha(SECRET_KEY, new \ReCaptcha\RequestMethod\SocketPost());

         $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

         if (!$resp->isSuccess()) {
            $output = json_encode(array('type'=>'error', 'text' => '<b>Captcha</b> validacija je obavezna!'));
            die($output);
         }
      }


      //unesi tvoj email
      $toEmail = "acasax@gmail.com";

      $mailBody = "<div>
            <p>Ime i prezime: ".$user_name."</p>
            <p>E adresa: ".$user_email."</p>
            <p>Telefon: ".$user_phone."</p>
            Poruka:</br>
            <p> ".$content."</p>
        </div>";



       $send = $user->send_mail($user_email,$mailBody,"Kreative Dent Lab");
       if($send){
          $array = array();
          $array['type']= 'message';
          $array['welcome'] = 'contactWelcome';
          $array['secMsg']= 'contactSecMsg';
          $array['username'] = $user_name;
         $output = json_encode($array);
         die($output);
      } else {
         $output = json_encode(array('type'=>'error', 'text' => 'Ne mogu poslati e-poštu, kontaktirajte našu službu.'));
         die($output);
      }
   }
?>
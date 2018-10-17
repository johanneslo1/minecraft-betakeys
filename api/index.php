<?php
//error_reporting(-1);
//ini_set('display_errors', true);

session_start();

header('Access-Control-Allow-Origin: *');

require_once '../includes/classes/Database.php';
require_once '../includes/classes/BetaKey.php';


function checkData($uuid, $key, $BetaKey) {
  // Create Betakey object first!

          //Entferne mögliche Trennstriche
          $key = str_replace("-", "", $key);

          if($BetaKey->checkKey($key)) {
            //Key existiert

            if(empty($BetaKey->getPlayerByKey($key))) {
              //Key wurde noch nicht benutzt
              if(empty($BetaKey->getKeyByPlayer($uuid))) {
                //Der Spieler ist nicht bereits in der Datenbank
                return true;

              } else {
                echo json_encode( array(
                  "status" => false,
                  "msg" => "Der Spieler hat bereits einen Key eingelöst!",
                  "errorField" => "username",
                  "uuid" => $uuid, "key" => $key));
              }
            } else {
              echo json_encode( array(
                "status" => false,
                "msg" => "Der Key wurde bereits eingelöst!",
                "errorField" => "key",
                "uuid" => $uuid, "key" => $key));
            }
          } else {
            echo json_encode( array(
              "status" => false,
              "msg" => "Der Key existiert nicht!",
              "errorField" => "key",
              "uuid" => $uuid, "key" => $key));
          }
          return false;
}



if(isset($_GET['check'])) {
  if(isset($_POST['uuid']) && isset($_POST['key']) && isset($_POST['g_recaptcha_response'])) {
    $url        = 'https://www.google.com/recaptcha/api/siteverify';
    $privatekey = "6LeobTUUAAAAAF6p6_-zjFrcWfl7ilDEckR2kBRX";

    $response = file_get_contents($url . "?secret=" . $privatekey . "&response=" . $_POST['g_recaptcha_response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
    $data     = json_decode($response);
    if (true) //isset($data->success) AND $data->success == true
      {
        $BetaKey = new BetaKey();

        $uuid = $_POST['uuid'];
        $key = $_POST['key'];

        if(checkData($uuid, $key, $BetaKey)) {
          // Der Key kann eingelöst werden, sprich bestätigung das man ihn wirklich einlösen will fehlt noch
          $_SESSION['canRedeemed'] = array(
            "status" => true,
            "uuid" => $uuid, "key" => $key);


          echo json_encode( array(
            "status" => true,
            "msg" => "Der Key kann nun eingelöst werden!",
            "errorField" => null,
            "uuid" => $uuid, "key" => $key));
        }

      } else {
        echo json_encode( array(
          "status" => false,
          "msg" => "Das Captcha wurde nicht bestätigt!",
          "errorField" => "captcha",
        ));
      }
  }
} else if(isset($_GET['redeem'])) {
  //Neues Captcha nicht nötig
  if(isset($_SESSION['canRedeemed'])) {
    $session = $_SESSION['canRedeemed'];

    $BetaKey = new BetaKey();
    //Erneute überprüfung, vlt. hat jmd. in der Zwischenzeit den Key eingelöst usw.
    if(checkData($session["uuid"], $session["key"], $BetaKey)) {

      $session["key"] = str_replace("-", "", $session["key"]);

      //$BetaKey->redeemKey($session["uuid"], $session["key"]);

      echo json_encode( array(
        "status" => true,
        "msg" => "Der Key wurde erfolgreich eingelöst!",
        "uuid" => $session["uuid"], "key" => $session["key"]));
    }
  }
}

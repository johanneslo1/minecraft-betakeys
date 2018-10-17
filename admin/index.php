<?php
error_reporting(-1);
ini_set('display_errors', true);

require_once '../includes/classes/Database.php';
require_once '../includes/classes/BetaKey.php';

$BetaKey = new BetaKey();

if(isset($_GET['createKeys'])) {
  if(isset($_POST['amount'])) {

    header("Content-type: text/plain");
    header("Content-Disposition: attachment; filename=keys.txt");

    $keys = $BetaKey->createKey($_POST['amount']);

    foreach($keys as &$key) {
      echo $BetaKey->formatKey($key)."\r\n";
    }
    die();
  } else {
    echo "Bitte die Anzahl der zu erstellenen Keys angeben!";
  }
}


if(isset($_GET['neutralizeKey'])) {
  if(isset($_POST['key'])) {
    $BetaKey->neutralizeKey($_POST['key']);
  } else {
    echo "Bitte einen Key angeben um diesen zu neutralisieren!";
  }
}

if(isset($_GET['deleteKey'])) {
  if(isset($_POST['key'])) {
    $BetaKey->deleteKey($_POST['key']);
  } else {
    echo "Bitte einen Key angeben um diesen zu löschen!";
  }
}

if(isset($_GET['getKeyByPlayer'])) {
  if(isset($_POST['uuid'])) {
    $key = $BetaKey->getKeyByPlayer(str_replace("-", "", $_POST['uuid'])); //Entferne - Zeichen aus UUID
    if(!empty($key)) {
      echo 'Der Key des <a target="_blank" href="https://mcuuid.net/?q='.$player.'">Spieler</a> ist: '.$key;
    } else {
      echo "Kein Spieler wurde mit der UUID ( ".$_POST['uuid']." ) gefunden";
    }
  } else {
    echo "Bitte die UUID angeben!";
  }
}

if(isset($_GET['getPlayerByKey'])) {
  if(isset($_POST['key'])) {
    $player = $BetaKey->getPlayerByKey(str_replace("-", "", $_POST['key']));
    if(!empty($player)) {
      echo 'Der Spieler des Keys '.$_POST['key'].' ist: <a target="_blank" href="https://mcuuid.net/?q='.$player.'">Spieler</a>';
    } else {
      echo "Es wurde kein Spieler mit dem Key ".$_POST['key']."gefunden";
    }
  } else {
    echo "Bitte gib den Key an!";
  }
}

if(isset($_GET['getAllKeys'])) {
  header("Content-type: text/plain");
  header("Content-Disposition: attachment; filename=keys.txt");

  $keys = $BetaKey->getAllKeys();

  foreach($keys as &$key) {
    echo $BetaKey->formatKey($key['Key'])."  -  ".$BetaKey->formatUUID($key['UUID'])."\r\n";
  }
  die();
}

if(isset($_GET['deleteAllKeys'])) {
  $BetaKey->deleteAllKeys();
  header('Location: index.php');
}

 ?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <!-- ======== Developed by Johannes Lohmann - Technikclou.com ======== -->

  	<meta charset="UTF-8">
  	<title>Beta-Keys Admin</title>
    <link rel="shortcut icon" type="image/png" href=../"assets/img/logo.png">
  </head>
  <body>
    <hr />
    Es existieren insgesamt <b><?php echo $BetaKey->countKeys(); ?></b> Keys<br />
    Es wurden <b><?php echo $BetaKey->countActiveKeys(); ?></b> Keys aktiviert (in Nutzung von Spieler)<br />
    Es sind <b><?php echo $BetaKey->countUnactiveKeys(); ?></b> ungenutzt
    <hr />
    <form role="form" action="?createKeys=1" method="post">
      <p>
        Hier kann man Keys erstellen, in dem Formular wird die Anzahl angegeben
      </p>
      <input name="amount" type="number" placeholder="Anzahl der Keys die erstellt werden sollen" required>
      <button type="submit">Erstellen</button>
    </form>
    <hr />
    <form role="form" action="?neutralizeKey=1" method="post">
      <p>
        Hier kann man einen bestimmten Key neutralisieren. D.h. der Key bleibt bestehen, aber der Spieler wird von dem key entfernt
      </p>
      <input name="key" type="text" placeholder="Key" required>
      <button type="submit">Neutralisieren</button>
    </form>
    <hr />
    <form role="form" action="?deleteKey=1" method="post">
      <p>
        Hier kann man einen bestimmten Key löschen. D.h. der Key wird aus der Datenbank entfernt, somit kann der Spieler der diesen eingelöst hat auch nicht mehr auf den Server kommen
      </p>
      <input name="key" type="text" placeholder="Key" required>
      <button type="submit">Löschen</button>
    </form>
    <hr />
    <form role="form" action="?getKeyByPlayer=1" method="post">
      <p>
        Hier kannst du mit dem Spieler(uuid) nach einem Key suchen
      </p>
      <input name="uuid" type="text" placeholder="uuid" required>
      <button type="submit">Suchen</button>
    </form>
    <hr />
    <form role="form" action="?getPlayerByKey=1" method="post">
      <p>
        Hier kannst du mit dem Key nach einem Spieler suchen
      </p>
      <input name="key" type="text" placeholder="key" required>
      <button type="submit">Suchen</button>
    </form>
    <hr />
    <a href="?getAllKeys=1">Alle Keys herunterladen</a><br />
    <a href="?deleteAllKeys=1">Alle Keys löschen</a>
  </body>
</html>

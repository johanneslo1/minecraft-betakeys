<?php

function random_string($option = 16) {
	if(function_exists('random_bytes')) {
		$bytes = random_bytes($option);
		$str = bin2hex($bytes);
	} else if(function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes($option);
		$str = bin2hex($bytes);
	} else if(function_exists('mcrypt_create_iv')) {
		$bytes = mcrypt_create_iv($option, MCRYPT_DEV_URANDOM);
		$str = bin2hex($bytes);
	} else {
		//Bitte euer_geheim_string durch einen zufälligen String mit >12 Zeichen austauschen
		$str = md5(uniqid('HypedCraft329ulaU§")UKdLASU)"§LOJUASj358023', true));
	}
	return $str;
}




class BetaKey
  {
    private $db;


      /**
      * __construct
      *
      * Create Databse conection
      *
      */
    public function __construct()
      {
        $this->db = new Database();
        $this->db = $this->db->dbConnect();
      }

      /**
      * countKeys
      *
      * Count all keys
      *
      * @return  int amount of keys
      */
    public function countKeys()
      {
        $statement = $this->db['ref_hypecraft']->prepare("SELECT null FROM WildCard");
        $statement->execute();

          return $statement->rowCount();
      }

      /**
      * countActiveKeys
      *
      * Count all redeemed keys
      *
      * @return  int amount of redeemed keys
      */
    public function countActiveKeys()
      {
        $statement = $this->db['ref_hypecraft']->prepare("SELECT null FROM WildCard WHERE UUID");
        $statement->execute();

          return $statement->rowCount();
      }

      /**
      * countUnactiveKeys
      *
      * Count all unactive keys
      *
      * @return  int amount of unactive keys
      */
    public function countUnactiveKeys()
      {
        $statement = $this->db['ref_hypecraft']->prepare("SELECT null FROM WildCard WHERE UUID = ?");
        $statement->execute(array(''));

          return $statement->rowCount();
      }

      /**
      * getAllKeys
      *
      * Get all entries from table
      *
      * @return  array entries from table
      * [{
      *   "UUID" => string (UUID unformatted),
      *   "Key" => string (Key unformatted)
      * }]
      */
    public function getAllKeys()
      {
        $statement = $this->db['ref_hypecraft']->prepare("SELECT * FROM WildCard");
        $statement->execute();

          return $statement->fetchAll();
      }



      /**
      * checkKey
      *
      * Check if key exists
      *
      * @return  string uuid (unformatted)
      */
    public function checkKey($key)
      {
        $statement = $this->db['ref_hypecraft']->prepare("SELECT `Key` FROM WildCard WHERE `Key` = ?");
        $statement->execute(array($key));

          return $statement->fetch();
      }


      /**
      * getPlayerByKey
      *
      * Get the uuid of a certain key
      *
      * @return  string uuid (unformatted)
      */
    public function getPlayerByKey($key)
      {
        $statement = $this->db['ref_hypecraft']->prepare("SELECT UUID FROM WildCard WHERE `Key` = ?");
        $statement->execute(array($key));

          return $statement->fetch()["UUID"];
      }

			/**
      * neutralizeKey
      *
      * Delete UUID from Key
      *
      */
    public function redeemKey($uuid, $key)
      {
        $statement = $this->db['ref_hypecraft']->prepare("UPDATE WildCard SET UUID = ? WHERE `Key` = ?");
        $statement->execute(array($uuid, $key));
      }


      /**
      * neutralizeKey
      *
      * Delete UUID from Key
      *
      */
    public function neutralizeKey($key)
      {
        $statement = $this->db['ref_hypecraft']->prepare("UPDATE WildCard SET UUID = ? WHERE `Key` = ?");
        $statement->execute(array('', $key));
      }

    public function getKeyByPlayer($UUID)
      {
        $statement = $this->db['ref_hypecraft']->prepare("SELECT `Key` FROM WildCard WHERE UUID = ?");
        $statement->execute(array($UUID));

          return $statement->fetch()['Key'];
      }

    public function deleteKey($key)
      {
        $statement = $this->db['ref_hypecraft']->prepare("DELETE FROM WildCard WHERE `Key` = ?");
        $statement->execute(array($key));

        if(!$this->checkKey($key)) //////////////////////////////////////////////////////
          return true;
      }

    public function deleteAllKeys()
      {
        $statement = $this->db['ref_hypecraft']->prepare("DELETE FROM WildCard");
        $statement->execute();
      }

      /**
      * createKey
      *
      * Create key or multiple keys
      *
      * @return  array keys
      * [
      *  string (Key formatted)
      * ]
      */
    public function createKey($amount = 1)
      {
        for ($i=0; $i < $amount; $i++) {

          $key = strtoupper(random_string(8));

          $keys[] = $key;

          $statement = $this->db['ref_hypecraft']->prepare("INSERT INTO WildCard (UUID, `Key`) VALUES (?, ?)");
          $statement->execute(array('', $key));

        }
        return $keys;
      }

    public function formatKey($key)
      {
        $key = chunk_split($key, 4, '-');
        return rtrim($key,"-");
      }

    public function formatUUID($uuid)
      {
          $uid = "";
          $uid .= substr($uuid, 0, 8)."-";
          $uid .= substr($uuid, 8, 4)."-";
          $uid .= substr($uuid, 12, 4)."-";
          $uid .= substr($uuid, 16, 4)."-";
          $uid .= substr($uuid, 20);
          return $uid;
      }
  }

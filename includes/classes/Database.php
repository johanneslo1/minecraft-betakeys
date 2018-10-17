<?php

class Database
  {
    //*******************************************************************************************
    //****************************************** Databse config *******************************************
    //*******************************************************************************************
    private $host = 'localhost';

    private $dbname = array('ref_hypecraft');

    private $user = '';
    private $password = '';


      /**
      * dbConnect
      *
      * Create for every Database a new object
      *
      * @return  array with pdo objects
      */

    public function dbConnect()
      {

        for($i=0; $i < count($this->dbname); $i++)
          {
            $objects[$this->dbname[$i]] = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname[$i], $this->user, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

          }
            return $objects;

      }
  }

?>

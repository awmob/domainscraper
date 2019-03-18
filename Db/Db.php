<?php

  namespace Db;

  Class Db{

    private $host;
    private $username;
    private $pass;
    private $db;
    private $charset;
    private $dsn;
    private $pdo_options;
    public $pdo;

    //construct and connect to PDO
    function __construct($host, $username, $pass, $db, $charset, $pdo_options){
      $this->host = $host;
      $this->username = $username;
      $this->pass = $pass;
      $this->db = $db;
      $this->charset = $charset;
      $this->pdo_options = $pdo_options;

      //set dsn settings
      $this->set_dsn();

    }

    private function set_dsn(){
      $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
      $this->dsn = $dsn;
    }

    public function db_connect(){
      try{

        $pdo = new \PDO($this->dsn, $this->username, $this->pass, $this->pdo_options);
        $this->pdo = $pdo;
      }
      catch (\PDOException $e){
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
      }
    }
  }

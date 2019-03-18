<?php
  error_reporting( E_ALL );

  //autoload classes
  spl_autoload_register(function ($class_name) {
    $class_name = str_replace("\\","/",$class_name);
    //echo $class_name . ".php"; //can be removed
    //echo "<br>";  //can be removed
    if (file_exists($class_name . ".php")) {
      require_once $class_name . ".php";
    }
  });

  function misc_loader($file_name){
    require_once $file_name . ".php";
  }

  //load config
  misc_loader("config/db_config");

  //load and declare the scrapers
  $scraper = new Scrapers\DomainScraper();

  //load database
  $db = new Db\Db($db_config['db_host'], $db_config['db_username'], $db_config['db_pass'], $db_config['db_database'], $db_config['charset'], $db_config['pdo_options']);



  ini_set('user_agent', 'MyBrowser v42.0.4711');

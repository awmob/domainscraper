<?php
  error_reporting( E_ALL );
  set_time_limit(0);

  session_start();

  //autoload classes
  spl_autoload_register(function ($class_name) {
    $class_name = str_replace("\\","/",$class_name);
    //echo $class_name . ".php"; //can be removed
    //echo "<br>";  //can be removed
    if (file_exists($class_name . ".php")) {
      require_once $class_name . ".php";
    }
  });

  //load require files
  function misc_loader($file_name){
    require_once $file_name . ".php";
  }

  //load config
  misc_loader("config/db_config");
  misc_loader("config/scraper_config");
  misc_loader("config/env_config");
  misc_loader("config/user_agents_config");





  //load database
  $pdomain = new Db\Db($db_config['db_host'], $db_config['db_username'], $db_config['db_pass'], $db_config['db_database'], $db_config['charset'], $db_config['pdo_options']);

  $pdo = $pdomain->get_pdo();

  $dbfuncs = new Db\DbFuncs();

  //load users
  $user = new User\User();


  //check logout
  if(isset($_GET['logout'])){
    if($_GET['logout']){
      session_unset();
      echo "You have logged out!";
    }
  }



  //check login
  if(isset($_POST['dologin'])){
    if($_POST['dologin'] == 1){
      //check login
      $login_set = $user->login($_POST['username'], $_POST['password'], $pdo, $dbfuncs);
      if($login_set){
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];

      }
    }
  }


  //check suburb file upload
  if(isset($_POST['suburb_submit'])){
    if($_POST['suburb_submit'] == 1){
      if(isset($_SESSION['logged_in'])){
        $files = new Files\Files();
        $files->parse_suburb_file('suburblist', $pdo, $dbfuncs);
      }
    }
  }


  //show the contents
  $pages = new PageShow\PageShow();

  //process pages in response to GET and POST
  require_once("page_process.php");

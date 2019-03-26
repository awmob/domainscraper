<?php

  namespace User;

  Class User{

    //log user in
    function login($username, $password, $pdo, $dbfuncs){
      //check the database for pass and username
      $user_db = $dbfuncs->get_user($pdo, $username);

      //user exists if size over 0
        if($user_db){
          //verify user
          if(password_verify($password, $user_db[0]['password'])){
            //login successful
            return true;
          }
          else{
            return false;
          }
        }
        else{
          //no user exists not logged in
          return false;
        }



    }


  }

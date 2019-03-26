<?php

  namespace Files;

    Class Files{
      function parse_suburb_file($submitname, $pdo, $dbfuncs){
        $contents = file_get_contents($_FILES[$submitname]["tmp_name"]);
        $lines = explode("\n",$contents);

        //go through each line
        foreach($lines as $ln){
          //separate into segments
          $bits = explode(",",$ln);
          $dbfuncs->add_suburbs($pdo, $bits[0],$bits[1], $bits[2], $bits[4], $bits[3]);
        }

      }
    }

<?php

  namespace DataShow;

  Class DataShow{

    private function seven_days(){
      $week = 604800;
      $seven_days_back = time() - $week;
      return $seven_days_back;
    }

    function ave_sales_week($pdo, $dbfuncs){
      $results = $dbfuncs->get_seven_day_ave_sales($pdo, $this->seven_days());

      //loop through results and present

      echo "<table cellpadding=\"10px\" border=\"1\">";
      echo "<tr><td>Sold Date</td><td>Average Sold Price</td><td># of Properties</td></tr>";
      foreach($results as $res){
        echo "<tr>";
        //convert date
        $mydate = date("d-M-Y",$res['sold_date_main']);
        $sale = floor($res['oursoldprice']);
        $counted = $res['counted'];

        echo "<td>$mydate</td><td>AUD \$$sale</td><td>$counted</td>";


        echo "</tr>";
      }
      echo "</table>";

    }

    function average_sales_suburb($pdo, $dbfuncs){
      $results = $dbfuncs->get_seven_day_ave_suburb($pdo, $this->seven_days());

      //show time period
      $past_date = date("d-M-Y",$this->seven_days());
      $now_date = date("d-M-Y",time());

      echo "<h4>$past_date to $now_date</h4>";

      echo "<table cellpadding=\"10px\" border=\"1\">";
      echo "<tr><td>Suburb</td><td>Average Sold Price</td><td># of Properties</td></tr>";
      foreach($results as $res){

        echo "<tr>";

          echo "<td>".$res['locality']."</td>";
          echo "<td>AUD \$".floor($res['avgprice'])."</td>";
          echo "<td>".$res['counted']."</td>";

        echo "</tr>";


      }
      echo "</table>";


    }//end function

    function top_property($pdo, $dbfuncs){
      $results = $dbfuncs->get_top_property($pdo, $this->seven_days());
      echo "Sell Price: AUD \$" . floor($results[0]['soldprice']) . "<br>";
      echo "Address: " . $results[0]['address_one'] . " " . $results[0]['locality'] . ", " . $results[0]['postcode'] . "<br>";
    }

    function bottom_property($pdo, $dbfuncs){
      $results = $dbfuncs->get_bottom_property($pdo, $this->seven_days());
      echo "Sell Price: AUD \$" . floor($results[0]['soldprice']) . "<br>";
      echo "Address: " . $results[0]['address_one'] . " " . $results[0]['locality'] . ", " . $results[0]['postcode'] . "<br>";
    }

  }

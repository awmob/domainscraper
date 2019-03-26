<?php

namespace Db;

Class DbFuncs{

  function get_top_property($pdo, $sevendays){
    $query = "select * from properties where soldprice = (select max(soldprice) from properties where sold_date_main > $sevendays) AND  sold_date_main > $sevendays";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $result;
  }

  function get_bottom_property($pdo, $sevendays){
    $query = "select * from properties where soldprice = (select min(soldprice) from properties where sold_date_main > $sevendays AND soldprice > 1) AND  sold_date_main > $sevendays AND soldprice > 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $result;
  }



  function get_seven_day_ave_sales($pdo, $sevendays){

    $query = "select avg(soldprice) as oursoldprice, count(soldprice) as counted, sold_date_main from properties where sold_date_main >= $sevendays AND soldprice > 1 group by sold_date_main";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $result;

  }

  function get_seven_day_ave_suburb($pdo, $sevendays){
    $query = "select avg(soldprice) as avgprice, locality, count(soldprice) as counted from properties where soldprice > 1 and sold_date_main > $sevendays AND soldprice > 1 group by locality order by avgprice desc";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $result;
  }

  function get_suburb_postcode_locality($pdo, $postcode, $locality){
    $query = "SELECT * FROM suburbs WHERE suburb_postcode = ? AND suburb_name = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([trim($postcode),trim($locality)]);
    $suburb = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if(sizeof($suburb) > 0){
      return $suburb;
    }
    else{
      return false;
    }


  }

  function check_property_exists($pdo, $property_id){
    $query = "SELECT count(*) as the_count from properties where domain_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([trim($property_id)]);
    $count = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if($count[0]['the_count'] > 0){
      return true;
    }
    else{
      return false;
    }
  }

  function add_property($pdo, $listing_id, $url, $price, $street_address, $locality, $state, $postcode, $sold_date, $suburb_id, $sold_date_parsed){
    $query = "INSERT INTO properties(domain_id, soldprice, address_one, locality, state, postcode, sold_date, suburb_id, add_date,sold_date_main) VALUES(?,?,?,?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([trim($listing_id), trim($price), trim($street_address), trim($locality), trim($state), trim($postcode), trim($sold_date),trim($suburb_id),time(),$sold_date_parsed]);
  }

  function get_user($pdo, $username){
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([trim($username)]);
    $user = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $user;
  }

  function add_suburbs($pdo, $postcode,$locality, $state, $latitude, $longitude){
    //first check if suburb exists
    $count = $this->check_suburb_exists($pdo, $postcode, $locality);

    //only add if none exist
    if($count == 0){
      echo "Adding $postcode, $locality...<br>";
      flush();
      $query = "INSERT INTO suburbs (suburb_name,suburb_postcode,state_id,longi,lati) VALUES(?,?,?,?,?)";
      $stmt = $pdo->prepare($query);

      $stmt->execute([trim($locality), trim($postcode),trim($state),trim($longitude),trim($latitude)]);

    }
    else{
      echo "$postcode, $locality Exists Not Added...<br>";
      flush();
    }
  }

  function check_suburb_exists($pdo, $postcode, $suburb_name){
    $query = "SELECT count(*) as the_count FROM suburbs WHERE suburb_postcode = ? AND suburb_name = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([trim($postcode),trim($suburb_name)]);
    $count = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $count[0]['the_count'];
  }



}

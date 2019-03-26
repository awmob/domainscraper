<?php

  namespace Scrapers;

  Class DomainScraper extends Scraper{


    //scrapes location and goes through iterations
    function scrape_location($uri, $pdo, $dbfuncs){
      ini_set('user_agent', $GLOBALS['user_agents_config'][0]);
      libxml_use_internal_errors(true);

      $data = file_get_contents($uri);

      $doc = new \DOMDocument();
      $doc->loadHTML($data);

      //get each listing
      $li = $doc->getElementsByTagName("li");


      foreach($li as $l){
        //get individual listings
        if($l->getAttribute("class") == "search-results__listing"){
          //get the id of the listing
          $listing_id = $l->getAttribute("data-testid");
          if($listing_id){
            $listing_id = str_replace("search-results__listing-","",$listing_id);
          }

          //get url of listing
          $ahref = $l->getElementsByTagName("a");
          foreach($ahref as $a){
            if($a->getAttribute("class") == "address is-two-lines listing-result__address"){
              $url = $a->getAttribute("href");
            }
          }

          //get sold price of listing - if withheld then set to 0
          $para = $l->getElementsByTagName("p");

          foreach($para as $pa){
            if($pa->getAttribute("class") == "listing-result__price"){
              $price = $pa->textContent;

              //remove anything except numbers
              $pattern = "/[^\d]/";
              $price = preg_replace($pattern,"",$price);
              if(!$price){
                $price = 0;
              }
            }
          }//end foreach

          //get address
          $spans = $l->getElementsByTagName("span");

          foreach($spans as $sp){
            //get address line 1
            if($sp->getAttribute("itemprop") == "streetAddress"){
              $street_address = $sp->textContent;
            }
            else if($sp->getAttribute("itemprop") == "addressLocality"){
              $locality = $sp->textContent;
            }
            else if($sp->getAttribute("itemprop") == "addressRegion"){
              $state = $sp->textContent;
            }
            else if($sp->getAttribute("itemprop") == "postalCode"){
              $postcode = $sp->textContent;
            }
            else if($sp->getAttribute("class") == "listing-result__tag is-sold"){
              $sold_date = $sp->textContent;
            }
          }//end spans foreach

          //insert into database
          //first check already not in db
          if($dbfuncs->check_property_exists($pdo, $listing_id)){
            echo "<p style=\"color:red\">Domain Property ID $listing_id Exists in Database - not added</p>";
            echo "<hr>";
            ob_flush();
            flush();
          }
          //not exist: add to database
          else{

            echo "<p style=\"color:yellow\">Adding Domain Property $listing_id to Database...</p>";

            echo "<p>Property Details:</p>";
            ?>
              <ul>
                <li>URL: <?php echo $url ?></li>
                <li>Sell Price: <?php if($price == 0){
                    echo "Price Not Available";
                  }
                  else{
                      echo "AUD \$$price";
                    } ?></li>
                <li>Address: <?php echo " $street_address, $locality, $state, $postcode"?></li>
                <li>Sale Type: <?php echo $sold_date ?></li>
              </ul>

            <?php

            //check suburb
            $suburb_get = $dbfuncs->get_suburb_postcode_locality($pdo, $postcode, $locality);
            if($suburb_get){
              //if exists then get the suburb id
              $suburb_id = $suburb_get[0]['id'];
              echo "Suburb Found...<br>";
            }
            else{
              //if doesnt exist add the suburb then get the suburb ID
              $dbfuncs->add_suburbs($pdo, $postcode,$locality, $state, null, null);
              $suburb_get = $dbfuncs->get_suburb_postcode_locality($pdo, $postcode, $locality);
              $suburb_id = $suburb_get[0]['id'];
              echo "Adding New Suburb to DB...<br>";
            }

            //parse sold date
            $sold_date_parsed = $this->parse_sold_date($sold_date);
            //convert date to epoch time
            $date_enter = strtotime($sold_date_parsed);

            //add property to database
            $dbfuncs->add_property($pdo, $listing_id, $url, $price, $street_address, $locality, $state, $postcode, $sold_date,$suburb_id,$date_enter);


            echo "<hr>";
          }//end else

        }//end if
      }//end foreach


    }

    private function parse_sold_date($sold_date){
      $pattern = "/^[\D]+([\d]+.*)$/i";

      if(preg_match($pattern, $sold_date, $match)){

        return $match[1];
      }
      else{
        return false;
      }

    }



    function scrape_auctions($uri){
      ini_set('user_agent', $GLOBALS['user_agents_config'][0]);
      libxml_use_internal_errors(true);

      $doc = new \DOMDocument();
      $doc->loadHTML(file_get_contents($uri));

      //need to save to db, but first need to check if this data exists in db

      return $doc;
    }

    function get_title($doc){
      $title = $doc->getElementsByTagName("title");
      return $title[0]->textContent;
    }

    function get_meta($doc){
      //get clearance rate and show
      $meta = $doc->getElementsByTagName("meta");
      foreach($meta as $m){
        if($m->getAttribute("name") == "description"){
          return $m->getAttribute("content");
        }
      }

      //no results
      return false;
    }

     function parse_script_json($doc){
      $scripts = $doc->getElementsByTagName("script");

      //auction contents
      $auction_data = $scripts[2]->textContent; //save this into db

      $auction_data = str_replace("window['__domain_group/APP_PROPS'] = ", "", $auction_data);
      $auction_data = str_replace(",\"baseUrl\":\"https://www.domain.com.au\"}; window['__domain_group/APP_PAGE'] = 'auction-results'", "", $auction_data);


      //separate the data
      $delim = "},{";

      $auction_bits = explode($delim, $auction_data);

      return $auction_bits;
    }

    //insert values into the database
    //Need add functions to check for duplicate entries
    function auction_insert($suburb, $price, $address, $pdo){
        $sql = "INSERT INTO domain_auctions (suburb, price, address) VALUES (?,?,?)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute([$suburb, $price, $address]);
    }





  }

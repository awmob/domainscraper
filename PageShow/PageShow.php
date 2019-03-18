<?php

  namespace PageShow;

  Class PageShow{


    private function show_basic_header(){
      ?>

      <!doctype html>
      <html lang="en">
        <head>
          <!--<link rel="shortcut icon" href="https://www.advantageholidays.com.au/storage/favicon.ico">
          <script src="https://www.advantageholidays.com.au/js/back_process.js"></script>-->
          <link href="css/main.css" rel="stylesheet">
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">




      <?php
    }


    private function show_basic_footer(){
      ?>
            <footer>

            </footer>
          </body>
        </html>

      <?php
    }

    function show_home_page(){

      $this->show_basic_header();

      ?>
          <title><?php echo  $GLOBALS['env_config']['app_name']  ?> | Home Page</title>
        </head>

        <body>

          <h1>Screen Scraper Home</h1>

          <p>Future implementation: Password protected access only, cron job</p>


          <div>

            Select a site to crawl:
            <form method="GET" action="index.php">
              <select name="site">
                <option value="<?php echo $GLOBALS['sites_config']['domain']['index'] ?>">Domain</option>
              </select>
              <input type="hidden" name="crawl_status" value="1">
              <input type="submit" value="Start Crawling">
            </form>

          </div>



      <?php

      $this->show_basic_footer();

    }//end function


    function show_domain_crawler($domain_crawler){
      $this->show_basic_header();

      ?>
        <title><?php echo  $GLOBALS['env_config']['app_name']  ?> | Domain Crawler</title>
      </head>

      <body class="crawling">
        <h2>Crawling Domain.com.au...</h2>

        <?php

          echo "<p>Getting Sydney Auction Data</p>";

          $doc = $domain_crawler->scrape_auctions($GLOBALS['sites_config']['domain']['sydney_auction_results_uri']);

          ob_flush();
          flush();

          echo "<h4>".$domain_crawler->get_title($doc)."</h4>";

          ob_flush();
          flush();

          //get json price etc
          $jsondata = $domain_crawler->parse_script_json($doc);

          echo "<table>";
          echo "<tr><td>Suburb</td><td>Address</td><td>Sell Price AUD$</td></tr>";
          //loop json and present data
          foreach($jsondata as $auct){

            $json = json_decode("{".$auct."}");

            if($json){
              if(isset($json->suburb) && isset($json->price)  && isset($json->streetType) ){
                echo "<tr>";
                echo "<td>" . $json->suburb."</td>";
                echo "<td>" . $json->streetNumber. " " . $json->streetName. " " . $json->streetType."</td>";
                echo "<td>$" . $json->price ."</td>";
                echo "</tr>";
                echo PHP_EOL;
              }

            }

          }
          echo "</table>";



    }

  }

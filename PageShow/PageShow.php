<?php

  namespace PageShow;

  Class PageShow{


    private function show_basic_header(){
      ?>

      <!doctype html>
      <html lang="en">
        <head>

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

    function show_home_page($login_status = false){

      $this->show_basic_header();

      ?>
          <title><?php echo  $GLOBALS['env_config']['app_name']  ?> | Home Page</title>
        </head>

        <body>
          <?php $this->show_login_details($login_status); ?>

          <h1>Property Web Crawler Home</h1>

          <?php
            //if logged in then show logged in segment
            if($login_status){
              $this->show_home_logged_in();
            }
            else{
              $this->home_show_login();
            }

          ?>


      <?php

      $this->show_basic_footer();

    }//end function

    function home_show_login(){
      ?>


      <h3>Enter your login details:</h3>

      <form method="POST" action="index.php">
        <input type="hidden" name="dologin" value="1">
        <input type="text" name="username"> Username<br><br>
        <input type="password" name="password"> Password<br>
        <input type="submit" value="Login">
      </form>


      <?php
    }


    function show_home_logged_in(){
      ?>

                <div>
                  <hr>

                  <h4>Crawl Data</h4>

                  Select data to crawl:
                  <form method="GET" action="index.php">
                    <select name="site">
                      <option value="1">Domain - Latest Weekly Sydney Property Auction Results</option>
                      <option value="2">Domain - Crawl Sold Sydney Listings</option>
                    </select>
                    <input type="hidden" name="crawl_status" value="1">
                    <input type="submit" value="Start Crawling">
                  </form>

                </div>


                <div style="padding-top: 20px;">
                  <hr>

                  <h4>View Crawled Results</h4>

                  Select data to crawl:
                  <form method="GET" action="index.php">
                    <select name="site">
                      <option value="3">Domain - View Sydney Listings</option>
                    </select>
                    <input type="hidden" name="crawl_status" value="1">
                    <input type="submit" value="View Crawl Results">
                  </form>

                </div>

                <div style="padding-top: 20px;">
                  <hr>
                  <h4>Download Code Base and Database</h4>

                  <p><a href="/domain_crawler.zip"></a></p>

                </div>





                <div style="padding-top: 20px;">
                  <hr>
                  <h4>Add New Suburbs</h4>
                  <p>Upload csv file with Suburb data.</p>


                    <ul>
                      <li>Postcode</li>
                      <li>Locality</li>
                      <li>State 2 or 3 letter code</li>
                      <li>Latitude</li>
                      <li>Longitude</li>
                    </ul>


                  <form method="POST" action="index.php" enctype="multipart/form-data">

                    <input type="hidden" name="suburb_submit" value="1">

                    <input type="file" name="suburblist" id="suburblist"><br>

                    <input type="submit" value="Upload Suburbs">

                  </form>
                </div>

        <?php
    }

    //displays login username and logout link
    private function show_login_details($login_status){
      ?>

      <div><a href = "/">Home</a></div>

      <?php
      if($login_status){
        ?>
          <div>Logged in as: <?php echo $_SESSION['username'] ?> : <a href="index.php?logout=true">LOGOUT</a></div>
        <?php
      }
    }


    function show_location_crawler($login_status, $domain_crawler, $uri, $crawl_iterations, $pdo, $dbfuncs){
      $this->show_basic_header();

      ?>
        <title><?php echo  $GLOBALS['env_config']['app_name']  ?> | Domain Crawler</title>
      </head>

      <body class="crawling">

        <?php $this->show_login_details($login_status); ?>

        <h2>Crawling Domain.com.au Property Listings...</h2>


        <?php

          //show domain page

          //loop goes here
          for($x = 1; $x <= $crawl_iterations; $x++){
            $sleep = rand(1,3);
            sleep($sleep);
            echo "Sleeping $sleep second/s... ";
            echo "Crawling Page $x of $crawl_iterations<br>";
            flush();

            if($x > 1){
              $uri .= $GLOBALS['sites_config']['domain']['page_var'] . $x;
            }

            $domain_crawler->scrape_location($uri, $pdo, $dbfuncs);
          }

          $this->show_basic_footer();

    }


    function show_domain_data($data_show, $login_status, $pdo, $dbfuncs){
      $this->show_basic_header();

      ?>
        <title><?php echo  $GLOBALS['env_config']['app_name']  ?> | Domain Results</title>
      </head>

      <body class="crawling">

        <?php $this->show_login_details($login_status); ?>

        <h2>Showing Some Domain Data</h2>

        <section class="blocker">
          <h4>Top Sydney Property from Past Week</h4>
          <?php $data_show->top_property($pdo, $dbfuncs); ?>

        </section>

        <section class="blocker">
          <h4>Bottom Sydney Property from Past Week</h4>
          <?php $data_show->bottom_property($pdo, $dbfuncs); ?>

        </section>

        <section class="blocker">
          <h4>Past Week Domain Average Sales Price</h4>
          <?php $data_show->ave_sales_week($pdo, $dbfuncs);?>

        </section>

        <section class="blocker">
          <h4>Past Week Domain Average Sales Price by Suburb</h4>
          <?php $data_show->average_sales_suburb($pdo, $dbfuncs);?>

        </section>



        <?php

        $this->show_basic_footer();

      }


    function show_domain_crawler($domain_crawler, $login_status = false, $pdo = false){
      $this->show_basic_header();

      ?>
        <title><?php echo  $GLOBALS['env_config']['app_name']  ?> | Domain Crawler</title>
      </head>

      <body class="crawling">

        <?php $this->show_login_details($login_status); ?>

        <h2>Crawling Domain.com.au Auction Results for Past Week...</h2>



        <?php

          echo "<p>Getting Sydney Auction Data</p>";

          $doc = $domain_crawler->scrape_auctions($GLOBALS['sites_config']['domain']['sydney_auction_results_uri']);


          flush();

          echo "<h4>".$domain_crawler->get_title($doc)."</h4>";


          flush();

          //get json price etc
          $jsondata = $domain_crawler->parse_script_json($doc);

          echo "<table>";
          echo "<tr><td>Suburb</td><td>Address</td><td>Sell Price AUD$</td></tr>";

          $price = 0;
          $count = 0;
          //loop json and present data
          foreach($jsondata as $auct){

            $json = json_decode("{".$auct."}");

            if($json){
              if(isset($json->suburb) && isset($json->price)  && isset($json->streetType) ){
                ++$count;
                $price += $json->price;
                echo "<tr>";
                echo "<td>" . $json->suburb."</td>";
                echo "<td>" . $json->streetNumber. " " . $json->streetName. " " . $json->streetType."</td>";
                echo "<td>$" . $json->price ."</td>";
                echo "</tr>";
                echo PHP_EOL;

                $address = $json->streetNumber. " " . $json->streetName. " " . $json->streetType;
                /*
                  Database entry disabled for demo

                  //this call will enter data into dbase in live version*/

                  //$domain_crawler->auction_insert($json->suburb, $json->price, $address, $pdo);

              }

            }

          }


          echo "</table>";

          echo "<div style=\"margin-top: 20px;\">Average Price: $". floor($price / $count) . "<br>";


          echo "</div>";






    }

  }

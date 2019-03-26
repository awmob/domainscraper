<?php

  $show_index = true;

  $login_status = false;
  //check logged in
  if(isset($_SESSION['logged_in'])){
    if($_SESSION['logged_in']){
      $login_status = true;
    }
  }

  if($login_status){
    //crawling

    if(isset($_GET['crawl_status']) && isset($_GET['site'])) {
      //load and declare the scrapers
      if($_GET['crawl_status'] == 1){
        //Show Domain and Scrape Domain
        if($_GET['site'] == 1){
          $show_index = false;
          //load the domain scraper
          $domain_crawler = new Scrapers\DomainScraper($scraper_config['base_iterations']);
          //show domain page
          $pages->show_domain_crawler($domain_crawler, $login_status);
        }
        //Show Domain and Scrape Domain
        else if($_GET['site'] == 2){
          $show_index = false;
          //load the domain scraper
          $domain_crawler = new Scrapers\DomainScraper($scraper_config['base_iterations']);

          $pages->show_location_crawler($login_status, $domain_crawler,$sites_config['domain']['sydney_sold_listings'], $scraper_config['base_iterations'], $pdo, $dbfuncs);

        }

        //show some results
        else if($_GET['site'] == 3){
          $data_show = new DataShow\DataShow();
          $show_index = false;
          $pages->show_domain_data($data_show, $login_status, $pdo, $dbfuncs);


        }
      }



    }//end crawl status
  }//end login status


  //show home page if no other page
  if($show_index){

    $pages->show_home_page($login_status);

  }

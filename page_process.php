<?php

  $show_index = true;

  //crawling
  if(isset($_GET['crawl_status']) && isset($_GET['site'])) {
    //load and declare the scrapers
    if($_GET['crawl_status'] == 1){

      //Show Domain and Scrape Domain
      if($_GET['site'] == $GLOBALS['sites_config']['domain']['index']){

        $show_index = false;

        //load the domain scraper
        $domain_crawler = new Scrapers\DomainScraper($scraper_config['base_iterations']);

        //show domain page
        $pages->show_domain_crawler($domain_crawler);

      }
    }

  }


  //show home page if no other page
  if($show_index){

    $pages->show_home_page();

  }

<?php

/*

  config for scrapers
*/
global $scraper_config;

$scraper_config = array(

  'base_iterations' => 2

);


global $sites_config;

$sites_config = array(
  'domain' => [

    'page_var' => '&page=',
    'property_uri' => 'https://www.domain.com.au',
    'sydney_auction_results_uri' => 'https://www.domain.com.au/auction-results/sydney/',
    'sydney_sold_listings' => 'https://www.domain.com.au/sold-listings/sydney-region-nsw/house/?ssubs=1'

  ]


);

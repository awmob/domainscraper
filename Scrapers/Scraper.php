<?php

  namespace Scrapers;

  Abstract Class Scraper{

    protected $iterations;

    function __construct($iterations){
      $this->iterations = $iterations;
    }

    public function get_iterations(){
      return $this->iterations;
    }

  }

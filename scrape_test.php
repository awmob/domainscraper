<?php



  $url = 'https://www.domain.com.au/auction-results/sydney/';
  //$url = 'http://localhost/property_scraper/dommy.html';

  //ini_set('user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0' );
  ini_set('user_agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A5370a Safari/604.1' );

  libxml_use_internal_errors(true);

  //get script contents
  $doc = new DOMDocument();
  $doc->loadHTML(file_get_contents($url));

  //get title and show
  $title = $doc->getElementsByTagName("title");
  echo $title[0]->textContent . "<br>";

  //get clearance rate and show
  $meta = $doc->getElementsByTagName("meta");
  foreach($meta as $m){
    if($m->getAttribute("name") == "description"){
      echo $m->getAttribute("content") . "<br>";
    }
  }


  $scripts = $doc->getElementsByTagName("script");

  //auction contents
  $auction_data = $scripts[2]->textContent; //save this into db

  $auction_data = str_replace("window['__domain_group/APP_PROPS'] = ", "", $auction_data);
  $auction_data = str_replace(",\"baseUrl\":\"https://www.domain.com.au\"}; window['__domain_group/APP_PAGE'] = 'auction-results'", "", $auction_data);


  //separate the data
  $delim = "},{";

  $auction_bits = explode($delim, $auction_data);
  $json = json_decode("{".$auction_bits[3]."}");

  foreach($auction_bits as $auct){
    $json = json_decode("{".$auct."}");

    if($json){
      if(isset($json->suburb) && isset($json->price) ){
        echo $json->suburb . " - $" . $json->price;
      }

    }

  }



/*
  $doc = new DOMDocument();
  $doc->loadHTML(file_get_contents($url));
  $img_arr = $doc->getElementsByTagName('img');
  foreach($img_arr as $img){
    $src =  $img->getAttribute("src");
    var_dump($src);
  }
*/


/*
  //$classname = 'testme';
  $classname = 'width-setter';
  //$classname = 'auction-details';
  $doc = new DOMDocument();
  $doc->loadHTML(file_get_contents($url));
  //$summary = $doc->getElementsByTagName('auction-details');

  $img = $doc->getElementsByTagName("img");

  foreach($img as $i){
    $att = $i->getAttribute("class");
    if($att == $classname){
      echo $i->getAttribute("src") . "<br>";
    }
  }
*/
  //$xpath = new DOMXpath($doc);
  ///$results = $xpath->query("//*[@class='" . $classname . "']");



  //$summary = $doc->getElementsByTagName('width-setter');
  // also have $doc->getElementsByTagName , etc
  //var_dump($summary);

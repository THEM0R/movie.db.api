<?php

use engine\ParserApi;

require 'config.php';
require 'parser.php';

$category = 1;

/**/

$parser = new ParserApi();

// ini
$Api = 'c785dff1-f7a7-4637-a7f3-d2ab0161d19e';
$Move_url = 'https://kinopoiskapiunofficial.tech/api/v2.2/films/301';
$url = 'https://kinopoiskapiunofficial.tech/api/';
$list_url = 'https://kinopoiskapiunofficial.tech/api/v2.2/films/top?type=TOP_250_BEST_FILMS&page=1';
// ini

$filters = 'https://kinopoiskapiunofficial.tech/api/v2.2/films/filters';

//$res = getArrayToUrl($list_url, $Api);

//$res = json_decode(json_encode($res),true); // обєкт в масив

//$pages = $res['pagesCount'];
//
//$movies = $res['films'];

$parser->parser($filters, $Api, $category);

/**/
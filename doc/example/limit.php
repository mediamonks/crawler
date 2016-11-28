<?php

use MediaMonks\Crawler\Crawler;
use MediaMonks\Crawler\Url\Matcher;

require_once __DIR__ . '/../../vendor/autoload.php';

$crawler = new Crawler;

// crawler will stop after 10 pages were returned in the loop
$crawler->setLimit(10);

foreach($crawler->crawl('https://www.yourwebsite.com') as $page) {
    echo $page->getUrl() . PHP_EOL;
}

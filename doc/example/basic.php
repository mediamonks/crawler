<?php

use MediaMonks\Crawler\Crawler;

require_once __DIR__ . '/../../vendor/autoload.php';

$crawler = new Crawler;
foreach($crawler->crawl('https://www.yourwebsite.com') as $page) {
    echo $page->getUrl() . PHP_EOL;
}

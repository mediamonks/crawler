<?php

use MediaMonks\Crawler\Crawler;

require_once __DIR__ . '/../../vendor/autoload.php';

$crawler = new Crawler;

// set any PSR-3 logger to get extra information on what the crawler is doing
$crawler->setLogger(new \Psr\Log\NullLogger());

foreach($crawler->crawl('https://www.yourwebsite.com') as $page) {
    echo $page->getUrl() . PHP_EOL;
}

<?php

use MediaMonks\Crawler\Crawler;
use MediaMonks\Crawler\Url\Matcher;

require_once __DIR__ . '/../../vendor/autoload.php';

$crawler = new Crawler;

// when a page can not be requested an exception will be thrown, by default this page is simply skipped
$crawler->setExceptionOnError(true);

foreach($crawler->crawl('https://www.yourwebsite.com') as $page) {
    echo $page->getUrl() . PHP_EOL;
}

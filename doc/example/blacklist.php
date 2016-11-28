<?php

use MediaMonks\Crawler\Crawler;
use MediaMonks\Crawler\Url\Matcher;

require_once __DIR__ . '/../../vendor/autoload.php';

$crawler = new Crawler;

// all pages except the ones starting with /foo will be returned in the loop
$crawler->addBlacklistUrlMatcher(new Matcher\PathRegexUrlMatcher('~^/foo~'));

foreach($crawler->crawl('https://www.yourwebsite.com') as $page) {
    echo $page->getUrl() . PHP_EOL;
}

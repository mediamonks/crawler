<?php

use MediaMonks\Crawler\Crawler;
use MediaMonks\Crawler\Url\Normalizer;

require_once __DIR__ . '/../../vendor/autoload.php';

$crawler = new Crawler;

// pages with /page?q=foo & /page?q=bar will only be returned once as /page
$crawler->addUrlNormalizer(new Normalizer\RemoveQueryParameterUrlNormalizer('q'));

foreach($crawler->crawl('https://www.yourwebsite.com') as $page) {
    echo $page->getUrl() . PHP_EOL;
}

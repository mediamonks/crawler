<?php

namespace tests\MediaMonks\Crawler;

use MediaMonks\Crawler\Page;
use MediaMonks\Crawler\Url;
use Mockery as m;
use Symfony\Component\DomCrawler\Crawler;

class PageTest extends \PHPUnit_Framework_TestCase
{
    public function test_getters()
    {
        $url = m::mock(Url::class);
        $domCrawler = m::mock(Crawler::class);

        $page = new Page($url, $domCrawler);

        $this->assertEquals($url, $page->getUrl());
        $this->assertEquals($domCrawler, $page->getCrawler());
    }
}

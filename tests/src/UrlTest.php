<?php

namespace tests\MediaMonks\Crawler;

use League\Uri\Schemes\Http;
use MediaMonks\Crawler\Url;
use Mockery as m;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function test_instance_of_http_uri()
    {
        $url = Url::createFromString('http://my-site');
        $this->assertInstanceOf(Url::class, $url);
        $this->assertInstanceOf(Http::class, $url);
    }
}

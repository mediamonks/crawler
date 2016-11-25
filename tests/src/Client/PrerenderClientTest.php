<?php

namespace tests\MediaMonks\Crawler\Client;

use MediaMonks\Crawler\Client\PrerenderClient;

class PrerenderClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_url_is_prepended()
    {
        $prerenderUrl = 'http://my-prerender-server/';
        $websiteUrl = 'http://my-website/';

        $method = get_non_public_method(PrerenderClient::class, 'getAbsoluteUri');
        $client = new PrerenderClient($prerenderUrl);
        $result = $method->invokeArgs($client, [$websiteUrl]);

        $this->assertEquals($prerenderUrl.$websiteUrl, $result);
    }
}

<?php

namespace tests\MediaMonks\Crawler\Client;

use MediaMonks\Crawler\Client\PrerenderClient;
use Symfony\Component\BrowserKit\Request;

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

    public function test_url_is_corrected()
    {
        $prerenderUrl = 'http://my-prerender-server/';
        $websiteUrl = 'http://my-website/';

        $request = new Request($websiteUrl, 'GET');

        $rp = new \ReflectionProperty(PrerenderClient::class, 'request');
        $rp->setAccessible(true);

        $client = new PrerenderClient($prerenderUrl);
        $rp->setValue($client, $request);

        $this->assertEquals($client->getRequest()->getUri(), $websiteUrl);
    }
}

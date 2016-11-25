<?php

namespace tests\MediaMonks\Crawler\Client;

use MediaMonks\Crawler\Client\PrerenderIoClient;

class PrerenderIoClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_url_is_prepended()
    {
        $token = 'my-prerender.io-token';
        $websiteUrl = 'http://my-website/';

        $method = get_non_public_method(PrerenderIoClient::class, 'getAbsoluteUri');
        $client = new PrerenderIoClient($token);
        $result = $method->invokeArgs($client, [$websiteUrl]);

        $this->assertEquals(PrerenderIoClient::URL.$websiteUrl, $result);
        $this->assertEquals(
            PrerenderIoClient::USER_AGENT,
            $client->getServerParameter(PrerenderIoClient::HEADER_USER_AGENT)
        );
        $this->assertEquals($token, $client->getServerParameter(PrerenderIoClient::HEADER_TOKEN));
    }
}

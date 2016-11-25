<?php

namespace tests\MediaMonks\Crawler;

use MediaMonks\Crawler\Crawler;
use MediaMonks\Crawler\Url\Matcher\UrlMatcherInterface;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\BrowserKit\Client;

class CrawlerTest extends \PHPUnit_Framework_TestCase
{
    public function test_default_values()
    {
        $crawler = new Crawler();

        $this->assertInstanceOf(Client::class, $crawler->getClient());
        $this->assertEquals(0, $crawler->getLimit());
        $this->assertInstanceOf(NullLogger::class, $crawler->getLogger());
        $this->assertFalse($crawler->getStopOnError());
        $this->assertCount(0, $crawler->getUrlsCrawled());
        $this->assertCount(0, $crawler->getUrlsQueued());
        $this->assertCount(0, $crawler->getUrlsRejected());
        $this->assertCount(0, $crawler->getUrlsReturned());
        $this->assertCount(0, $crawler->getWhitelistUrlMatchers());
        $this->assertCount(0, $crawler->getBlacklistUrlMatchers());
    }

    public function test_getters_setters()
    {
        $crawler = new Crawler();

        $client = new \Goutte\Client();
        $crawler->setClient($client);
        $this->assertEquals($client, $crawler->getClient());

        $limit = 1;
        $crawler->setLimit($limit);
        $this->assertEquals($limit, $crawler->getLimit());

        $crawler->setStopOnError(true);
        $this->assertTrue($crawler->getStopOnError());

        $logger = m::mock(NullLogger::class);
        $crawler->setLogger($logger);
        $this->assertEquals($logger, $crawler->getLogger());
    }

    public function test_options()
    {
        $logger = m::mock(NullLogger::class);

        $crawler = new Crawler(null, [
            'limit' => 1,
            'stop_on_error' => true,
            'logger' => $logger,
            'whitelist_url_matchers' => [
                m::mock(UrlMatcherInterface::class)
            ],
            'blacklist_url_matchers' => [
                m::mock(UrlMatcherInterface::class)
            ]
        ]);

        $this->assertEquals(1, $crawler->getLimit());
        $this->assertTrue($crawler->getStopOnError());
        $this->assertEquals($logger, $crawler->getLogger());
        $this->assertCount(1, $crawler->getWhitelistUrlMatchers());
        $this->assertCount(1, $crawler->getBlacklistUrlMatchers());
    }


}

<?php

namespace tests\MediaMonks\Crawler;

use MediaMonks\Crawler\Crawler;
use MediaMonks\Crawler\Exception\RequestException;
use MediaMonks\Crawler\Url;
use MediaMonks\Crawler\Url\Matcher\PathRegexUrlMatcher;
use MediaMonks\Crawler\Url\Matcher\UrlMatcherInterface;
use MediaMonks\Crawler\Url\Normalizer\CallbackUrlNormalizer;
use Mockery as m;
use Psr\Log\NullLogger;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

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

    public function test_clear_matchers()
    {
        $matcher = m::mock(UrlMatcherInterface::class);

        $crawler = new Crawler();
        $crawler->addBlacklistUrlMatcher($matcher);
        $crawler->addWhitelistUrlMatcher($matcher);

        $this->assertCount(1, $crawler->getBlacklistUrlMatchers());
        $this->assertCount(1, $crawler->getWhitelistUrlMatchers());

        $crawler->clearBlacklistUrlMatchers();
        $crawler->clearWhitelistUrlMatchers();

        $this->assertCount(0, $crawler->getBlacklistUrlMatchers());
        $this->assertCount(0, $crawler->getWhitelistUrlMatchers());

        $crawler->setBlacklistUrlMatchers([$matcher]);
        $crawler->setWhitelistUrlMatchers([$matcher]);

        $this->assertCount(1, $crawler->getBlacklistUrlMatchers());
        $this->assertCount(1, $crawler->getWhitelistUrlMatchers());

        $crawler->clearBlacklistUrlMatchers();
        $crawler->clearWhitelistUrlMatchers();

        $this->assertCount(0, $crawler->getBlacklistUrlMatchers());
        $this->assertCount(0, $crawler->getWhitelistUrlMatchers());
    }

    public function test_clear_normalizers()
    {
        $normalizer = m::mock(Url\Normalizer\UrlNormalizerInterface::class);

        $crawler = new Crawler();

        $crawler->addUrlNormalizer($normalizer);
        $this->assertCount(1, $crawler->getUrlNormalizers());

        $crawler->clearUrlNormalizers();
        $this->assertCount(0, $crawler->getUrlNormalizers());

        $crawler->setUrlNormalizers([$normalizer]);
        $this->assertCount(1, $crawler->getUrlNormalizers());

        $crawler->clearUrlNormalizers();
        $this->assertCount(0, $crawler->getUrlNormalizers());
    }

    public function test_crawl_single_page()
    {
        $domCrawler = new DomCrawler('<html></html>');

        $client = $this->getClient();
        $client->shouldReceive('request')->once()->andReturn($domCrawler);
        $client->shouldReceive('getInternalRequest')->once()->andReturn(null);

        $crawler = new Crawler($client);

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(1, $crawler->getUrlsCrawled());
    }

    public function test_crawl_multiple_pages()
    {
        $crawler = new Crawler($this->getDummyClient());

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(5, $crawler->getUrlsCrawled());
        $this->assertCount(5, $crawler->getUrlsReturned());
        $this->assertCount(2, $crawler->getUrlsRejected());
    }

    public function test_crawl_with_limit()
    {
        $crawler = new Crawler($this->getDummyClient());
        $crawler->setLimit(3);

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(3, $crawler->getUrlsCrawled());
        $this->assertCount(2, $crawler->getUrlsQueued());
    }

    public function test_crawl_with_whitelist()
    {
        $crawler = new Crawler($this->getDummyClient());
        $crawler->addWhitelistUrlMatcher(new PathRegexUrlMatcher('~^/page_1.html~'));

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(5, $crawler->getUrlsCrawled());
        $this->assertCount(1, $crawler->getUrlsReturned());
    }

    public function test_crawl_with_blacklist()
    {
        $crawler = new Crawler($this->getDummyClient());
        $crawler->addBlacklistUrlMatcher(new PathRegexUrlMatcher('~^/page_1.html~'));

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(5, $crawler->getUrlsCrawled());
        $this->assertCount(4, $crawler->getUrlsReturned());
    }

    public function test_crawl_with_normalizer()
    {
        $crawler = new Crawler($this->getDummyClient());
        $crawler->addUrlNormalizer(
            new CallbackUrlNormalizer(
                function (Url $url) {
                    if ($url->getPath() === '/page_4.html') {
                        $url = $url->withPath('/page_3.html');
                    }

                    return $url;
                }
            )
        );

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(4, $crawler->getUrlsCrawled());
    }

    public function test_crawler_stop_on_error()
    {
        $client = $this->getClient();

        $i = 0;
        $client->shouldReceive('request')->andReturnUsing(
            function () use (&$i) {
                $i++;
                switch ($i) {
                    case 1:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="/page_2.html">Page 2</a></body></html>';
                        break;
                    case 2:
                        throw new \Exception('foo');
                    case 3:
                        $html = '<html><body><a href="/page_4.html">Page 4</a><a href="mailto:foo@bar.com">Invalid</a></body></html>';
                        break;
                    default:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="http://external/">External</a></body></html>';
                        break;
                }

                return new DomCrawler($html, 'http://my-test');
            }
        );
        $client->shouldReceive('getInternalRequest')->once()->andReturn(null);

        $crawler = new Crawler($client);
        $crawler->setStopOnError(true);

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(1, $crawler->getUrlsCrawled());
    }

    public function test_crawler_exception_on_error()
    {
        $this->setExpectedException(RequestException::class);
        $client = $this->getClient();

        $i = 0;
        $client->shouldReceive('request')->andReturnUsing(
            function () use (&$i) {
                $i++;
                switch ($i) {
                    case 1:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="/page_2.html">Page 2</a></body></html>';
                        break;
                    case 2:
                        throw new \Exception('foo');
                    case 3:
                        $html = '<html><body><a href="/page_4.html">Page 4</a><a href="mailto:foo@bar.com">Invalid</a></body></html>';
                        break;
                    default:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="http://external/">External</a></body></html>';
                        break;
                }

                return new DomCrawler($html, 'http://my-test');
            }
        );

        $crawler = new Crawler($client);
        $crawler->setExceptionOnError(true);

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(1, $crawler->getUrlsCrawled());
    }

    public function test_crawler_does_not_stop_on_error()
    {
        $client = $this->getClient();

        $i = 0;
        $client->shouldReceive('request')->andReturnUsing(
            function () use (&$i) {
                $i++;
                switch ($i) {
                    case 1:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="/page_2.html">Page 2</a></body></html>';
                        break;
                    case 2:
                        throw new \Exception('foo');
                    case 3:
                        $html = '<html><body><a href="/page_4.html">Page 4</a><a href="mailto:foo@bar.com">Invalid</a></body></html>';
                        break;
                    default:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="http://external/">External</a></body></html>';
                        break;
                }

                return new DomCrawler($html, 'http://my-test');
            }
        );
        $client->shouldReceive('getInternalRequest')->andReturn(null);

        $crawler = new Crawler($client);

        foreach ($crawler->crawl('http://my-test') as $page) {
        }

        $this->assertCount(4, $crawler->getUrlsCrawled());
    }

    /**
     * @return m\MockInterface
     */
    protected function getDummyClient()
    {
        $client = $this->getClient();

        $i = 0;
        $client->shouldReceive('request')->andReturnUsing(
            function () use (&$i) {
                $i++;
                switch ($i) {
                    case 1:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="/page_2.html">Page 2</a></body></html>';
                        break;
                    case 2:
                        $html = '<html><body><a href="/page_3.html">Page 3</a><a href="http://external/">External</a></body></html>';
                        break;
                    case 3:
                        $html = '<html><body><a href="/page_4.html">Page 4</a><a href="mailto:foo@bar.com">Invalid</a></body></html>';
                        break;
                    default:
                        $html = '<html><body><a href="/page_1.html">Page 1</a><a href="http://external/">External</a></body></html>';
                        break;
                }

                return new DomCrawler($html, 'http://my-test');
            }
        );
        $client->shouldReceive('getInternalRequest')->andReturn(null);

        return $client;
    }

    public function test_should_crawl_url()
    {
        $reset = get_non_public_method(Crawler::class, 'reset');
        $shouldCrawlUrl = get_non_public_method(Crawler::class, 'shouldCrawlUrl');
        $addToQueue = get_non_public_method(Crawler::class, 'addUrlToQueue');

        $client = new Crawler();

        $reset->invokeArgs($client, [Url::createFromString('http://my-website')]);

        // already in queue as it is the base url
        $this->assertFalse($shouldCrawlUrl->invokeArgs($client, [Url::createFromString('http://my-website')]));

        // new page, should be crawled
        $this->assertTrue($shouldCrawlUrl->invokeArgs($client, [Url::createFromString('http://my-website/foo')]));

        // different host, should not be crawled
        $this->assertFalse($shouldCrawlUrl->invokeArgs($client, [Url::createFromString('http://other-host')]));

        // already rejected, should not be crawled
        $this->assertFalse($shouldCrawlUrl->invokeArgs($client, [Url::createFromString('http://other-host')]));

        $addToQueue->invokeArgs($client, [Url::createFromString('http://my-website/bar')]);
        $this->assertFalse($shouldCrawlUrl->invokeArgs($client, [Url::createFromString('http://my-website/bar')]));
    }

    public function test_update_url()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('getUri')->andReturn('http://redirected-url');

        $domCrawler = new DomCrawler('<html></html>');

        $client = $this->getClient();
        $client->shouldReceive('request')->once()->andReturn($domCrawler);
        $client->shouldReceive('getInternalRequest')->andReturn($request);

        $client = new Crawler($client);
        foreach ($client->crawl('http://original-url') as $page) {
            $this->assertEquals('http://redirected-url', $page->getUrl()->__toString());
        }
    }

    protected function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function getClient()
    {
        $client = m::mock(Client::class);
        $client->shouldReceive('getResponse')->andReturnNull();

        return $client;
    }
}

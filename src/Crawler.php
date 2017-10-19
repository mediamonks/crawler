<?php

namespace MediaMonks\Crawler;

use MediaMonks\Crawler\Client\CrawlerClientInterface;
use MediaMonks\Crawler\Client\GoutteClient;
use MediaMonks\Crawler\Exception\RequestException;
use MediaMonks\Crawler\Exception\UnsupportedUrlException;
use MediaMonks\Crawler\Url\Matcher\UrlMatcherInterface;
use MediaMonks\Crawler\Url\Normalizer\UrlNormalizerInterface;
use MediaMonks\Crawler\Url\UrlCollection;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Crawler implements LoggerAwareInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @var bool
     */
    private $stopOnError = false;

    /**
     * @var bool
     */
    private $exceptionOnError = false;

    /**
     * @var UrlMatcherInterface[]
     */
    private $whitelistUrlMatchers = [];

    /**
     * @var UrlMatcherInterface[]
     */
    private $blacklistUrlMatchers = [];

    /**
     * @var UrlNormalizerInterface[]
     */
    private $urlNormalizers = [];

    /**
     * @var Url
     */
    private $baseUrl;

    /**
     * @var UrlCollection
     */
    private $urlsCrawled;

    /**
     * @var UrlCollection
     */
    private $urlsQueued;

    /**
     * @var UrlCollection
     */
    private $urlsReturned;

    /**
     * @var array
     */
    private $urlsRejected = [];

    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * @param CrawlerClientInterface $client
     */
    public function __construct(CrawlerClientInterface $client = null)
    {
        if (empty($client)) {
            $client = new GoutteClient();
        }

        $this->setClient($client);

        $this->urlsCrawled = new UrlCollection();
        $this->urlsQueued = new UrlCollection();
        $this->urlsReturned = new UrlCollection();

        return $this;
    }

    /**
     * @param CrawlerClientInterface $client
     */
    public function setClient(CrawlerClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getStopOnError()
    {
        return $this->stopOnError;
    }

    /**
     * @param boolean $stopOnError
     * @return $this
     */
    public function setStopOnError($stopOnError)
    {
        $this->stopOnError = $stopOnError;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getExceptionOnError()
    {
        return $this->exceptionOnError;
    }

    /**
     * @param boolean $exceptionOnError
     * @return $this
     */
    public function setExceptionOnError($exceptionOnError)
    {
        $this->exceptionOnError = $exceptionOnError;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrlsCrawled()
    {
        return $this->urlsCrawled->toArray();
    }

    /**
     * @return array
     */
    public function getUrlsQueued()
    {
        return $this->urlsQueued->toArray();
    }

    /**
     * @return array
     */
    public function getUrlsReturned()
    {
        return $this->urlsReturned->toArray();
    }

    /**
     * @return array
     */
    public function getUrlsRejected()
    {
        return $this->urlsRejected;
    }

    /**
     * @param $urlMatchers
     * @return $this
     */
    public function setWhitelistUrlMatchers(array $urlMatchers)
    {
        $this->clearWhitelistUrlMatchers();
        foreach ($urlMatchers as $matcher) {
            $this->addWhitelistUrlMatcher($matcher);
        }

        return $this;
    }

    /**
     * @return Url\Matcher\UrlMatcherInterface[]
     */
    public function getWhitelistUrlMatchers()
    {
        return $this->whitelistUrlMatchers;
    }

    /**
     * @param UrlMatcherInterface $urlMatcher
     * @return $this
     */
    public function addWhitelistUrlMatcher(UrlMatcherInterface $urlMatcher)
    {
        $this->whitelistUrlMatchers[] = $urlMatcher;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearWhitelistUrlMatchers()
    {
        $this->whitelistUrlMatchers = [];

        return $this;
    }

    /**
     * @param array $urlMatchers
     * @return $this
     */
    public function setBlacklistUrlMatchers(array $urlMatchers)
    {
        $this->clearBlacklistUrlMatchers();
        foreach ($urlMatchers as $matcher) {
            $this->addBlacklistUrlMatcher($matcher);
        }

        return $this;
    }

    /**
     * @return UrlMatcherInterface[]
     */
    public function getBlacklistUrlMatchers()
    {
        return $this->blacklistUrlMatchers;
    }

    /**
     * @param UrlMatcherInterface $urlMatcher
     * @return $this
     */
    public function addBlacklistUrlMatcher(UrlMatcherInterface $urlMatcher)
    {
        $this->blacklistUrlMatchers[] = $urlMatcher;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearBlacklistUrlMatchers()
    {
        $this->blacklistUrlMatchers = [];

        return $this;
    }

    /**
     * @param array $normalizers
     * @return $this
     */
    public function setUrlNormalizers(array $normalizers)
    {
        $this->clearUrlNormalizers();

        foreach ($normalizers as $normalizer) {
            $this->addUrlNormalizer($normalizer);
        }

        return $this;
    }

    /**
     * @return UrlNormalizerInterface[]
     */
    public function getUrlNormalizers()
    {
        return $this->urlNormalizers;
    }

    /**
     * @param UrlNormalizerInterface $normalizer
     * @return $this
     */
    public function addUrlNormalizer(UrlNormalizerInterface $normalizer)
    {
        $this->urlNormalizers[] = $normalizer;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearUrlNormalizers()
    {
        $this->urlNormalizers = [];

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (is_null($this->logger)) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param $url
     * @return Url
     * @throws \Exception
     */
    protected function createHttpUrlString($url)
    {
        try {
            return $this->normalizeUrl(Url::createFromString($url));
        }
        catch (\Exception $e) {
            $this->getLogger()->warning(
                sprintf('Url %s could not be converted to an object: %s', $url, $e->getMessage())
            );

            throw new UnsupportedUrlException($url);
        }
    }

    /**
     * @param Url $url
     */
    protected function reset(Url $url)
    {
        $this->baseUrl = $url;

        $this->urlsCrawled->reset();
        $this->urlsQueued->reset();
        $this->urlsReturned->reset();
        $this->urlsRejected = [];

        $this->urlsQueued->push($url);
    }

    /**
     * @param string $url
     * @return \Generator|Page[]
     * @throws RequestException
     */
    public function crawl($url)
    {
        $this->reset($this->createHttpUrlString($url));

        while ($url = $this->urlsQueued->pop()) {

            try {
                $crawler = $this->requestPage($url);
                $url = $this->updateResolvedUrl($url);
            } catch (\Exception $e) {
                $this->getLogger()->error(sprintf('Error requesting page %s: %s', $url, $e->getMessage()));

                if ($this->getStopOnError()) {
                    return;
                }
                if ($this->getExceptionOnError()) {
                    throw new RequestException($e->getMessage(), $e->getCode(), $e);
                }

                continue;
            }

            $this->urlsCrawled->push($url);
            $this->updateQueue($crawler);

            if ($this->shouldReturnUrl($url)) {
                $this->getLogger()->debug(sprintf('Return url "%s"', $url));
                $this->urlsReturned->push($url);

                yield new Page($url, $crawler, $this->client->getResponse());
            }

            if ($this->isLimitReached()) {
                $this->getLogger()->info(sprintf('Crawl limit of %d was reach', $this->limit));

                return;
            }
        }
    }

    /**
     * @param Url $url
     * @return Url
     */
    protected function updateResolvedUrl(Url $url)
    {
        $request = $this->client->getRequest();
        if (!empty($request)) {
            $url = $this->createHttpUrlString($request->getUri());
        }

        return $url;
    }

    /**
     * @param DomCrawler $crawler
     */
    protected function updateQueue(DomCrawler $crawler)
    {
        foreach ($this->extractUrlsFromCrawler($crawler) as $url) {
            $this->getLogger()->debug(sprintf('Found url %s in page', $url));
            try {
                $url = $this->createHttpUrlString($url);

                if ($this->shouldCrawlUrl($url)) {
                    $this->urlsQueued->push($url);
                }
            } catch (\Exception $e) {
                $this->addRejectedUrl($url);
            }
        }
    }

    /**
     * @param Url $url
     * @return Url
     */
    protected function normalizeUrl(Url $url)
    {
        foreach ($this->urlNormalizers as $normalizer) {
            $url = $normalizer->normalize($url);
        }

        return $url;
    }

    /**
     * @param Url $url
     * @return bool
     */
    protected function shouldReturnUrl(Url $url)
    {
        if (!empty($this->whitelistUrlMatchers)) {
            if (!$this->isUrlWhitelisted($url)) {
                $this->getLogger()->info(sprintf('Skipping "%s" because it is not whitelisted', $url));

                return false;
            }
        }

        if ($this->isUrlBlacklisted($url)) {
            $this->getLogger()->info(sprintf('Skipping "%s" because it is blacklisted', $url));

            return false;
        }

        return true;
    }

    /**
     * @param Url $url
     * @return bool
     */
    protected function isUrlWhitelisted(Url $url)
    {
        foreach ($this->whitelistUrlMatchers as $matcher) {
            if ($matcher->matches($url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Url $url
     * @return bool
     */
    protected function isUrlBlacklisted(Url $url)
    {
        foreach ($this->blacklistUrlMatchers as $matcher) {
            if ($matcher->matches($url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Url $url
     * @return bool
     */
    protected function shouldCrawlUrl(Url $url)
    {
        if ($this->urlsCrawled->contains($url) || $this->urlsQueued->contains($url)) {
            return false;
        }

        if (!$this->isUrlPartOfBaseUrl($url)) {
            $this->addRejectedUrl($url);

            return false;
        }

        return true;
    }

    /**
     * @param $url
     */
    protected function addRejectedUrl($url)
    {
        if ($url instanceof Url) {
            $url = $url->__toString();
        }
        if (!is_string($url)) {
            throw new \InvalidArgumentException('Url should be a string or an instance of '.Url::class);
        }

        $this->urlsRejected[$url] = $url;
    }

    /**
     * @param Url $url
     * @return bool
     */
    protected function isUrlPartOfBaseUrl(Url $url)
    {
        $baseUrlString = (string)$this->baseUrl;
        $this->getLogger()->debug($baseUrlString.' - '.$url);
        if (strpos((string)$url, $baseUrlString) === false) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isLimitReached()
    {
        return (!empty($this->limit) && count($this->urlsReturned) === $this->limit);
    }

    /**
     * @param DomCrawler $crawler
     * @return array
     */
    protected function extractUrlsFromCrawler(DomCrawler $crawler)
    {
        return $crawler->filter('a')->each(
            function (DomCrawler $node) {
                return $node->link()->getUri();
            }
        );
    }

    /**
     * @param Url $url
     * @return DomCrawler
     */
    protected function requestPage(Url $url)
    {
        $this->getLogger()->info(sprintf('Crawling page %s', $url));
        $crawler = $this->client->request('GET', (string)$url);
        $this->getLogger()->info(sprintf('Crawled page %s', $url));

        return $crawler;
    }
}

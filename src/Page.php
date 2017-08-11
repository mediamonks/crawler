<?php

namespace MediaMonks\Crawler;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Page
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var DomCrawler
     */
    private $crawler;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param Url $url
     * @param DomCrawler $crawler
     * @param $response
     */
    public function __construct(Url $url, DomCrawler $crawler, Response $response = null)
    {
        $this->url = $url;
        $this->crawler = $crawler;
        $this->response = $response;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return DomCrawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}

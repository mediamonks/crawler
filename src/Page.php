<?php

namespace MediaMonks\Crawler;

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
     * @param Url $url
     * @param DomCrawler $crawler
     */
    public function __construct(Url $url, DomCrawler $crawler)
    {
        $this->url = $url;
        $this->crawler = $crawler;
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
}

<?php

namespace MediaMonks\Crawler\Client;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;

interface CrawlerClientInterface
{
    /**
     * @return Request|null A Request instance
     */
    public function getRequest();

    /**
     * @return Response|null A response instance
     */
    public function getResponse();

    /**
     * @param string $method        The request method
     * @param string $uri           The URI to fetch
     * @param array  $parameters    The Request parameters
     * @param array  $files         The files
     * @param array  $server        The server parameters (HTTP headers are referenced with a HTTP_ prefix as PHP does)
     * @param string $content       The raw body data
     * @param bool   $changeHistory Whether to update the history or not (only used internally for back(), forward(), and reload())
     *
     * @return Crawler
     */
    public function request(
        $method,
        $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        $content = null,
        $changeHistory = true
    );
}

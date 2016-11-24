<?php

namespace MediaMonks\Crawler\Client;

use Goutte\Client as BaseClient;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;

class PrerenderClient extends BaseClient
{
    /**
     * @var string
     */
    protected $prerenderUrl;

    /**
     * @param string $prenderUrl The url of the prerender server
     * @param array $server The server parameters (equivalent of $_SERVER)
     * @param History $history A History instance to store the browser history
     * @param CookieJar $cookieJar A CookieJar instance to store the cookies
     */
    public function __construct($prenderUrl, array $server = [], History $history = null, CookieJar $cookieJar = null)
    {
        $this->prerenderUrl = $prenderUrl;
        parent::__construct($server, $history, $cookieJar);
    }

    /**
     * @inheritdoc
     */
    public function request(
        $method,
        $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        $content = null,
        $changeHistory = true
    ) {
        $uri = $this->prerenderUrl.$uri;

        return parent::request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
    }
}

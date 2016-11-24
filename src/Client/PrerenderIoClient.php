<?php

namespace MediaMonks\Crawler\Client;

use Goutte\Client as BaseClient;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;

class PrerenderIoClient extends BaseClient
{
    const URL = 'http://service.prerender.io/';

    const HEADER_TOKEN = 'HTTP_X-Prerender-Token';
    const HEADER_USER_AGENT = 'HTTP_USER_AGENT';

    const USER_AGENT = 'MediaMonks Crawler';

    /**
     * @var string
     */
    protected $token;

    /**
     * @param string $token Token from the prerender.io service
     * @param array $server The server parameters (equivalent of $_SERVER)
     * @param History $history A History instance to store the browser history
     * @param CookieJar $cookieJar A CookieJar instance to store the cookies
     */
    public function __construct($token, array $server = [], History $history = null, CookieJar $cookieJar = null)
    {
        $this->token = $token;
        $server[self::HEADER_TOKEN] = $token;
        $server[self::HEADER_USER_AGENT] = self::USER_AGENT;
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
        $uri = self::URL.$uri;

        return parent::request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
    }
}

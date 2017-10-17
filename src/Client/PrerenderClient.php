<?php

namespace MediaMonks\Crawler\Client;

use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\Request;

class PrerenderClient extends GoutteClient
{
    /**
     * @var string
     */
    protected $prerenderUrl;

    /**
     * @param string $prerenderUrl The url of the prerender server
     * @param array $server The server parameters (equivalent of $_SERVER)
     * @param History $history A History instance to store the browser history
     * @param CookieJar $cookieJar A CookieJar instance to store the cookies
     */
    public function __construct($prerenderUrl, array $server = [], History $history = null, CookieJar $cookieJar = null)
    {
        $this->prerenderUrl = $prerenderUrl;

        parent::__construct($server, $history, $cookieJar);
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function getAbsoluteUri($uri)
    {
        return $this->prerenderUrl.parent::getAbsoluteUri($uri);
    }

    /**
     * @inheritdoc
     */
    public function getRequest()
    {
        $request = parent::getRequest();
        if (!empty($request)) {
            return new Request($this->correctUrl($request->getUri()),
                $request->getMethod(), $request->getParameters(),
                $request->getFiles(), $request->getCookies(), $request->getServer(),
                $request->getContent());
        }
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function correctUrl($url)
    {
        return str_replace($this->prerenderUrl, '', $url);
    }
}

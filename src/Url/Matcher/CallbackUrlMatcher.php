<?php

namespace MediaMonks\Crawler\Url\Matcher;

use League\Uri\Schemes\Http as Url;

class CallbackUrlMatcher implements UrlMatcherInterface
{
    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @param \Closure $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Url $url
     * @return bool
     */
    public function matches(Url $url)
    {
        return call_user_func($this->callback, $url);
    }
}

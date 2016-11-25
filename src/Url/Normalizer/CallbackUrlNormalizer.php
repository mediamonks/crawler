<?php

namespace MediaMonks\Crawler\Url\Normalizer;

use MediaMonks\Crawler\Url;

class CallbackUrlNormalizer implements UrlNormalizerInterface
{
    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @param \Closure $callback
     */
    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Url $url
     * @return Url
     */
    public function normalize(Url $url)
    {
        return call_user_func($this->callback, $url);
    }
}

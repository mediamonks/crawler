<?php

namespace MediaMonks\Crawler\Url\Matcher;

use League\Uri\Schemes\Http as Url;

interface UrlMatcherInterface
{
    public function matches(Url $url);
}
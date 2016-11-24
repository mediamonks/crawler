<?php

namespace MediaMonks\Crawler\Url\Normalizer;

use MediaMonks\Crawler\Url;

interface UrlNormalizerInterface
{
    public function normalize(Url $url);
}

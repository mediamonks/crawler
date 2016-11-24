<?php

namespace MediaMonks\Crawler\Url\Normalizer;

use MediaMonks\Crawler\Url;

class RemoveQueryParameterUrlNormalizer implements UrlNormalizerInterface
{
    /**
     * @var array
     */
    private $keys;

    /**
     * @param array $keys
     */
    public function __construct($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $this->keys = $keys;
    }

    /**
     * @param Url $url
     * @return Url
     */
    public function normalize(Url $url)
    {
        $query = $url->query;
        $query = $query->without($this->keys);

        return $url->withQuery((string)$query);
    }
}

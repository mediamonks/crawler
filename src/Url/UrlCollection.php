<?php

namespace MediaMonks\Crawler\Url;

use MediaMonks\Crawler\Exception\EmptyCollectionException;
use MediaMonks\Crawler\Url;

class UrlCollection implements \Countable
{

    /**
     * @var Url[]
     */
    private $urls = [];

    /**
     * @param Url $url
     */
    public function push(Url $url)
    {
        if (!$this->contains($url)) {
            $this->urls[$url->__toString()] = $url;
        }
    }

    /**
     * @return Url|false
     * @throws EmptyCollectionException
     */
    public function pop()
    {
        $url = array_shift($this->urls);
        if (empty($url)) {
            return false;
        }

        return $url;
    }

    /**
     * @param Url $url
     *
     * @return bool
     */
    public function contains(Url $url)
    {
        return isset($this->urls[$url->__toString()]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->urls);
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->urls = [];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $stringified = [];
        foreach ($this->urls as $url) {
            $stringified[] = $url->__toString();
        }

        return $stringified;
    }
}

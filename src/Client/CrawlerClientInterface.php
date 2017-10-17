<?php

namespace MediaMonks\Crawler\Client;

interface CrawlerClientInterface
{
    public function getRequest();

    public function getResponse();

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

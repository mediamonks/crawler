<?php

namespace tests\MediaMonks\Crawler\Url\Matcher;

use MediaMonks\Crawler\Url;
use MediaMonks\Crawler\Url\Matcher\PathRegexUrlMatcher;
use Mockery as m;

class PathRegexUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test_matches_path_regex()
    {
        $callbackUrlMatcher = new PathRegexUrlMatcher('~^/foo~');

        $this->assertTrue($callbackUrlMatcher->matches(Url::createFromString('http://my-project/foo')));
        $this->assertTrue($callbackUrlMatcher->matches(Url::createFromString('http://my-project/foo/bat')));
        $this->assertFalse($callbackUrlMatcher->matches(Url::createFromString('http://my-project/bar')));
        $this->assertFalse($callbackUrlMatcher->matches(Url::createFromString('http://my-project/bar/foo')));
    }
}
<?php

namespace tests\MediaMonks\Crawler\Url\Matcher;

use MediaMonks\Crawler\Url;
use MediaMonks\Crawler\Url\Matcher\CallbackUrlMatcher;
use Mockery as m;

class CallbackUrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test_callback_is_called()
    {
        $callback = function(Url $url) {
            $url->__toString();
        };

        $url = m::mock(Url::class);
        $url->shouldReceive('__toString')->once();

        $callbackUrlMatcher = new CallbackUrlMatcher($callback);
        $callbackUrlMatcher->matches($url);
    }

    protected function tearDown()
    {
        parent::tearDown();

        m::close();
    }
}
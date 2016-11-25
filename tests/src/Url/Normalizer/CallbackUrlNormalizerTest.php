<?php

namespace tests\MediaMonks\Crawler\Url\Normalizer;

use MediaMonks\Crawler\Url;
use MediaMonks\Crawler\Url\Normalizer\CallbackUrlNormalizer;
use Mockery as m;

class CallbackUrlNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function test_callback_is_called()
    {
        $callback = function(Url $url) {
            $url->__toString();
        };

        $url = m::mock(Url::class);
        $url->shouldReceive('__toString')->once();

        $callbackUrlMatcher = new CallbackUrlNormalizer($callback);
        $callbackUrlMatcher->normalize($url);
    }

    protected function tearDown()
    {
        parent::tearDown();

        m::close();
    }
}
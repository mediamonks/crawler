<?php

namespace tests\MediaMonks\Crawler\Url\Normalizer;

use MediaMonks\Crawler\Url;
use MediaMonks\Crawler\Url\Normalizer\RemoveQueryParameterUrlNormalizer;
use Mockery as m;

class RemoveQueryParameterUrlNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function test_query_parameter_is_removed()
    {
        $this->applyNormalization('http://my-project/?foo=bar', 'foo', 'http://my-project/');
    }

    public function test_query_parameters_are_removed()
    {
        $this->applyNormalization('http://my-project/?foo=bar&bar=baz', ['foo', 'bar'], 'http://my-project/');
    }

    public function test_query_parameters_are_not_removed()
    {
        $this->applyNormalization('http://my-project/?foo=bar&bar=baz', ['foo2'], 'http://my-project/?foo=bar&bar=baz');
    }

    /**
     * @param $urlInput
     * @param array $removeKeys
     * @param $urlExpectedOutput
     */
    protected function applyNormalization($urlInput, $removeKeys, $urlExpectedOutput)
    {
        $url = Url::createFromString($urlInput);
        $callbackUrlMatcher = new RemoveQueryParameterUrlNormalizer($removeKeys);
        $url = $callbackUrlMatcher->normalize($url);

        $this->assertEquals($urlExpectedOutput, $url->__toString());
    }
}
<?php

use Composer\Autoload\ClassLoader;

/** @var $loader ClassLoader */
$loader = require __DIR__ . '/../vendor/autoload.php';

/**
 * @param $className
 * @param $methodName
 * @return \ReflectionMethod
 */
function get_non_public_method($className, $methodName)
{
    $class = new \ReflectionClass($className);
    $method = $class->getMethod($methodName);
    $method->setAccessible(true);

    return $method;
}

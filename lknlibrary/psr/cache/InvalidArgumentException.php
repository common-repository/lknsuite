<?php

namespace Psr\Cache;

defined('_LKNSUITE_PLUGIN') or die('Restricted access');


/**
 * Exception interface for invalid cache arguments.
 *
 * Any time an invalid argument is passed into a method it must throw an
 * exception class which implements Psr\Cache\InvalidArgumentException.
 */
interface InvalidArgumentException extends CacheException
{
}

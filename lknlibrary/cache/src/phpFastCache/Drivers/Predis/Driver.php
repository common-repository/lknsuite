<?php
/**
 *
 * This file is part of phpFastCache.
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the docs/CREDITS.txt file.
 *
 * @author Khoa Bui (khoaofgod)  <khoaofgod@gmail.com> http://www.phpfastcache.com
 * @author Georges.L (Geolim4)  <contact@geolim4.com>
 *
 */

namespace phpFastCache\Drivers\Predis;

defined('_LKNSUITE_PLUGIN') or die('Restricted access');


use phpFastCache\Core\Pool\DriverBaseTrait;
use phpFastCache\Core\Pool\ExtendedCacheItemPoolInterface;
use phpFastCache\Entities\DriverStatistic;
use phpFastCache\Exceptions\phpFastCacheDriverCheckException;
use phpFastCache\Exceptions\phpFastCacheDriverException;
use phpFastCache\Exceptions\phpFastCacheInvalidArgumentException;
use Predis\Client as PredisClient;
use Psr\Cache\CacheItemInterface;

/**
 * Class Driver
 * @package phpFastCache\Drivers
 * @property PredisClient $instance Instance of driver service
 */
class Driver implements ExtendedCacheItemPoolInterface
{
    use DriverBaseTrait;

    /**
     * Driver constructor.
     * @param array $config
     * @throws phpFastCacheDriverException
     */
    public function __construct(array $config = [])
    {
        $this->setup($config);

        if (!$this->driverCheck()) {
            throw new phpFastCacheDriverCheckException(sprintf(self::DRIVER_CHECK_FAILURE, $this->getDriverName()));
        } else {
            $this->driverConnect();
        }
    }

    /**
     * @return bool
     */
    public function driverCheck()
    {
        if (extension_loaded('Redis')) {
            trigger_error('The native Redis extension is installed, you should use Redis instead of Predis to increase performances', E_USER_NOTICE);
        }

        return class_exists('Predis\Client');
    }

    /**
     * @param \Psr\Cache\CacheItemInterface $item
     * @return mixed
     * @throws phpFastCacheInvalidArgumentException
     */
    protected function driverWrite(CacheItemInterface $item)
    {
        /**
         * Check for Cross-Driver type confusion
         */
        if ($item instanceof Item) {
            $ttl = $item->getExpirationDate()->getTimestamp() - time();

            return $this->instance->setex($item->getKey(), $ttl, $this->encode($this->driverPreWrap($item)));
        } else {
            throw new phpFastCacheInvalidArgumentException('Cross-Driver type confusion detected');
        }
    }

    /**
     * @param \Psr\Cache\CacheItemInterface $item
     * @return mixed
     */
    protected function driverRead(CacheItemInterface $item)
    {
        $val = $this->instance->get($item->getKey());
        if ($val == false) {
            return null;
        } else {
            return $this->decode($val);
        }
    }

    /**
     * @param \Psr\Cache\CacheItemInterface $item
     * @return bool
     * @throws phpFastCacheInvalidArgumentException
     */
    protected function driverDelete(CacheItemInterface $item)
    {
        /**
         * Check for Cross-Driver type confusion
         */
        if ($item instanceof Item) {
            return $this->instance->del($item->getKey());
        } else {
            throw new phpFastCacheInvalidArgumentException('Cross-Driver type confusion detected');
        }
    }

    /**
     * @return bool
     */
    protected function driverClear()
    {
        return $this->instance->flushdb();
    }

    /**
     * @return bool
     */
    protected function driverConnect()
    {
        $config = isset($this->config[ 'predis' ]) ? $this->config[ 'predis' ] : [];

        $this->instance = new PredisClient(array_merge([
          'host' => '127.0.0.1',
          'port' => 6379,
          'password' => null,
          'database' => null,
        ], $config));

        return true;
    }

    /********************
     *
     * PSR-6 Extended Methods
     *
     *******************/


    /**
     * @return string
     */
    public function getHelp()
    {
        return <<<HELP
<p>
To install the Predis library via Composer:
<code>composer require "predis/predis" "~1.1.0"</code>
</p>
HELP;
    }

    /**
     * @return DriverStatistic
     */
    public function getStats()
    {
        $info = $this->instance->info();
        $size = (isset($info['Memory']['used_memory']) ? $info['Memory']['used_memory'] : 0);
        $version = (isset($info['Server']['redis_version']) ? $info['Server']['redis_version'] : 0);
        $date = (isset($info['Server'][ 'uptime_in_seconds' ]) ? (new \DateTime())->setTimestamp(time() - $info['Server'][ 'uptime_in_seconds' ]) : 'unknown date');

        return (new DriverStatistic())
          ->setData(implode(', ', array_keys($this->itemInstances)))
          ->setRawData($info)
          ->setSize($size)
          ->setInfo(sprintf("The Redis daemon v%s is up since %s.\n For more information see RawData. \n Driver size includes the memory allocation size.", $version, $date->format(DATE_RFC2822)));
    }
}
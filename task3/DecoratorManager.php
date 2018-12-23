<?php

namespace src\Decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager
{
    public $cache;
    public $logger;
    private $dataProvider;

    /**
     * @param DataProvider $dataProvider
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(DataProvider $dataProvider, CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    public function getResponse(array $input)
    {
        try {
            $cacheKey = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->dataProvider->get($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );

            return $result;
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }

        return [];
    }

    /**
     * @param array $input
     *
     * @return string
     */
    public function getCacheKey(array $input)
    {
        ksort($input);
        return json_encode($input);
    }
}

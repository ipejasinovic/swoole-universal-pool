<?php

declare(strict_types=1);

namespace Swoole;

use Swoole\ConnectionPool;
use Swoole\UniversalConfig;

class UniversalPool {

    protected $size = 64;
    protected $pool = false;

    public function __construct(UniversalConfig $config, int $size = self::DEFAULT_SIZE) {
        $this->pool = new ConnectionPool(function () use($config) {
            $driver = $config->getDriver();
            if (!in_array($driver, $config->getAvailableDrivers())) {
                return false;
            }
            switch ($driver) {
                case 'mysql': return $this->getMySQLConnection($config);
                case 'pgsql': return $this->getPostgresConnection($config);
                case 'redis': return $this->getRedisConnection($config);
            }
        }, $size);
    }

    public function close() {
        if (!$this->pool) {
            return false;
        }
        return $this->pool->close();
    }

    public function fill() {
        if (!$this->pool) {
            return false;
        }
        return $this->pool->fill();
    }

    public function get() {
        if (!$this->pool) {
            return false;
        }
        return $this->pool->get();
    }

    public function put($connection) {
        if (!$this->pool) {
            return false;
        }
        return $this->pool->put($connection);
    }

    private function getMySQLConnection(&$config) {
        $conn = new \Swoole\MySQLConnection();
        $conn->connect($config);
        return $conn;
    }

    private function getPostgresConnection(&$config) {
        $conn = new \Swoole\PostgresConnection();
        $conn->connect($config);
        return $conn;
    }

    private function getRedisConnection(&$config) {
        $redis = new \Swoole\Coroutine\Redis();
        $arguments = [$config->getHost(), $config->getPort()];
        if ($config->getTimeout() !== 0.0) {
            $arguments[] = $config->getTimeout();
        }
        if ($config->getRetryInterval() !== 0) {
            $arguments[] = null;
            $arguments[] = $config->getRetryInterval();
        }
        if ($config->getReadTimeout() !== 0.0) {
            $arguments[] = $config->getReadTimeout();
        }
        $redis->connect(...$arguments);
        if ($config->getAuth()) {
            $redis->auth($config->getAuth());
        }
        if ($config->getDbIndex() !== 0) {
            $redis->select($config->getDbIndex());
        }
        return $redis;
    }

}

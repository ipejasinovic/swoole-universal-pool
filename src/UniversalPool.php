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
            if ($config->getDriver() === 'mysql') {
                $conn = new \Swoole\MySQLConnection();
            } else if ($config->getDriver() === 'pgsql') {
                $conn = new \Swoole\PostgresConnection();
            } else if ($config->getDriver() === 'redis') {
                $redis = new \Redis();
                $arguments = [
                    $config->getHost(),
                    $config->getPort(),
                ];
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
            } else {
                return false;
            }
            $conn->connect($config);
            return $conn;
        }, $size);
    }

    public function get() {
        if(!$this->pool) {
            return false;
        }
        return $this->pool->get();
    }

    public function put($connection) {
        if(!$this->pool) {
            return false;
        }
        return $this->pool->put($connection);
    }

    public function close() {
        if(!$this->pool) {
            return false;
        }
        return $this->pool->close();
    }

    public function fill() {
        if(!$this->pool) {
            return false;
        }
        return $this->pool->close();
    }

}

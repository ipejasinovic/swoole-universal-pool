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

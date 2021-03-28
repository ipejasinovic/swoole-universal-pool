<?php

declare(strict_types=1);

namespace Swoole;

use Swoole\ConnectionPool;
use Swoole\Database\PDOPool;
use Swoole\Database\PDOConfig;

class UniversalPool {

    protected $size = 64;
    protected $pool;

    public function __construct(PDOConfig $config, int $size = self::DEFAULT_SIZE) {
        if ($config->getDriver() === 'mysql') {
            $this->pool = new PDOPool($config, $size);
        } else if ($config->getDriver() === 'pgsql') {
            $this->pool = new ConnectionPool(function () use($config) {
                $conn = new \Swoole\Coroutine\PostgreSQL();
                $conn->connect("host={$config->getHost()} port={$config->getPort()} dbname={$config->getDbname()} user={$config->getUsername()} password={$config->getPassword()}");
                return $conn;
            }, $size);
        }
    }

    public function get() {
        return $this->pool->get();
    }

    public function put($connection) {
        return $this->pool->put($connection);
    }

    public function close() {
        return $this->pool->close();
    }

    public function fill() {
        return $this->pool->close();
    }

}

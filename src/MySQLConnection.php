<?php

namespace Swoole;

use Swoole\Coroutine\MySQL;
use Swoole\UniversalConfig;

class MySQLConnection implements ConnectionInterface {

    private $config;
    private $conn;
    private $resource;
    public $connected;
    public $error;
    public $errno;
    public $connect_error;
    public $connect_errno;
    public $affected_rows;

    public function __construct($config = null) {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new UniversalConfig();
        }
        $this->conn = new MySQL();
    }

    public function connect($config = null) {
        if ($config) {
            $this->config = $config;
        }
        $this->connected = $this->conn->connect([
            'host' => $this->config->getHost(),
            'user' => $this->config->getUsername(),
            'password' => $this->config->getPassword(),
            'database' => $this->config->getDbname(),
            'port' => $this->config->getPort() . '',
            'charset' => $this->config->getCharset(),
            'timeout' => -1,
            'strict_type' => false,
            'fetch_mode' => true
        ]);
        $this->connect_error = $this->conn->error;
        return $this->connected;
    }

    public function begin() {
        if(!$this->conn->connected) {
            $this->connect($this->config);
        }
        $this->resource = $this->conn->begin();
        if(!$this->resource) {
            $this->connect($this->config);
            $this->resource = $this->conn->begin();
        }
        $this->error = $this->conn->error;
        $this->affected_rows = 0;
        if(!$this->resource) {
            return false;
        }
        return $this;
    }

    public function commit() {
        if(!$this->conn->connected) {
            $this->connect($this->config);
        }
        $this->resource = $this->conn->commit();
        if(!$this->resource) {
            $this->connect($this->config);
            $this->resource = $this->conn->commit();
        }
        $this->error = $this->conn->error;
        $this->affected_rows = 0;
        if(!$this->resource) {
            return false;
        }
        return $this;
    }

    public function query($sql) {
        if(!$this->conn->connected) {
            $this->connect($this->config);
        }
        $this->resource = $this->conn->query($sql);
        if(!$this->resource) {
            $this->connect($this->config);
            $this->resource = $this->conn->query($sql);
        }
        $this->error = $this->conn->error;
        $this->affected_rows = $this->conn->affected_rows;
        if(!$this->resource) {
            return false;
        }
        return $this;
    }

    public function fetch($mode = null) {
        if(!$this->resource) {
            return false;
        }
        return $this->conn->fetch();
    }

    public function fetchAll($mode = null) {
        if(!$this->resource) {
            return false;
        }
        return $this->conn->fetchAll();
    }

}

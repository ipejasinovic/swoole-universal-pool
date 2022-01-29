<?php

namespace Swoole;

use Swoole\Coroutine\PostgreSQL;
use Swoole\UniversalConfig;

class PostgresConnection implements ConnectionInterface {

    private $config;
    private $conn;
    private $resource;
    public $connected;
    public $error;
    public $errno;
    public $connect_error;
    public $connect_errno;
    public $affected_rows = 0;
    public $insert_id = 0;

    public function __construct($config = null) {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = (new UniversalConfig)
                ->withDriver('pgsql')
                ->withHost('127.0.0.1')
                ->withPort(5432)
                ->withDbName('admin')
                ->withUsername('admin')
                ->withPassword('admin');
        }
        $this->conn = new PostgreSQL();
    }

    public function connect($config = null) {
        if ($config) {
            $this->config = $config;
        }
        $this->connected = $this->conn->connect("host={$this->config->getHost()} port={$this->config->getPort()} dbname={$this->config->getDbname()} user={$this->config->getUsername()} password={$this->config->getPassword()}");
        $this->connect_error = $this->conn->error;
        return $this->connected;
    }

    public function begin() {
        $this->resource = $this->conn->query('BEGIN');
        $this->error = $this->conn->error;
        $this->affected_rows = 0;
        $this->insert_id = 0;
        if (!$this->resource) {
            return false;
        }
        return $this;
    }

    public function commit() {
        $this->resource = $this->conn->query('COMMIT');
        $this->error = $this->conn->error;
        $this->affected_rows = 0;
        $this->insert_id = 0;
        if (!$this->resource) {
            return false;
        }
        return $this;
    }

    public function query($sql) {
        $this->resource = $this->conn->query($sql);
        $this->error = $this->conn->error;
        $this->affected_rows = 0;
        if (!$this->resource) {
            return false;
        }
        $returning = strpos(strtolower($sql), "returning ");
        if ($returning !== false) {
            $id = "";
            for ($i = $returning + 10; $i < strlen($sql); $i++) {
                if ($sql[$i] !== ' ' && $sql[$i] !== ";" && $sql[$i] !== ")") {
                    $id .= $sql[$i];
                } else {
                    break;
                }
            }
            if (strlen($id) > 0) {
                $row = $this->conn->fetchRow($this->resource);
                if (isset($row[$id])) {
                    $this->insert_id = $row[$id];
                }
            }
        } else {
            $this->insert_id = 0;
        }
        $this->affected_rows = $this->conn->affectedRows($this->resource);
        return $this;
    }

    public function fetch($mode = null) {
        if (!$this->resource) {
            return false;
        }
        return $this->conn->fetchRow($this->resource);
    }

    public function fetchAll($mode = null) {
        if (!$this->resource) {
            return false;
        }
        return $this->conn->fetchAll($this->resource);
    }

    public function lastInsertId() {
        return $this->insert_id;
    }

    public function rowCount() {
        return $this->affected_rows;
    }

}

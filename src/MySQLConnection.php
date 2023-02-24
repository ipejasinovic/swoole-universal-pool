<?php

namespace Swoole;

use PDO;
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
    public $affected_rows = 0;
    public $insert_id = 0;

    public function __construct($config = null) {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new UniversalConfig();
        }
    }

    public function connect($config = null) {
        if ($config) {
            $this->config = $config;
        }
        try {
            $this->conn = new PDO(
                    "{$this->config->getDriver()}:" .
                    (
                    $this->config->hasUnixSocket() ?
                    "unix_socket={$this->config->getUnixSocket()};" :
                    "host={$this->config->getHost()};" . "port={$this->config->getPort()};"
                    ) .
                    "dbname={$this->config->getDbname()};" .
                    "charset={$this->config->getCharset()}",
                    $this->config->getUsername(),
                    $this->config->getPassword(),
                    $this->config->getOptions()
            );
            $this->connected = true;
        } catch (PDOException $ex) {
            $this->connect_error = $this->conn->connect_error;
            $this->connected = false;
        }
        return $this->connected;
    }

    public function begin() {
        if (!$this->connected) {
            $this->connect($this->config);
        }
        $this->affected_rows = 0;
        $this->insert_id = 0;
        try {
            $this->conn->beginTransaction();
        } catch (PDOException $ex) {
            $this->error = $ex->getMessage();
            $this->errno = $ex->getCode();
            return false;
        }
        $this->error = '';
        $this->errno = null;
        return $this;
    }

    public function commit() {
        if (!$this->connected) {
            $this->connect($this->config);
        }
        $this->affected_rows = 0;
        $this->insert_id = 0;
        try {
            $this->conn->commit();
        } catch (PDOException $ex) {
            $this->error = $ex->getMessage();
            $this->errno = $ex->getCode();
            return false;
        }
        $this->error = '';
        $this->errno = null;
        return $this;
    }

    public function query($sql) {
        if (!$this->connected) {
            $this->connect($this->config);
        }
        try {
            $this->resource = $this->conn->query($sql);
        } catch (PDOException $ex) {
            $this->error = $ex->getMessage();
            $this->errno = $ex->getCode();
            return false;
        }
        $this->error = '';
        $this->errno = null;
        $this->affected_rows = $this->resource->rowCount();
        $this->insert_id = $this->resource->lastInsertId();
        return $this;
    }

    public function fetch($mode = PDO::FETCH_DEFAULT) {
        if (!$this->resource) {
            return false;
        }
        return $this->resource->fetch($mode);
    }

    public function fetchAll($mode = PDO::FETCH_DEFAULT) {
        if (!$this->resource) {
            return false;
        }
        return $this->resource->fetchAll($mode);
    }

    public function lastInsertId() {
        return $this->insert_id;
    }

    public function rowCount() {
        return $this->affected_rows;
    }

}

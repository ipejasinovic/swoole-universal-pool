<?php

namespace Swoole;

use PDO;
use Exception;
use PDOException;

final class MySQLConnection implements ConnectionInterface {

    private $config;
    private $conn;
    private $resource;

    const RETRY_ATTEMPTS = 1;

    public function __construct($config = null) {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new UniversalConfig();
        }
    }

    public function connect($config = null) {
        $this->conn = null;
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
        } catch (Exception $ex) {
            throw $ex;
        } catch (PDOException $ex) {
            throw $ex;
        }
    }

    public function __call($method, $args) {
        $retry_attempt = 0;
        do {
            try {
                if (is_callable(array($this->conn, $method))) {
                    $this->resource = call_user_func_array(array($this->conn, $method), $args);
                    return $this->resource;
                } else if (is_callable(array($this->resource, $method))) {
                    return call_user_func_array(array($this->resource, $method), $args);
                } else {
                    throw new Exception("Call to undefined method '{$method}'");
                }
            } catch (Exception $ex) {
                if (strpos($ex->getMessage(), 'server has gone away') !== false) {
                    ++$retry_attempt;
                } else {
                    throw $ex;
                }
            } catch (PDOException $ex) {
                if (strpos($ex->getMessage(), 'server has gone away') !== false) {
                    ++$retry_attempt;
                } else {
                    throw $ex;
                }
            }
            $this->connect();
        } while ($retry_attempt <= self::RETRY_ATTEMPTS);
    }

}

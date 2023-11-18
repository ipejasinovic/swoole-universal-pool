<?php

declare(strict_types=1);

namespace Swoole;

class UniversalConfig {

    public const DRIVER_MYSQL = 'mysql';
    public const DRIVER_PGSQL = 'pgsql';
    public const DRIVER_REDIS = 'redis';

    protected $driver = self::DRIVER_MYSQL;
    protected $host = '127.0.0.1';
    protected $port = 3306;
    protected $unixSocket;
    protected $dbname = 'test';
    protected $charset = 'utf8mb4';
    protected $username = 'root';
    protected $password = 'root';
    protected $options = [];
    protected $timeout = 0.0;
    protected $reserved = '';
    protected $retry_interval = 0;
    protected $read_timeout = 0.0;
    protected $auth = '';
    protected $dbIndex = 0;
    public $retry_number = 1;

    public function getDriver(): string {
	return $this->driver;
    }

    public function withDriver(string $driver): self {
	$this->driver = $driver;
	return $this;
    }

    public function getHost(): string {
	return $this->host;
    }

    public function withHost($host): self {
	$this->host = $host;
	return $this;
    }

    public function getPort(): int {
	return $this->port;
    }

    public function hasUnixSocket(): bool {
	return isset($this->unixSocket);
    }

    public function getUnixSocket(): string {
	return $this->unixSocket;
    }

    public function withUnixSocket(?string $unixSocket): self {
	$this->unixSocket = $unixSocket;
	return $this;
    }

    public function withPort(int $port): self {
	$this->port = $port;
	return $this;
    }

    public function getDbname(): string {
	return $this->dbname;
    }

    public function withDbname(string $dbname): self {
	$this->dbname = $dbname;
	return $this;
    }

    public function getCharset(): string {
	return $this->charset;
    }

    public function withCharset(string $charset): self {
	$this->charset = $charset;
	return $this;
    }

    public function getUsername(): string {
	return $this->username;
    }

    public function withUsername(string $username): self {
	$this->username = $username;
	return $this;
    }

    public function getPassword(): string {
	return $this->password;
    }

    public function withPassword(string $password): self {
	$this->password = $password;
	return $this;
    }

    public function getOptions(): array {
	return $this->options;
    }

    public function withOptions(array $options): self {
	$this->options = $options;
	return $this;
    }

    public function getTimeout(): float {
	return $this->timeout;
    }

    public function withTimeout(float $timeout): self {
	$this->timeout = $timeout;
	return $this;
    }

    public function getReserved(): string {
	return $this->reserved;
    }

    public function withReserved(string $reserved): self {
	$this->reserved = $reserved;
	return $this;
    }

    public function getRetryInterval(): int {
	return $this->retry_interval;
    }

    public function withRetryInterval(int $retry_interval): self {
	$this->retry_interval = $retry_interval;
	return $this;
    }

    public function getReadTimeout(): float {
	return $this->read_timeout;
    }

    public function withReadTimeout(float $read_timeout): self {
	$this->read_timeout = $read_timeout;
	return $this;
    }

    public function getAuth(): string {
	return $this->auth;
    }

    public function withAuth(string $auth): self {
	$this->auth = $auth;
	return $this;
    }

    public function getRetryNumber(): string {
	return $this->retry_number;
    }

    public function withRetryNumber(int $retry_number): self {
	$this->retry_number = $retry_number;
	return $this;
    }

    public function getDbIndex(): int {
	return $this->dbIndex;
    }

    public function withDbIndex(int $dbIndex): self {
	$this->dbIndex = $dbIndex;
	return $this;
    }

    public static function getAvailableDrivers() {
	return [
	    self::DRIVER_MYSQL,
	    self::DRIVER_PGSQL,
	    self::DRIVER_REDIS
	];
    }
}

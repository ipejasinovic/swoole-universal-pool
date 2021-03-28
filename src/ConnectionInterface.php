<?php

namespace Swoole;

interface ConnectionInterface {

    public function connect($config = null);

    public function begin();

    public function commit();

    public function query($sql);

    public function fetch($mode = null);

    public function fetchAll($mode = null);
}

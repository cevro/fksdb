<?php

namespace FKSDB\Tools;

use FKSDB\Bootstrap;

define('APP_DIR', __DIR__ . '/../app');

// Load Nette Framework
require APP_DIR . '/Bootstrap.php';

// Configure application
$configurator = Bootstrap::boot();

return $configurator->createContainer();

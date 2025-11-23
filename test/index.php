<?php

use Jorrmaglione\WaClient\WaClient;
use Jorrmaglione\WaClient\WaInstance;

include_once __DIR__ . '/../vendor/autoload.php';

try {
    $wApi = new WaClient('WzJs8wdeRHTwToIbY2elZJrK6E9UyZ9wWDUqBgRgbbdf7b93');
    $inst = new WaInstance($wApi, 86207);
    $inst->retrieveQRCode();
} catch (Exception $e) {
    fprintf(STDOUT, "Error: %s\n", $e->getMessage());
}

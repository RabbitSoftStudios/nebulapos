<?php
require_once __DIR__ . '/../../../pg_connection.php';
require_once __DIR__ . '/queue.php';

$pdo = pg_pool();

$job = Queue::take($pdo);

if (!$job) {
    exit; // no jobs
}

try {
    Queue::markProcessing($pdo, $job['id']);

    require __DIR__ . "/handlers/{$job['tipo']}.php";

    $handler = "handle_{$job['tipo']}";
    $handler($job['payload']);

    Queue::markDone($pdo, $job['id']);

} catch (Throwable $e) {

    Queue::markFailed($pdo, $job['id'], $e->getMessage());
}

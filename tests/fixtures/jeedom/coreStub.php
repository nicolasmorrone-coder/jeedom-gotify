<?php
require __DIR__ . '/vendor/autoload.php';
// Jeedom loads PSR-3 v1 through its log class before loading plugin vendors.
class log extends \Psr\Log\AbstractLogger {
    public function log($level, $message, array $context = []) {}
    public static function getLogger($name) { return new self(); }
}
class eqLogic {}
class cmd {}
class config {
    public static function byKey($key, $plugin) { return 'http://127.0.0.1:18993'; }
}

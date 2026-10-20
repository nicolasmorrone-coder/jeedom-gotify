<?php
// Run after composer install in the plugin and tests/fixtures/jeedom.
$root = dirname(__DIR__);
require __DIR__ . '/fixtures/jeedom/coreStub.php';
$temp = sys_get_temp_dir() . '/gotify-logger-' . bin2hex(random_bytes(8));
mkdir($temp . '/core/php', 0700, true);
mkdir($temp . '/plugins/gotify/core/class', 0700, true);
file_put_contents($temp . '/core/php/core.inc.php', '<?php');
copy($root . '/core/class/gotify.class.php', $temp . '/plugins/gotify/core/class/gotify.class.php');
symlink($root . '/vendor', $temp . '/plugins/gotify/vendor');
try {
    require $temp . '/plugins/gotify/core/class/gotify.class.php';
    $method = new ReflectionMethod('gotify', 'getClient');
    $method->setAccessible(true);
    $client = $method->invoke(new gotify(), 'synthetic-token');
    if (!($client->getLogger() instanceof log)) {
        throw new RuntimeException('Expected the Jeedom logger');
    }
    if (class_exists('Psr\\Log\\NullLogger', false)) {
        throw new RuntimeException('Plugin PSR-3 v3 NullLogger must not be loaded over the v1 interface');
    }
    echo "PASS: Gotify HTTP client uses the Jeedom PSR-3 v1 logger with plugin PSR-3 v3 installed\n";
} finally {
    unlink($temp . '/plugins/gotify/vendor');
    unlink($temp . '/plugins/gotify/core/class/gotify.class.php');
    unlink($temp . '/core/php/core.inc.php');
    foreach (['/plugins/gotify/core/class', '/plugins/gotify/core', '/plugins/gotify', '/plugins', '/core/php', '/core', ''] as $dir) {
        rmdir($temp . $dir);
    }
}

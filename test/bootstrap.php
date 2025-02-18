<?php
// vendor at component dir
use Composer\Autoload\ClassLoader;
use SwoftTest\Testing\TestApplication;
use Swoole\Runtime;

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
    // application's vendor
} elseif (file_exists(dirname(__DIR__, 5) . '/autoload.php')) {

    /** @var ClassLoader $loader */
    $loader = require dirname(__DIR__, 5) . '/autoload.php';

    // need load testing psr4 config map
    $componentDir  = dirname(__DIR__, 4) . '/component';
    $componentJson = $componentDir . '/composer.json';
    $composerData  = json_decode(file_get_contents($componentJson), true);

    foreach ($composerData['autoload-dev']['psr-4'] as $prefix => $dir) {
        $loader->addPsr4($prefix, $componentDir . '/' . $dir);
    }

    // need load testing psr4 config map
    $componentDir  = dirname(__DIR__, 4) . '/ext';
    $componentJson = $componentDir . '/composer.json';
    $composerData  = json_decode(file_get_contents($componentJson), true);

    foreach ($composerData['autoload-dev']['psr-4'] as $prefix => $dir) {
        $loader->addPsr4($prefix, $componentDir . '/' . $dir);
    }
} else {
    exit('Please run "composer install" to install the dependencies' . PHP_EOL);
}

// Always enable coroutine hook on server
CLog::info('Swoole\Runtime::enableCoroutine--swoft-limiter');
// 更安全的写法，先检查常量是否存在
$hookFlags = SWOOLE_HOOK_ALL;
if (defined('SWOOLE_HOOK_CURL')) {
    $hookFlags ^= SWOOLE_HOOK_CURL;
}
Runtime::enableCoroutine($hookFlags);
$application = new TestApplication();
$application->setBeanFile(__DIR__ . '/testing/bean.php');
$application->run();
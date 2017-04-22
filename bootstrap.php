<?php
/**
 * Selenium testing
 */

namespace Selenium;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Justbefit\Utility\Strings;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

require_once('../../vendor/autoload.php');

const FIREFOX = 'firefox';
const CHROME = 'chrome';
const PHANTOM = 'phantomjs';

echo "Justbefit Selenium tester\n";
//******************************************************


/**
 * Load config
 */
if (file_exists('config_local.php')) {
    require_once('config_local.php');
} else {
    require_once('config.php');
}

const HOST = 'http://' . SELENIUM_HOST . ':' . SELENIUM_PORT . '/wd/hub';

if (!defined('DRIVERS')) {
    throw new \Exception('No defined DRIVERS');
}

$testCases = [];
$finder = new Finder();
foreach ($finder->files('*Test.php')->in('TestCase') as $file) {
    /** @var SplFileInfo $file */
    $testCases[$file->getPathname()] = str_replace('/', '\\', Strings::replacePostfix($file->getPathname(), '.php'));
}

$testsCount = 0;
$timeProfile = microtime(true);

//Run test at each browser
foreach (DRIVERS as $driver) {
    $driverTestsCount = 0;
    $driverTimeProfile = microtime(true);
    switch ($driver) {
        case FIREFOX:
            $capabilities = DesiredCapabilities::firefox();
            break;
        case CHROME:
            $capabilities = DesiredCapabilities::chrome();
            break;
        default:
            throw new \Exception('Driver ' . $driver . ' not found');
    }

    echo "\nRunning on " . $capabilities->getBrowserName() . "\n";

    $browser = RemoteWebDriver::create(HOST, $capabilities, DRIVER_CONNECTION_TIMEOUT);

    //and each TestCase
    foreach ($testCases as $caseFile => $case) {
        require_once($caseFile);
        /**
         * @var SeleniumTestCase $test
         */
        $class = __NAMESPACE__ . '\\' . str_replace('/', '\\', $case);
        $test = new $class($browser);
        try {
            $driverTestsCount = $test->runTests();
        } catch (\Exception $e) {
            $browser->quit();
            echo "\033[01;31m";
            throw $e;
        }
    }

    $driverTotalTime = microtime(true) - $driverTimeProfile;
    echo "\nTotal time for " . $capabilities->getBrowserName() . ": " . sprintf('%f', $driverTotalTime) . " sec.\n";
    echo "Total tests for " . $capabilities->getBrowserName() . ": {$driverTestsCount}\n";

    $testsCount += $driverTestsCount;
    $browser->quit();
}

//Tests end status
$totalTime = microtime(true) - $timeProfile;
echo "\n________________________________________________\n";
echo "\nTotal time: " . sprintf('%f', $totalTime) . " sec.\n";
echo "Total tests: {$testsCount}\n";
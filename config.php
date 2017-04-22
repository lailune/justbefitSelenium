<?php
/**
* Copy this file as config_local.php
*/

/**
 * Selenium server connection params
 */
const SELENIUM_HOST = 'localhost';
const SELENIUM_PORT = '4444';

/**
 * Browsers for testing
 */
const DRIVERS = [
    \Tests\Selenium\FIREFOX,
    \Tests\Selenium\CHROME
];

/**
 * Browser connection default timeout
 */
const DRIVER_CONNECTION_TIMEOUT = 5000;

/**
 * Site for testing
 */
const DEV_HOST = 'http://justbefit.local';
<?php
namespace Selenium;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Justbefit\Utility\Strings;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeleniumTestCase extends WebTestCase
{

    /**
     * Current browser driver
     * @var RemoteWebDriver
     */
    protected $_browser = null;

    /**
     * Test list
     * @var array
     */
    protected $_tests = [];

    /**
     * SeleniumTestCase constructor.
     * @param RemoteWebDriver $browser
     */
    public function __construct($browser)
    {
        parent::__construct();
        $this->_browser = $browser;
    }

    /**
     * Run tests in TestCase
     */
    public function runTests()
    {
        $this->_tests = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (Strings::startsWith($method, 'test')) {
                $this->_tests[] = $method;
            }
        }

        $this->setUp();

        foreach ($this->_tests as $test) {
            $this->$test();
        }

        return count($this->_tests);
    }

    /**
     * Maximize browser window
     */
    public function maximize()
    {
        $this->_browser->manage()->window()->maximize();
    }

    /**
     * Run JavaScript synced
     * @param string $javascript
     * @param [] $args
     * @return mixed
     */
    public function evalJs($javascript, $args = [])
    {
        try {
            return $this->_browser->executeScript($javascript, $args);
        } catch (\Exception $e) {
            echo $e->getMessage(), $e->getTraceAsString();
            return null;
        }
    }

    /**
     * Run JavaScript in async mode. Wait for callback before timeout $wait
     * @param string $javascript
     * @param int $wait
     * @param array $args
     * @return mixed
     */
    public function evalAsyncJs($javascript, $wait = 5000, $args = [])
    {
        $this->_browser->manage()->timeouts()->setScriptTimeout($wait);
        try {
            return $this->_browser->executeAsyncScript($javascript, $args);
        } catch (\Exception $e) {
            echo $e->getMessage(), $e->getTraceAsString();
            return null;
        }
    }

    /**
     * Accept any dialog
     */
    public function dialogAccept()
    {
        $this->_browser->switchTo()->alert()->accept();
    }

    /**
     * Dismiss any dialog. Press close button for alert
     */
    public function dialogDeny()
    {
        $this->_browser->switchTo()->alert()->dismiss();
    }

    /**
     * Get dialog text
     * @return string
     */
    public function dialogGetText()
    {
        return $this->_browser->switchTo()->alert()->getText();
    }

    /**
     * Returns dialog instance
     * @return \Facebook\WebDriver\WebDriverAlert
     */
    public function dialog()
    {
        return $this->_browser->switchTo()->alert();
    }

    /**
     * Wait for all ajax requests end
     * @param string $framework
     * @throws \Exception
     */
    public function waitForAjax($framework = 'jquery')
    {
        // javascript framework
        switch ($framework) {
            case 'jquery':
                $code = "return jQuery.active;";
                break;
            case 'prototype':
                $code = "return Ajax.activeRequestCount;";
                break;
            case 'dojo':
                $code = "return dojo.io.XMLHTTPTransport.inFlight.length;";
                break;
            default:
                throw new \Exception('Not supported framework');
        }

        $this->_browser->wait(30, 2000)->until(
            function ($driver, $code) {
                /**
                 * @var RemoteWebDriver $driver
                 */
                return !$this->_browser->executeScript($code);
            }
        );
    }

    /**
     * Alias of $this->_browser->findElement
     * @param WebDriverBy $by
     * @return \Facebook\WebDriver\Remote\RemoteWebElement
     */
    public function find($by){
        return $this->_browser->findElement($by);
    }

    /**
     * Dummy set up
     */
    public function setUp()
    {
    }

}
<?php

namespace Selenium\TestCase\Controller;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Selenium\SeleniumTestCase;


class IndexControllerTest extends SeleniumTestCase
{

    /**
     * Test example
     */
    public function testTest()
    {
        $this->maximize();
        $this->_browser->get(DEV_HOST);

        $this->assertEquals('Justbefit', $this->_browser->getTitle());
        $this->find(WebDriverBy::name('min-price'))->sendKeys('1000');
        $this->find(WebDriverBy::name('max-price'))->sendKeys('2000');
        $this->find(WebDriverBy::className('js-main-search--btn'))->click();

        $this->_browser->wait(10, 1000)
                       ->until(WebDriverExpectedCondition::titleIs('Justbefit – Абонементы'));
    }
}
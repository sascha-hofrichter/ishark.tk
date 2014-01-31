<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{

    private $baseUrl;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->baseUrl = 'http://localhost:8080';
        // Initialize your context here
        $this->useContext('WebApiContext', new \WebApiContext($this->baseUrl));
    }

    /**
     * Sends HTTP request to specific URL with raw body from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with raw file body:$/
     */
    public function iSendAPutRequestToRawFileBody($method, $url, PyStringNode $string)
    {
        /** @var \Behat\CommonContexts\WebApiContext $webApiContext */
        $webApiContext = $this->getSubcontext('WebApiContext');
        $url = $this->baseUrl . '/' . ltrim($webApiContext->replacePlaceHolder($url), '/');
        $string = $webApiContext->replacePlaceHolder(trim($string));

        $string = __DIR__ . '/../' . $string;
        $string = file_get_contents($string);

        $webApiContext->getBrowser()->call($url, $method, $webApiContext->getHeaders(), $string);
    }
}

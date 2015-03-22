<?php

namespace Mib\Component\Curl;

use Mib\Component\Curl\Exception\InvalidUrlException;

/**
 * Curl
 * @package Mib\Component\Curl
 */
class Curl
{
    private $resource;

    private $url;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Initializes the curl handle
     * @throws Exception
     * @return void
     */
    protected function init()
    {
        $resource = curl_init();

        if ($resource === false) {
            throw new Exception('curl_init() failed');
        }

        $this->resource = $resource;
    }

    /**
     * Sets the curl option with the given value
     * @param mixed $option the option parameter
     * @param mixed $value  the corresponding value
     * @return void
     */
    protected function setOption($option, $value)
    {
        curl_setopt($this->resource, $option, $value);
    }

    /**
     * Creates an exception from the curl error code
     * @return Exception
     */
    protected function createExceptionFromErrorCode()
    {
        $errorCode = curl_errno($this->resource);
        $errorStr  = curl_error($this->resource);

        return new Exception(sprintf('%s: %s', $errorCode, $errorStr));
    }

    /**
     * Performs the curl session
     * @return bool|string
     */
    protected function exec()
    {
        return curl_exec($this->resource);
    }

    /**
     * Sets the url for the request
     * @param string $url the request url
     * @throws InvalidUrlException
     * @return $this
     */
    public function setUrl($url)
    {
        $url = filter_var($url, FILTER_VALIDATE_URL);

        if ($url === false) {
            throw new InvalidUrlException(
                sprintf("invalid url")
            );
        }

        $this->setOption(CURLOPT_URL, $url);

        $this->url = $url;

        return $this;
    }

    /**
     * follow redirect
     * @param bool $follow following status
     * @return $this;
     */
    public function followRedirects($follow = true)
    {
        $value = 1;

        if (!$follow) {
            $value = 0;
        }

        $this->setOption(CURLOPT_FOLLOWLOCATION, $value);

        return $this;
    }

    /**
     * Return the response data
     * @param bool $return whether to return the reponse data or not
     * @return $this
     */
    public function returnTransfer($return = true)
    {
        $value = 1;

        if (!$return) {
            $value = 0;
        }

        $this->setOption(CURLOPT_RETURNTRANSFER, $value);

        return $this;
    }

    /**
     * Sends the request as a get request and returns the response
     * or a boolean depending on the settings and the status
     * @return bool|string
     * @throws Exception
     */
    public function get()
    {
        $this->validateUrl();

        $result = $this->exec();

        $this->validateResult($result);

        return $result;
    }

    /**
     * Sends the request as a post request and returns the response
     * or a boolean depending on the settings and the status
     * @param array $fields post field data
     * @return bool|string
     * @throws Exception
     */
    public function post(array $fields = array())
    {

        $this->validateUrl();

        $requestString = http_build_query($fields);

        $this->setOption(CURLOPT_POST, 1);
        $this->setOption(CURLOPT_POSTFIELDS, $requestString);

        return $this->exec();
    }

    /**
     * validates if an url is set
     * @throws Exception
     * @return void
     */
    private function validateUrl()
    {
        if ($this->url === null) {
            throw new Exception('an url have to be set in order to complete the request');
        }
    }

    /**
     * @param $result
     * @throws Exception
     */
    private function validateResult($result)
    {
        if ($result == false) {
            $exception = $this->createExceptionFromErrorCode();
            throw $exception;
        }
    }
}

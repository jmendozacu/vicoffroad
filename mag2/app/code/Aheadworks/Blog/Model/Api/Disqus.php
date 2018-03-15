<?php
namespace Aheadworks\Blog\Model\Api;

use Aheadworks\Blog\Helper\Config;

/**
 * Disqus Api
 * @package Aheadworks\Blog\Model\Api
 */
class Disqus
{
    /**
     * API version
     *
     * @var string
     */
    protected $version = '3.0';

    /**
     * Default request method
     *
     * @var string
     */
    protected $method = \Zend_Http_Client::GET;

    /**
     * Default output type
     *
     * @var string
     */
    protected $outputType = 'json';

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    protected $curlFactory;

    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Aheadworks\Blog\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Aheadworks\Blog\Helper\Config $configHelper
    ) {
        $this->curlFactory = $curlFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * Send request
     *
     * @param string $resource
     * @param array $args
     * @return array|bool
     */
    public function sendRequest($resource, $args = [])
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(['timeout' => 60, 'header' => false]);
        $curl->write($this->method, $this->getEndpoint($resource, $args));
        try {
            $response = \Zend_Json::decode($curl->read());
            $response = isset($response['response']) ? $response['response'] : false;
        } catch (\Exception $e) {
            $response = false;
        }
        $curl->close();
        return $response;
    }

    /**
     * Get prepared endpoint url
     *
     * @param string $resource
     * @param array $args
     * @return string
     */
    protected function getEndpoint($resource, $args = [])
    {
        $endpoint = 'https://disqus.com/api/' . $this->version . '/' .
            $resource . '.' . $this->outputType;
        $rawParams = array_merge(
            ['api_secret' => $this->configHelper->getValue(Config::XML_GENERAL_DISQUS_SECRET_KEY)],
            $args
        ); // todo: store ID

        $params = [];
        foreach ($rawParams as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $params[] = $key . '[]=' . urlencode($item);
                }
            } else {
                $params[] = $key . '=' . urlencode($value);
            }
        }
        $endpoint .= '?' . implode('&', $params);

        return $endpoint;
    }
}

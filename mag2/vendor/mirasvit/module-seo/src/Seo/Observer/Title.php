<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   1.0.38
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;

class Title extends \Magento\Framework\Model\AbstractModel implements ObserverInterface
{
    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Mirasvit\Seo\Helper\Data   $seoData
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\Registry $registry
    ) {
        $this->seoData = $seoData;
        $this->registry = $registry;
    }

    /**
     * @param string $e
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function modifyHtmlResponseTitle($e)
    {
        if (!$this->registry->registry('current_product')
            || $this->seoData->isIgnoredActions()) {
                return;
        }

        $seo = $this->seoData->getCurrentSeo();

        if (!$seo || !trim($seo->getTitle()) || !is_object($e)) {
            return;
        }

        $response = $e->getResponse();

        $body = $response->getBody();

        if (!$this->_hasDoctype(trim($body))) {
            return;
        }

        $body = $this->_replaceFirstLevelTitle($body, $seo->getTitle());

        $response->setBody($body);
    }

    /**
     * @param string $body
     * @return bool
     */
    protected function _hasDoctype($body)
    {
        $doctypeCode = ['<!doctype html', '<html', '<?xml'];
        foreach ($doctypeCode as $doctype) {
            if (stripos($body, $doctype) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $body
     * @param string $seoTitle
     * @return string
     */
    protected function _replaceFirstLevelTitle($body, $seoTitle)
    {
        $firstLevelTitle = [];
        $firstLevelTitlePrepared = [];
        $patterns = ['/<h1(.*?)>(.*?)<\/h1>/ims', '/<h1>(.*?)<\/h1>/ims'];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $body, $firstLevelTitle[]);
        }

        foreach ($firstLevelTitle as $title) {
            if (isset($title[0][0]) && strpos($title[0][0], '</h1>') !== false) {
                $firstLevelTitlePrepared[] = $title[0][0];
            }
        }

        if (isset($firstLevelTitlePrepared[0])
            && $firstLevelTitlePrepared[0]
            && count($firstLevelTitlePrepared) == 1
            && ($titleText = trim(strip_tags($firstLevelTitlePrepared[0])))
            && $titleText != $seoTitle
            ) {
            $title = str_replace($titleText, $seoTitle, $firstLevelTitlePrepared[0]);
            $body = str_replace($firstLevelTitlePrepared[0], $title, $body);
        }

        return $body;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->modifyHtmlResponseTitle($observer);
    }
}

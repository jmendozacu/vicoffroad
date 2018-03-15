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



namespace Mirasvit\Seo\Block;

/**
 * Блок для вывода SEO описания в футере магазина.
 */
class Description extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Seoautolink\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\SeoAutolink\Helper\Replace
     */
    protected $seoautolinkData;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Seoautolink\Model\Config               $config
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Mirasvit\Seo\Helper\Data                        $seoData
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Seoautolink\Model\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->config = $config;
        $this->seoautolinkData = $objectManager->get('\Mirasvit\SeoAutolink\Helper\Replace');
        $this->seoData = $seoData;
        $this->moduleManager = $moduleManager;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|string
     */
    public function getDescription()
    {
        if ($this->moduleManager->isEnabled('Mirasvit_SeoAutolink')) {
            if (in_array(
                \Mirasvit\SeoAutolink\Model\Config\Source\Target::SEO_DESCRIPTION,
                $this->config->getTarget()
            )
            ) {
                return $this->seoautolinkData->addLinks($this->seoData->getCurrentSeo()->getDescription());
            }
        }

        return $this->seoData->getCurrentSeo()->getDescription();
    }
}

<?php
namespace Aheadworks\Blog\Helper;

use Magento\Store\Model\ScopeInterface;
use Aheadworks\Blog\Model\Source\Config\Cms\Block as BlockConfig;

/**
 * Cms block helper
 * @package Aheadworks\Blog\Helper
 */
class CmsBlock extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $cmsBlockFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $cmsFilterProvider;

    /**
     * @var array
     */
    protected $cmsBlocks = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\BlockFactory $cmsBlockFactory
     * @param \Magento\Cms\Model\Template\FilterProvider $cmsFilterProvider
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\BlockFactory $cmsBlockFactory,
        \Magento\Cms\Model\Template\FilterProvider $cmsFilterProvider
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->cmsBlockFactory = $cmsBlockFactory;
        $this->cmsFilterProvider = $cmsFilterProvider;
    }

    /**
     * Retrieves cms block using xml path
     *
     * @param  string $pathConfig
     * @return \Magento\Cms\Model\Block|bool
     */
    public function getBlock($pathConfig)
    {
        if (!isset($this->cmsBlocks[$pathConfig])) {
            $cmsBlockId = $this->scopeConfig->getValue(
                $pathConfig,
                ScopeInterface::SCOPE_STORE
            );
            if ($cmsBlockId && $cmsBlockId != BlockConfig::DONT_DISPLAY) {
                $this->cmsBlocks[$pathConfig] = $this->cmsBlockFactory->create()
                    ->setStoreId($this->storeManager->getStore()->getId())
                    ->load($cmsBlockId);
            } else {
                $this->cmsBlocks[$pathConfig] = false;
            }
        }
        return $this->cmsBlocks[$pathConfig];
    }

    /**
     * Filter content of cms block
     *
     * @param  string $content
     * @return string
     */
    public function filter($content)
    {
        return $this->cmsFilterProvider->getBlockFilter()
            ->setStoreId($this->storeManager->getStore()->getId())
            ->filter($content);
    }
}

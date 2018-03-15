<?php
/** @var $category \Aheadworks\Blog\Model\Category */
$category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Aheadworks\Blog\Model\Category');
$category
    ->setName('Category')
    ->setUrlKey('cat')
    ->setStatus(\Aheadworks\Blog\Model\Category::STATUS_ENABLED)
    ->setSortOrder(0)
    ->setMetaTitle('Category - aheadWorks Blog')
    ->setMetaDescription('Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu.')
    ->setStores(
        [
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getId()
        ]
    )
    ->save();

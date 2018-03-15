<?php
include __DIR__ . '/category.php';

/** @var $post \Aheadworks\Blog\Model\Post */
$post = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Aheadworks\Blog\Model\Post');
$post
    ->setTitle('Post')
    ->setUrlKey('post')
    ->setShortContent(
        '<h1>Lorem ipsum dolor sit amet</h1>
        <p>Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu.</p>'
    )
    ->setContent(
        '<h1>Lorem ipsum dolor sit amet</h1>
        <p>Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu. At mazim possit noluisse sea, liber accumsan vituperata an usu.</p>'
    )
    ->setIsAllowComments(1)
    ->setMetaTitle('Post - aheadWorks Blog')
    ->setMetaDescription('Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu.')
    ->setStores(
        [
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getId()
        ]
    )
    ->setCategories(
        [
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Aheadworks\Blog\Model\Category')
                ->load('cat', 'url_key')
                ->getId()
        ]
    )
    ->setTags(['tag 1', 'tag 2'])
    ->save();

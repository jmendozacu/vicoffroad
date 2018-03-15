<?php
$tagsData = [
    ['name' => 'Tag 1'],
    ['name' => 'Tag 2'],
    ['name' => 'Tag 3'],
    ['name' => 'Tag 4'],
    ['name' => 'Tag 5'],
    ['name' => 'Tag 6']
];
foreach ($tagsData as $data) {
    /** @var $tag \Aheadworks\Blog\Model\Tag */
    $tag = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
        ->create('Aheadworks\Blog\Model\Tag');
    $tag->setData($data)->save();
}

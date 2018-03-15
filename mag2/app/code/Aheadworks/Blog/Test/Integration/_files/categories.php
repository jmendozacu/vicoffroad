<?php
$categoriesData = [
    [
        'name' => 'Category 1',
        'url_key' => 'cat1',
        'status' => \Aheadworks\Blog\Model\Category::STATUS_ENABLED,
        'sort_order' => 0,
        'meta_title' => 'Category 1 meta title',
        'meta_description' => 'Category 1 meta description'
    ],
    [
        'name' => 'Category 2',
        'url_key' => 'cat2',
        'status' => \Aheadworks\Blog\Model\Category::STATUS_ENABLED,
        'sort_order' => 1,
        'meta_title' => 'Category 2 meta title',
        'meta_description' => 'Category 2 meta description'
    ],
    [
        'name' => 'Category 3',
        'url_key' => 'cat3',
        'status' => \Aheadworks\Blog\Model\Category::STATUS_ENABLED,
        'sort_order' => 2,
        'meta_title' => 'Category 3 meta title',
        'meta_description' => 'Category 3 meta description'
    ]
];
foreach ($categoriesData as $data) {
    /** @var $category \Aheadworks\Blog\Model\Category */
    $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
        ->create('Aheadworks\Blog\Model\Category');
    $category->setData($data)->setStores([0])->save();
}

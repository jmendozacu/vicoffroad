<?php
/** @var $category \Aheadworks\Blog\Model\Category */
$category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Aheadworks\Blog\Model\Category');
$category->setData(
    [
        'name' => 'Category for posts',
        'url_key' => 'cat-for-posts',
        'status' => \Aheadworks\Blog\Model\Category::STATUS_ENABLED,
        'sort_order' => 0,
        'meta_title' => 'Category for posts meta title',
        'meta_description' => 'Category for posts meta description',
        'stores' => [0]
    ]
)
->save();
$categoryId = $category->getId();

$postsData = [
    [
        'title' => 'Post 1',
        'url_key' => 'post1',
        'short_content' => '<h1>Short content head 1</h1><p>Short content 1</p>',
        'content' => '<h1>Content head 1</h1><p>Content 1</p>',
        'is_allow_comments' => 1,
        'meta_title' => 'Post 1 meta title',
        'meta_description' => 'Post 1 meta description',
        'tags' => ['tag 1', 'tag 2', 'tag 3']
    ],
    [
        'title' => 'Post 2',
        'url_key' => 'post2',
        'short_content' => '<h1>Short content head 2</h1><p>Short content 2</p>',
        'content' => '<h1>Content head 2</h1><p>Content 2</p>',
        'is_allow_comments' => 1,
        'meta_title' => 'Post 2 meta title',
        'meta_description' => 'Post 2 meta description',
        'tags' => ['tag 1', 'tag 2', 'tag 4']
    ],
    [
        'title' => 'Post 3',
        'url_key' => 'post3',
        'short_content' => '<h1>Short content head 3</h1><p>Short content 3</p>',
        'content' => '<h1>Content head 3</h1><p>Content 3</p>',
        'is_allow_comments' => 1,
        'meta_title' => 'Post 3 meta title',
        'meta_description' => 'Post 3 meta description',
        'tags' => ['tag 1', 'tag 5', 'tag 6']
    ]
];
foreach ($postsData as $data) {
    /** @var $post \Aheadworks\Blog\Model\Post */
    $post = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
        ->create('Aheadworks\Blog\Model\Post');
    $post->setData($data)
        ->setCategories([$categoryId])
        ->setStores([0])
        ->save();
}

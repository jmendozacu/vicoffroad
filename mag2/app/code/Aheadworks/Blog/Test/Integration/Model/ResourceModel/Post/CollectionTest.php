<?php
namespace Aheadworks\Blog\Test\Integration\Model\ResourceModel\Post;

use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;

/**
 * Class CollectionTest
 * @package Aheadworks\Blog\Test\Integration\Model\ResourceModel\Post
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Post\Collection
     */
    protected $postCollection;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Collection
     */
    protected $collectionHelper;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->postCollection = $this->getEmptyPostCollection();
        $this->fixtureHelper = $objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Fixture');
        $this->collectionHelper = $objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Collection');
    }

    /**
     * @return \Aheadworks\Blog\Model\Post
     */
    protected function getEmptyPostModel()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Post');
    }

    /**
     * @return \Aheadworks\Blog\Model\ResourceModel\Post\Collection
     */
    protected function getEmptyPostCollection()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\ResourceModel\Post\Collection');
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testAddStoreFilter()
    {
        list($firstStoreId, $secondStoreId, $thirdStoreId) = $this->fixtureHelper->getFixtureStoreIds(
            ['fixturestore', 'secondstore', 'thirdstore']
        );
        $this->collectionHelper->initPostsForFilter(
            [
                'post1' => [$firstStoreId, $secondStoreId],
                'post2' => [$firstStoreId, $secondStoreId],
                'post3' => [$firstStoreId]
            ],
            'stores'
        );

        $this->postCollection->addStoreFilter($firstStoreId);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2', 'post3']);

        $this->postCollection = $this->getEmptyPostCollection()->addStoreFilter($secondStoreId);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2']);

        $this->postCollection = $this->getEmptyPostCollection()->addStoreFilter($thirdStoreId);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, []);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/categories.php
     */
    public function testAddCategoryFilter()
    {
        list($firstCategoryId, $secondCategoryId, $thirdCategoryId) = $this->fixtureHelper->getFixtureCategoryIds(['cat1', 'cat2', 'cat3']);
        $this->collectionHelper->initPostsForFilter(
            [
                'post1' => [$firstCategoryId, $secondCategoryId],
                'post2' => [$firstCategoryId, $secondCategoryId],
                'post3' => [$firstCategoryId]
            ],
            'categories'
        );

        $this->postCollection->addCategoryFilter($firstCategoryId);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2', 'post3']);

        $this->postCollection = $this->getEmptyPostCollection()->addCategoryFilter($secondCategoryId);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2']);

        $this->postCollection = $this->getEmptyPostCollection()->addCategoryFilter($thirdCategoryId);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, []);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testAddTagFilter()
    {
        list($tag1Id, $tag2Id, $tag3Id, $tag4Id, $tag5Id, $tag6Id) = $this->fixtureHelper->getFixtureTagIds(
            ['tag 1', 'tag 2', 'tag 3', 'tag 4', 'tag 5', 'tag 6']
        );
        $this->collectionHelper->initPostsForFilter(
            [
                'post1' => ['tag 1', 'tag 2', 'tag 3'],
                'post2' => ['tag 1', 'tag 2', 'tag 4'],
                'post3' => ['tag 1', 'tag 5']
            ],
            'tags'
        );

        $this->postCollection->addTagFilter($tag1Id);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2', 'post3']);

        $this->postCollection = $this->getEmptyPostCollection()->addTagFilter($tag2Id);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2']);

        $this->postCollection = $this->getEmptyPostCollection()->addTagFilter($tag3Id);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1']);

        $this->postCollection = $this->getEmptyPostCollection()->addTagFilter($tag4Id);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post2']);

        $this->postCollection = $this->getEmptyPostCollection()->addTagFilter($tag5Id);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post3']);

        $this->postCollection = $this->getEmptyPostCollection()->addTagFilter($tag6Id);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, []);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testAddPublishedFilter()
    {
        $yesterday = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() - (24 * 60 * 60));
        $tomorrow = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() + (24 * 60 * 60));

        $this->collectionHelper->initPostsForFilter(['post1' => $yesterday, 'post2' => $tomorrow], 'publish_date');
        $this->collectionHelper->initPostsForFilter(['post3' => true], 'save_as_draft');

        $this->postCollection->addPublishedFilter();
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1']);

        $this->collectionHelper->initPostsForFilter(['post2' => $yesterday], 'publish_date');
        $this->postCollection = $this->getEmptyPostCollection()->addPublishedFilter();
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2']);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testAddStatusFilter()
    {
        $yesterday = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() - (24 * 60 * 60));
        $tomorrow = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() + (24 * 60 * 60));

        $this->collectionHelper->initPostsForFilter(['post1' => $yesterday, 'post2' => $tomorrow], 'publish_date');
        $this->collectionHelper->initPostsForFilter(['post3' => true], 'save_as_draft');

        $this->postCollection->addStatusFilter([PostStatus::PUBLICATION_PUBLISHED]);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1']);

        $this->postCollection = $this->getEmptyPostCollection()->addStatusFilter([PostStatus::PUBLICATION_SCHEDULED]);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post2']);

        $this->postCollection = $this->getEmptyPostCollection()->addStatusFilter([PostStatus::DRAFT]);
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post3']);

        $this->postCollection = $this->getEmptyPostCollection()->addStatusFilter(
            [
                PostStatus::PUBLICATION_PUBLISHED,
                PostStatus::PUBLICATION_SCHEDULED
            ]
        );
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2']);

        $this->postCollection = $this->getEmptyPostCollection()->addStatusFilter(
            [
                PostStatus::PUBLICATION_PUBLISHED,
                PostStatus::PUBLICATION_SCHEDULED,
                PostStatus::DRAFT
            ]
        );
        $this->collectionHelper->checkPostCollectionItems($this->postCollection, ['post1', 'post2', 'post3']);
    }
}

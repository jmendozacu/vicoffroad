<?php
namespace Aheadworks\Blog\Test\Integration\Model\ResourceModel\tag;

/**
 * Class CollectionTest
 * @package Aheadworks\Blog\Test\Integration\Model\ResourceModel\tag
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
     */
    protected $tagCollection;

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
        $this->tagCollection = $this->getEmptyTagCollection();
        $this->fixtureHelper = $objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Fixture');
        $this->collectionHelper = $objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Collection');
    }

    /**
     * @return \Aheadworks\Blog\Model\Tag
     */
    protected function getEmptyTagModel()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Tag');
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
     * @return \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
     */
    protected function getEmptyTagCollection()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\ResourceModel\Tag\Collection');
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
                'post2' => [$firstCategoryId, $thirdCategoryId],
                'post3' => [$firstCategoryId]
            ],
            'categories'
        );
        $this->collectionHelper->initPostsForFilter(
            [
                'post1' => ['tag 1', 'tag 2', 'tag 3'],
                'post2' => ['tag 1', 'tag 2', 'tag 4'],
                'post3' => ['tag 1', 'tag 5']
            ],
            'tags'
        );

        $this->tagCollection->addCategoryFilter($firstCategoryId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, ['tag 1', 'tag 2', 'tag 3', 'tag 4', 'tag 5']);

        $this->tagCollection = $this->getEmptyTagCollection()->addCategoryFilter($secondCategoryId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, ['tag 1', 'tag 2', 'tag 3']);

        $this->tagCollection = $this->getEmptyTagCollection()->addCategoryFilter($thirdCategoryId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, ['tag 1', 'tag 2', 'tag 4']);
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testAddPostsVisibilityFilter()
    {
        $yesterday = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() - (24 * 60 * 60));
        $tomorrow = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() + (24 * 60 * 60));

        $this->collectionHelper->initPostsForFilter(
            [
                'post1' => ['tag 1', 'tag 2', 'tag 3'],
                'post2' => ['tag 1', 'tag 2', 'tag 4'],
                'post3' => ['tag 1', 'tag 5']
            ],
            'tags'
        );
        list($firstStoreId, $secondStoreId, $thirdStoreId) = $this->fixtureHelper->getFixtureStoreIds(
            ['fixturestore', 'secondstore', 'thirdstore']
        );
        $this->collectionHelper->initPostsForFilter(
            [
                'post1' => [$firstStoreId],
                'post2' => [$firstStoreId, $secondStoreId],
                'post3' => [$firstStoreId, $secondStoreId]
            ],
            'stores'
        );
        $this->collectionHelper->initPostsForFilter(['post1' => $yesterday, 'post2' => $tomorrow], 'publish_date');
        $this->collectionHelper->initPostsForFilter(['post3' => true], 'save_as_draft');

        $this->tagCollection->addPostsVisibilityFilter($firstStoreId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, ['tag 1', 'tag 2', 'tag 3']);

        $this->tagCollection = $this->getEmptyTagCollection()->addPostsVisibilityFilter($secondStoreId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, []);

        $this->tagCollection = $this->getEmptyTagCollection()->addPostsVisibilityFilter($thirdStoreId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, []);

        $this->collectionHelper->initPostsForFilter(['post2' => $yesterday], 'publish_date');

        $this->tagCollection = $this->getEmptyTagCollection()->addPostsVisibilityFilter($firstStoreId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, ['tag 1', 'tag 2', 'tag 3', 'tag 4']);

        $this->tagCollection = $this->getEmptyTagCollection()->addPostsVisibilityFilter($secondStoreId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, ['tag 1', 'tag 2', 'tag 4']);

        $this->tagCollection = $this->getEmptyTagCollection()->addPostsVisibilityFilter($thirdStoreId);
        $this->collectionHelper->checkTagCollectionItems($this->tagCollection, []);
    }
}

<?php
namespace Aheadworks\Blog\Test\Integration\Model\ResourceModel\Category;

use Aheadworks\Blog\Model\Category;

/**
 * Class CollectionTest
 * @package Aheadworks\Blog\Test\Integration\Model\ResourceModel\Category
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

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
        $this->categoryCollection = $this->getEmptyCategoryCollection();
        $this->fixtureHelper = $objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Fixture');
        $this->collectionHelper = $objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Collection');
    }

    /**
     * @return \Aheadworks\Blog\Model\Category
     */
    protected function getEmptyCategoryModel()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Category');
    }

    /**
     * @return \Aheadworks\Blog\Model\ResourceModel\Category\Collection
     */
    protected function getEmptyCategoryCollection()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\ResourceModel\Category\Collection');
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/categories.php
     */
    public function testAddStoreFilter()
    {
        list($firstStoreId, $secondStoreId, $thirdStoreId) = $this->fixtureHelper->getFixtureStoreIds(
            ['fixturestore', 'secondstore', 'thirdstore']
        );
        $this->collectionHelper->initCategoriesForFilter(
            [
                'cat1' => [$firstStoreId, $secondStoreId],
                'cat2' => [$firstStoreId, $secondStoreId],
                'cat3' => [$firstStoreId]
            ],
            'stores'
        );

        $this->categoryCollection->addStoreFilter($firstStoreId);
        $this->collectionHelper->checkCategoryCollectionItems($this->categoryCollection, ['cat1', 'cat2', 'cat3']);

        $this->categoryCollection = $this->getEmptyCategoryCollection()->addStoreFilter($secondStoreId);
        $this->collectionHelper->checkCategoryCollectionItems($this->categoryCollection, ['cat1', 'cat2']);

        $this->categoryCollection = $this->getEmptyCategoryCollection()->addStoreFilter($thirdStoreId);
        $this->collectionHelper->checkCategoryCollectionItems($this->categoryCollection, []);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/categories.php
     */
    public function testAddEnabledFilter()
    {
        $this->collectionHelper->initCategoriesForFilter(
            [
                'cat1' => Category::STATUS_ENABLED,
                'cat2' => Category::STATUS_DISABLED,
                'cat3' => Category::STATUS_ENABLED
            ],
            'status'
        );

        $this->categoryCollection->addEnabledFilter();
        $this->collectionHelper->checkCategoryCollectionItems($this->categoryCollection, ['cat1', 'cat3']);
    }
}

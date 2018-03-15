<?php
namespace Aheadworks\Blog\Test\Integration\Model;

/**
 * Class CategoryTest
 * @package Aheadworks\Blog\Test\Integration\Model
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Category
     */
    protected $categoryModel;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Fixture
     */
    protected $fixtureHelper;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->categoryModel = $objectManager->create('Aheadworks\Blog\Model\Category');
        $this->fixtureHelper = $objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Fixture');
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     */
    public function testCRUD()
    {
        list($secondStoreId, $thirdStoreId) = $this->fixtureHelper->getFixtureStoreIds(['secondstore', 'thirdstore']);
        $this->categoryModel->setData(
            [
                'name' => 'Category',
                'url_key' => 'cat',
                'status' => \Aheadworks\Blog\Model\Category::STATUS_ENABLED,
                'sort_order' => 0,
                'meta_title' => 'Category - aheadWorks Blog',
                'meta_description' => 'Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu.',
                'stores' => [$secondStoreId, $thirdStoreId]
            ]
        );
        $crud = new \Magento\TestFramework\Entity(
            $this->categoryModel,
            [
                // testing only attach/update of related data
                'stores' => [$secondStoreId]
            ]
        );
        $crud->testCrud();
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testGetIdByUrlKey()
    {
        $this->assertNotNull($this->categoryModel->getIdByUrlKey('cat'));
    }

    /**
     * @dataProvider saveValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/categories.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testSaveValidation($invalidData)
    {
        $this->categoryModel->load('cat', 'url_key');
        foreach ($invalidData as $key => $value) {
            $this->categoryModel->setDataUsingMethod($key, $value);
        }
        $this->categoryModel->save();
    }

    /**
     * @return array
     */
    public function saveValidationDataProvider()
    {
        return [
            'empty name' => [['name' => '']],
            'empty URL-Key' => [['url_key' => '']],
            'numeric URL-Key' => [['url_key' => 123]],
            'invalid URL-Key' => [['url_key' => 'invalid key*^']],
            'duplicated URL-Key' => [['url_key' => 'cat1']],
            'empty stores' => [['stores' => []]]
        ];
    }
}

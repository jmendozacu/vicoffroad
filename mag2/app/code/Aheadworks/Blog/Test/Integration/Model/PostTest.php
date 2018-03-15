<?php
namespace Aheadworks\Blog\Test\Integration\Model;

use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;

/**
 * Class PostTest
 * @package Aheadworks\Blog\Test\Integration\Model
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Post
     */
    protected $postModel;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Fixture
     */
    protected $fixtureHelper;

    protected function setUp()
    {
        $this->postModel = $this->getEmptyPostModel();
        $this->fixtureHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Test\Integration\Helper\Fixture');
    }

    /**
     * Retrieve new post instance
     *
     * @return \Aheadworks\Blog\Model\Post
     */
    protected function getEmptyPostModel()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Post');
    }

    /**
     * Retrieve new tag instance
     *
     * @return \Aheadworks\Blog\Model\Tag
     */
    protected function getEmptyTagModel()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Model\Tag');
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/categories.php
     */
    public function testCRUD()
    {
        list($secondStoreId, $thirdStoreId) = $this->fixtureHelper->getFixtureStoreIds(['secondstore', 'thirdstore']);
        list($firstCategoryId, $secondCategoryId) = $this->fixtureHelper->getFixtureCategoryIds(['cat1', 'cat2']);
        $this->postModel->setData(
            [
                'title' => 'Post',
                'url_key' => 'post',
                'short_content' => '<h1>Lorem ipsum dolor sit amet</h1><p>Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu.</p>',
                'content' => '<h1>Lorem ipsum dolor sit amet</h1><p>Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu. At mazim possit noluisse sea, liber accumsan vituperata an usu.</p>',
                'is_allow_comments' => 1,
                'meta_title' => 'Post - aheadWorks Blog',
                'meta_description' => 'Lorem ipsum dolor sit amet, in magna aliquid quo, vim laudem fabellas quaerendum eu.',
                'stores' => [$secondStoreId, $thirdStoreId],
                'categories' => [$firstCategoryId, $secondCategoryId]
            ]
        );
        $crud = new \Magento\TestFramework\Entity(
            $this->postModel,
            [
                // testing only attach/update of related data
                'stores' => [$secondStoreId],
                'categories' => [$firstCategoryId]
            ]
        );
        $crud->testCrud();
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testGetIdByUrlKey()
    {
        $this->assertNotNull($this->postModel->getIdByUrlKey('post'));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testChangeStatus()
    {
        // Status 'draft'
        $this->postModel->load('post', 'url_key');
        $this->postModel
            ->setData('save_as_draft', true)
            ->save();
        $this->postModel = $this->getEmptyPostModel()->load('post', 'url_key');
        $this->assertEquals(PostStatus::DRAFT, $this->postModel->getStatus());
        $this->assertEquals(PostStatus::DRAFT, $this->postModel->getVirtualStatus());

        // Status 'publication', virtual status 'scheduled'
        $this->postModel = $this->getEmptyPostModel()->load('post', 'url_key');
        $this->postModel
            ->setPublishDate(
                date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() + (24 * 60 * 60))
            )
            ->save();
        $this->postModel = $this->getEmptyPostModel()->load('post', 'url_key');
        $this->assertEquals(PostStatus::PUBLICATION, $this->postModel->getStatus());
        $this->assertEquals(PostStatus::PUBLICATION_SCHEDULED, $this->postModel->getVirtualStatus());

        // Status 'publication', virtual status 'published'
        $this->postModel = $this->getEmptyPostModel()->load('post', 'url_key');
        $this->postModel
            ->setPublishDate(null)
            ->save();
        $this->postModel = $this->getEmptyPostModel()->load('post', 'url_key');
        $this->assertEquals(PostStatus::PUBLICATION, $this->postModel->getStatus());
        $this->assertEquals(PostStatus::PUBLICATION_PUBLISHED, $this->postModel->getVirtualStatus());
    }

    /**
     * @dataProvider saveValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testSaveValidation($invalidData)
    {
        $this->postModel->load('post', 'url_key');
        foreach ($invalidData as $key => $value) {
            $this->postModel->setDataUsingMethod($key, $value);
        }
        $this->postModel->save();
    }

    /**
     * @return array
     */
    public function saveValidationDataProvider()
    {
        return [
            'empty title' => [['title' => '']],
            'empty content' => [['content' => '']],
            'empty URL-Key' => [['url_key' => '']],
            'numeric URL-Key' => [['url_key' => 123]],
            'invalid URL-Key' => [['url_key' => 'invalid key*^']],
            'duplicated URL-Key' => [['url_key' => 'post1']]
        ];
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testCreateTags()
    {
        $tagNames = ['Post tag 1', 'Post tag 2'];
        $this->postModel->load('post', 'url_key');
        $this->postModel
            ->setTags($tagNames)
            ->save();
        $this->postModel = $this->getEmptyPostModel()
            ->load('post', 'url_key');
        $this->assertEmpty(array_diff($tagNames, $this->postModel->getTags()));

        foreach ($tagNames as $tagName) {
            $this->assertNotNull(
                $this->getEmptyTagModel()->loadByName($tagName)->getId()
            );
        }
    }
}

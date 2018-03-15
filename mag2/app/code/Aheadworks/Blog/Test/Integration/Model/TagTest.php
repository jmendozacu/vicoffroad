<?php
namespace Aheadworks\Blog\Test\Integration\Model;

/**
 * Class TagTest
 * @package Aheadworks\Blog\Test\Integration\Model
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Tag
     */
    protected $tagModel;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Fixture
     */
    protected $fixtureHelper;

    protected function setUp()
    {
        $this->tagModel = $this->getEmptyTagModel();
        $this->fixtureHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Test\Integration\Helper\Fixture');
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
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testCRUD()
    {
        list($firstPostId, $secondPostId) = $this->fixtureHelper->getFixturePostIds(['post1', 'post2']);
        $this->tagModel->setData(
            [
                'name' => 'Tag',
                'posts' => [$firstPostId, $secondPostId]
            ]
        );
        $crud = new \Magento\TestFramework\Entity(
            $this->tagModel,
            [
                // testing only attach/update of related data
                'posts' => [$firstPostId]
            ]
        );
        $crud->testCrud();
    }

    public function testLoadByName()
    {
        $tagName = 'Tag';
        $tagId = $this->tagModel
            ->setName($tagName)
            ->save()
            ->getId();
        $this->tagModel = $this->getEmptyTagModel()->load($tagId);
        $this->assertEquals($tagId, $this->getEmptyTagModel()->loadByName($tagName)->getId());

        $this->tagModel->delete();
    }

    /**
     * todo: refactor this test when store id will be taken into amount in count algorithm
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testCount()
    {
        list($firstPostId, $secondPostId) = $this->fixtureHelper->getFixturePostIds(['post1', 'post2']);

        // count = 0
        $this->tagModel
            ->setData(['name' => 'Tag', 'posts' => []])
            ->save();
        $tagId = $this->tagModel->getId();
        $this->tagModel = $this->getEmptyTagModel()->load($tagId);
        $this->assertEquals(0, $this->tagModel->getCount());

        // adding, count = 1
        $this->tagModel->addPost($firstPostId)->save();
        $this->tagModel = $this->getEmptyTagModel()->load($tagId);
        $this->assertEquals(1, $this->tagModel->getCount());

        // adding, count = 2
        $this->tagModel->addPost($secondPostId)->save();
        $this->tagModel = $this->getEmptyTagModel()->load($tagId);
        $this->assertEquals(2, $this->tagModel->getCount());

        // removing, count = 1
        $this->tagModel->removePost($secondPostId)->save();
        $this->tagModel = $this->getEmptyTagModel()->load($tagId);
        $this->assertEquals(1, $this->tagModel->getCount());

        // removing, count = 0
        $this->tagModel->removePost($firstPostId)->save();
        $this->tagModel = $this->getEmptyTagModel()->load($tagId);
        $this->assertEquals(0, $this->tagModel->getCount());

        $this->tagModel->delete();
    }

    /**
     * @dataProvider saveValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/tags.php
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testSaveValidation($invalidData)
    {
        $tagName = 'Tag';
        $tagId = $this->tagModel
            ->setName($tagName)
            ->save()
            ->getId();
        $this->tagModel->load($tagId);
        foreach ($invalidData as $key => $value) {
            $this->tagModel->setDataUsingMethod($key, $value);
        }
        $this->tagModel->save();
    }

    /**
     * @return array
     */
    public function saveValidationDataProvider()
    {
        return [
            'empty name' => [['name' => '']],
            'duplicated name' => [['name' => 'Tag 1']]
        ];
    }
}

<?php
namespace Aheadworks\Blog\Test\Integration\Ui\DataProvider;

/**
 * Class PostDataProviderTest
 *
 * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
 *
 * @package Aheadworks\Blog\Test\Integration\Ui\DataProvider
 */
class PostDataProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Ui\DataProvider\PostDataProvider
     */
    protected $postDataProvider;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Collection
     */
    protected $collectionHelper;

    /**
     * @var \Aheadworks\Blog\Test\Integration\Helper\Stub\Disqus
     */
    protected $disqusHelperStub;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->fixtureHelper = $this->objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Fixture');
        $this->collectionHelper = $this->objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Collection');
        $this->disqusHelperStub = $this->objectManager->create('Aheadworks\Blog\Test\Integration\Helper\Stub\Disqus');
        $this->postDataProvider = $this->createPostDataProvider();
    }

    /**
     * @return \Aheadworks\Blog\Ui\DataProvider\PostDataProvider
     */
    protected function createPostDataProvider()
    {
        return $this->objectManager->create(
            'Aheadworks\Blog\Ui\DataProvider\PostDataProvider',
            [
                'name' => 'aw_blog_post_listing_data_source',
                'primaryFieldName' => 'post_id',
                'requestFieldName' => 'post_id',
                'disqusHelper' => $this->disqusHelperStub
            ]
        );
    }

    /**
     * @return \Magento\Framework\Api\Filter
     */
    protected function createFilter()
    {
        return $this->objectManager->create('Magento\Framework\Api\Filter');
    }

    public function testSortByNewComments()
    {
        list($firstPostId, $secondPostId, $thirdPostId) = $this->fixtureHelper->getFixturePostIds(['post1', 'post2', 'post3']);
        $this->disqusHelperStub->setCommentsData(
            [
                $secondPostId => ['new' => 1],
                $thirdPostId => ['new' => 2],
                $firstPostId => ['new' => 3]
            ]
        );

        // ASC
        $this->postDataProvider->addOrder('new_comments', 'ASC');
        $items = $this->postDataProvider->getData()['items'];
        $this->assertEquals($secondPostId, array_shift($items)['post_id']);
        $this->assertEquals($thirdPostId, array_shift($items)['post_id']);
        $this->assertEquals($firstPostId, array_shift($items)['post_id']);

        // DESC
        $this->postDataProvider = $this->createPostDataProvider();
        $this->postDataProvider->addOrder('new_comments', 'DESC');
        $items = $this->postDataProvider->getData()['items'];
        $this->assertEquals($firstPostId, array_shift($items)['post_id']);
        $this->assertEquals($thirdPostId, array_shift($items)['post_id']);
        $this->assertEquals($secondPostId, array_shift($items)['post_id']);
    }

    public function testSortByVirtualStatus()
    {
        $yesterday = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() - (24 * 60 * 60));
        $tomorrow = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() + (24 * 60 * 60));

        $this->collectionHelper->initPostsForFilter(['post1' => $yesterday, 'post2' => $tomorrow], 'publish_date');
        $this->collectionHelper->initPostsForFilter(['post3' => true], 'save_as_draft');

        // ASC
        $this->postDataProvider->addOrder('status', 'ASC');
        $items = $this->postDataProvider->getData()['items'];
        $this->assertEquals('post3', array_shift($items)['url_key']);
        $this->assertEquals('post1', array_shift($items)['url_key']);
        $this->assertEquals('post2', array_shift($items)['url_key']);

        // DESC
        $this->postDataProvider = $this->createPostDataProvider();
        $this->postDataProvider->addOrder('status', 'DESC');
        $items = $this->postDataProvider->getData()['items'];
        $this->assertEquals('post2', array_shift($items)['url_key']);
        $this->assertEquals('post1', array_shift($items)['url_key']);
        $this->assertEquals('post3', array_shift($items)['url_key']);
    }

    /**
     * @dataProvider commentFiltersDataProvider
     */
    public function testFilterByNewComments($fromFilter, $toFilter, $expected)
    {
        list($firstPostId, $secondPostId, $thirdPostId) = $this->fixtureHelper->getFixturePostIds(['post1', 'post2', 'post3']);
        $this->disqusHelperStub->setCommentsData(
            [
                $firstPostId => ['new' => 15],
                $secondPostId => ['new' => 20],
                $thirdPostId => ['new' => 35]
            ]
        );
        if ($fromFilter) {
            $this->postDataProvider->addFilter(
                $this->createFilter()
                    ->setField('new_comments')
                    ->setConditionType($fromFilter['conditionType'])
                    ->setValue($fromFilter['value'])
            );
        }
        if ($toFilter) {
            $this->postDataProvider->addFilter(
                $this->createFilter()
                    ->setField('new_comments')
                    ->setConditionType($toFilter['conditionType'])
                    ->setValue($toFilter['value'])
            );
        }

        $items = $this->postDataProvider->getData()['items'];
        $this->assertCount(count($expected), $items);
        foreach ($items as $item) {
            $this->assertContains($item['url_key'], $expected);
        }
    }

    /**
     * @return array
     */
    public function commentFiltersDataProvider()
    {
        return [
            '0 - ...' => [
                ['conditionType' => 'gteq', 'value' => 0],
                null,
                ['post1', 'post2', 'post3']
            ],
            '0 - 20' => [
                ['conditionType' => 'gteq', 'value' => 0],
                ['conditionType' => 'lteq', 'value' => 20],
                ['post1', 'post2']
            ],
            '... - 20' => [
                null,
                ['conditionType' => 'lteq', 'value' => 30],
                ['post1', 'post2']
            ],
            '20 - ...' => [
                ['conditionType' => 'gteq', 'value' => 20],
                null,
                ['post2', 'post3']
            ],
            '20 - 40' => [
                ['conditionType' => 'gteq', 'value' => 20],
                ['conditionType' => 'lteq', 'value' => 40],
                ['post2', 'post3']
            ]
        ];
    }
}

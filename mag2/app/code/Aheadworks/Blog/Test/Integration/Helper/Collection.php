<?php
namespace Aheadworks\Blog\Test\Integration\Helper;

/**
 * Class Collection
 * @package Aheadworks\Blog\Test\Integration\Helper
 */
class Collection
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface|null
     */
    private $objectManager = null;

    /**
     * @return \Magento\Framework\ObjectManagerInterface|null
     */
    private function getObjectManager()
    {
        if ($this->objectManager === null) {
            $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        }
        return $this->objectManager;
    }

    /**
     * Modify given fields of any fixture model instance
     *
     * @param array $updateData
     * @param string $className
     * @param mixed $idFieldName
     * @return void
     */
    private function initInstanceForFilter($updateData, $className, $idFieldName = null)
    {
        foreach ($updateData as $modelId => $data) {
            $instance = $this->getObjectManager()->create($className)
                ->load($modelId, $idFieldName);
            foreach ($data as $key => $value) {
                $instance->setDataUsingMethod($key, $value);
            }
            $instance->save();
        }
    }

    /**
     * @param array $data
     * @param string $fieldName
     * @return void
     */
    public function initCategoriesForFilter($data, $fieldName)
    {
        $updateData = [];
        foreach ($data as $catUrlKey => $fieldValue) {
            $updateData[$catUrlKey] = [$fieldName => $fieldValue];
        }
        $this->initInstanceForFilter($updateData, 'Aheadworks\Blog\Model\Category', 'url_key');
    }

    /**
     * @param array $data
     * @param string $fieldName
     * @return void
     */
    public function initPostsForFilter($data, $fieldName)
    {
        $updateData = [];
        foreach ($data as $catUrlKey => $fieldValue) {
            $updateData[$catUrlKey] = [$fieldName => $fieldValue];
        }
        $this->initInstanceForFilter($updateData, 'Aheadworks\Blog\Model\Post', 'url_key');
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @param array $expectedItems
     * @param string $fieldName
     */
    public function checkCollectionItems($collection, $expectedItems, $fieldName)
    {
        \PHPUnit_Framework_Assert::assertCount(count($expectedItems), $collection->getItems());
        foreach ($collection as $item) {
            \PHPUnit_Framework_Assert::assertContains($item->getData($fieldName), $expectedItems);
        }
    }

    /**
     * @param \Aheadworks\Blog\Model\ResourceModel\Category\Collection $collection
     * @param array $expectedItems
     */
    public function checkCategoryCollectionItems($collection, $expectedItems)
    {
        $this->checkCollectionItems($collection, $expectedItems, 'url_key');
    }

    /**
     * @param \Aheadworks\Blog\Model\ResourceModel\Post\Collection $collection
     * @param array $expectedItems
     */
    public function checkPostCollectionItems($collection, $expectedItems)
    {
        $this->checkCollectionItems($collection, $expectedItems, 'url_key');
    }

    /**
     * @param \Aheadworks\Blog\Model\ResourceModel\Tag\Collection $collection
     * @param array $expectedItems
     */
    public function checkTagCollectionItems($collection, $expectedItems)
    {
        $this->checkCollectionItems($collection, $expectedItems, 'name');
    }
}

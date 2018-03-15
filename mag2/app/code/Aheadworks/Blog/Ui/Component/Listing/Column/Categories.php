<?php
namespace Aheadworks\Blog\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Categories extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Aheadworks\Blog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Aheadworks\Blog\Model\ResourceModel\Category\Collection $categoryCollection,
        array $components = [],
        array $data = []
    ) {
        $this->categoryCollection = $categoryCollection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $categories = $this->categoryCollection->toOptionHash();
            foreach ($dataSource['data']['items'] as & $post) {
                if (is_array($post['categories'])) {
                    $categoryNames = [];
                    foreach ($post['categories'] as $catId) {
                        if (isset($categories[$catId])) {
                            $categoryNames[] = $categories[$catId];
                        }
                    }
                    $post['categories'] = implode(', ', $categoryNames);
                }
            }
        }
        return $dataSource;
    }
}
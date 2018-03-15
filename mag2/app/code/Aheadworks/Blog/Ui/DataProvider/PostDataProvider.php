<?php
namespace Aheadworks\Blog\Ui\DataProvider;

use \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory;
use \Aheadworks\Blog\Model\Source\Post\Status;

/**
 * Class BlogDataProvider
 */
class PostDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Aheadworks\Blog\Helper\Disqus
     */
    protected $disqusHelper;

    /**
     * @var array|null
     */
    protected $commentsDataSort = null;

    /**
     * @var array|null
     */
    protected $commentsDataFilters = null;

    /**
     * @var string|null
     */
    protected $virtualStatusSort = null;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Aheadworks\Blog\Helper\Disqus $disqusHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Aheadworks\Blog\Helper\Disqus $disqusHelper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->disqusHelper = $disqusHelper;
        $this->collection = $collectionFactory->create();
        $this->addOrder('post_id', 'DESC');
    }

    /**
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
        $select = $this->getCollection()->getSelect();
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $this->commentsDataSort = null;
        if (in_array($field, ['published_comments', 'new_comments'])) {
            $this->commentsDataSort = ['field' => $field, 'dir' => $direction];
        } elseif ($field == 'status') {
            $this->virtualStatusSort = $direction;
        } else {
            $select->order(new \Zend_Db_Expr($field . ' ' . $direction));
        }
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
            $this->attachCommentsData();
            $this->attachVirtualStatuses();
        }
        $data = parent::getData();
        if (is_array($this->commentsDataSort)) {
            usort($data['items'], [$this, 'sortByCommentsData']);
        }
        if ($this->virtualStatusSort !== null) {
            usort($data['items'], [$this, 'sortByVirtualStatus']);
        }
        if ($this->commentsDataFilters !== null) {
            $data['items'] = array_values(
                array_filter($data['items'], [$this, 'filterCommentsData'])
            );
        }
        return $data;
    }

    /**
     * @return void
     */
    protected function attachCommentsData()
    {
        foreach ($this->getCollection() as $item) {
            $item->setNewComments($this->disqusHelper->getNewCommentsNum($item->getId()));
            $item->setPublishedComments($this->disqusHelper->getPublishedCommentsNum($item->getId()));
        }
    }

    /**
     * @return void
     */
    protected function attachVirtualStatuses()
    {
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time());
        foreach ($this->getCollection() as $item) {
            if ($item->getStatus() != Status::DRAFT) {
                if ($item->getPublishDate() > $now) {
                    $item->setStatus(Status::PUBLICATION_SCHEDULED);
                } else {
                    $item->setStatus(Status::PUBLICATION_PUBLISHED);
                }
            }
        }
    }

    /**
     * Sort data by published or new comments
     *
     * @param array $item1
     * @param array $item2
     * @return int
     */
    protected function sortByCommentsData(array $item1, array $item2)
    {
        $result = 0;
        $field = $this->commentsDataSort['field'];
        $direction = $this->commentsDataSort['dir'];

        if (!isset($item1[$field])) {
            $result = isset($item2[$field]) ? -1 : 0;
        } else {
            if (!isset($item2[$field])) {
                $result = 1;
            } else {
                if ($item1[$field] == $item2[$field]) {
                    $result = 0;
                } else {
                    $result = $item1[$field] < $item2[$field] ? -1 : 1;
                }
            }

        }

        return strtolower($direction) == 'asc' ? $result : -$result;
    }

    /**
     * Sort data by virtual status
     *
     * @param array $item1
     * @param array $item2
     * @return int
     */
    protected function sortByVirtualStatus(array $item1, array $item2)
    {
        $result = strnatcasecmp($item1['status'], $item2['status']);
        return strtolower($this->virtualStatusSort) == 'asc' ? $result : -$result;
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function filterCommentsData(array $item)
    {
        foreach ($this->commentsDataFilters as $field => $filters) {
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($filters as $condition => $filter) {
                $value = $filter->getValue();
                if ($condition == 'gteq' && $item[$field] < $value
                    || $condition == 'lteq' && $item[$field] > $value
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();
        if (in_array($field, ['published_comments', 'new_comments'])) {
            if ($this->commentsDataFilters === null) {
                $this->commentsDataFilters = [];
            }
            $this->commentsDataFilters[$field][$filter->getConditionType()] = $filter;
        } elseif ($field == 'status') {
            $allowedStatuses = $filter->getValue();
            $this->getCollection()->addStatusFilter($allowedStatuses);
        } else {
            parent::addFilter($filter);
        }
    }
}

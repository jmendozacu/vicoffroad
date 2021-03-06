<?php
namespace Aheadworks\Blog\Ui\Component\Listing;

use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;

/**
 * Class Bookmark
 */
class Bookmark extends \Magento\Ui\Component\Bookmark
{
    const BLOG_LISTING_NAMESPACE = 'aw_blog_post_listing';

    /**
     * @var \Magento\Ui\Api\Data\BookmarkInterfaceFactory
     */
    protected $bookmarkFactory;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $userContext;

    /**
     * @param \Magento\Ui\Api\Data\BookmarkInterfaceFactory $bookmarkFactory
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param ContextInterface $context
     * @param BookmarkRepositoryInterface $bookmarkRepository
     * @param BookmarkManagementInterface $bookmarkManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Ui\Api\Data\BookmarkInterfaceFactory $bookmarkFactory,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        ContextInterface $context,
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkManagementInterface $bookmarkManagement,
        array $components = [],
        array $data = []
    ) {
        $this->bookmarkFactory = $bookmarkFactory;
        $this->userContext = $userContext;
        parent::__construct($context, $bookmarkRepository, $bookmarkManagement, $components, $data);
    }

    /**
     * Register component
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getConfiguration($this);
        if (!isset($config['views'])) {
            $this->addView('default', __('Default View'));
            $this->addView(
                'drafts',
                __('Drafts'),
                [
                    'publish_date' =>       ['visible' => false],
                    'published_comments' => ['visible' => false],
                    'new_comments' =>       ['visible' => false],
                    'updated_at' =>         ['visible' => true],
                    'created_at' =>         ['visible' => true]
                ],
                ['status' => [Status::DRAFT]]
            );
            $this->addView(
                'scheduled',
                __('Scheduled Posts'),
                [
                    'published_comments' => ['visible' => false],
                    'new_comments' =>       ['visible' => false],
                    'updated_at' =>         ['visible' => true],
                    'created_at' =>         ['visible' => true]
                ],
                ['status' => [Status::PUBLICATION_SCHEDULED]]
            );
            $this->addView(
                'new_comments',
                __('New Comments'),
                [
                    'published_comments' => ['visible' => false],
                    'new_comments' =>       ['visible' => true],
                    'updated_at' =>         ['visible' => true],
                    'created_at' =>         ['visible' => true]
                ],
                ['new_comments' => ['from' => 1]]
            );
        }
    }

    /**
     * Add view to the current config and save the bookmark to db
     * @param $index
     * @param $label
     * @param array $changeColumns columns to change comparing to default view config. Array of
     *        elements $colName => ['sorting' => $sorting, 'visible' => $visible, 'position' => $position]
     * @param array $filters applied filters as $filterName => $filterValue array
     * @return $this
     */
    public function addView($index, $label, $changeColumns = [], $filters = [])
    {
        $config = $this->getConfiguration($this);

        $viewConf = $this->getDefaultViewConfig();
        $viewConf = array_merge($viewConf, [
            'index'     => $index,
            'label'     => $label,
            'value'     => $label,
            'editable'  => false
        ]);
        foreach ($changeColumns as $column => $columnData) {
            if (isset($columnData['sorting'])) {
                $viewConf['data']['columns'][$column]['sorting'] = $columnData['sorting'];
            }
            if (isset($columnData['visible'])) {
                $viewConf['data']['columns'][$column]['visible'] = $columnData['visible'];
            }
            if (isset($columnData['position'])) {
                $config['data']['positions'][$column] = $columnData['position'];
            }
        }
        foreach ($filters as $filterName => $filterValue) {
            $viewConf['data']['filters']['applied'][$filterName] = $filterValue;
        }

        $this->_saveBookmark($index, $label, $viewConf);

        $config['views'][$index] = $viewConf;
        $this->setData('config', array_replace_recursive($config, $this->getConfiguration($this)));
        return $this;
    }

    /**
     * Save bookmark to db
     * @param $index
     * @param $label
     * @param $viewConf
     */
    protected function _saveBookmark($index, $label, $viewConf)
    {
        $bookmark = $this->bookmarkFactory->create();
        $config = ['views' => [$index => $viewConf]];
        $bookmark->setUserId($this->userContext->getUserId())
            ->setNamespace(self::BLOG_LISTING_NAMESPACE)
            ->setIdentifier($index)
            ->setTitle($label)
            ->setConfig(json_encode($config));
        $this->bookmarkRepository->save($bookmark);
    }

    /**
     * @return mixed
     */
    public function getDefaultViewConfig()
    {
        $config['editable']  = false;
        $config['data']['filters']['applied']['placeholder'] = true;
        $config['data']['columns'] = [
            'title'             => ['sorting' => false, 'visible' => true],
            'status'            => ['sorting' => false, 'visible' => true],
            'publish_date'      => ['sorting' => false, 'visible' => true],
            'published_comments'=> ['sorting' => false, 'visible' => true],
            'new_comments'      => ['sorting' => false, 'visible' => true],
            'categories'        => ['sorting' => false, 'visible' => true],
            'tags'              => ['sorting' => false, 'visible' => true],
            'stores'            => ['sorting' => false, 'visible' => true],
            'author_name'       => ['sorting' => false, 'visible' => true],
            'updated_at'        => ['sorting' => false, 'visible' => false],
            'created_at'        => ['sorting' => 'desc', 'visible' => false]
        ];

        $position = 0;
        foreach (array_keys($config['data']['columns']) as $colName) {
            $config['data']['positions'][$colName] = $position;
            $position++;
        }

        $config['data']['paging'] = [
            'options' => [
                20 => ['value' => 20, 'label' => 20],
                30 => ['value' => 30, 'label' => 30],
                50 => ['value' => 50, 'label' => 50],
                100 => ['value' => 30, 'label' => 30],
                200 => ['value' => 30, 'label' => 30]
            ],
            'value' => 20
        ];
        return $config;
    }
}

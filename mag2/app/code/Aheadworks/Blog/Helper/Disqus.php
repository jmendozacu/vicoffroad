<?php
namespace Aheadworks\Blog\Helper;

use Aheadworks\Blog\Helper\Config;

/**
 * Disqus helper
 * @package Aheadworks\Blog\Helper
 */
class Disqus extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Aheadworks\Blog\Model\Api\Disqus
     */
    protected $disqusApi;

    /**
     * @var array|null
     */
    protected $configForumCodes = null;

    /**
     * @var array
     */
    protected $commentsData = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Config $configHelper
     * @param \Aheadworks\Blog\Model\Api\Disqus $disqusApi
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Aheadworks\Blog\Model\Api\Disqus $disqusApi
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
        $this->disqusApi = $disqusApi;
    }

    /**
     * Retrieves Disqus admin url
     *
     * @return string
     */
    public function getAdminUrl()
    {
        $forumCodePart = $this->getForumCode() ? $this->getForumCode() . '.' : '';
        return "https://" . $forumCodePart . "disqus.com/admin/moderate";
    }

    /**
     * Retrieves number of published comments
     *
     * @param  mixed $id
     * @return int
     */
    public function getPublishedCommentsNum($id)
    {
        if (!isset($this->commentsData[$id])) {
            $this->commentsData[$id] = $this->getCommentsData($id);
        }
        return $this->commentsData[$id]['published'];
    }

    /**
     * Retrieves number of new comments
     *
     * @param  mixed $id
     * @return int
     */
    public function getNewCommentsNum($id)
    {
        if (!isset($this->commentsData[$id])) {
            $this->commentsData[$id] = $this->getCommentsData($id);
        }
        return $this->commentsData[$id]['new'];
    }

    /**
     * Retrieves forum code option
     *
     * @param  int|null $storeId
     * @param  int|null $websiteId
     * @return mixed
     */
    protected function getForumCode($storeId = null, $websiteId = null)
    {
        return $this->configHelper->getValue(
            Config::XML_GENERAL_DISQUS_FORUM_CODE,
            $storeId,
            $websiteId
        );
    }

    /**
     * Retrieves all configured forum codes
     *
     * @return array|null
     */
    protected function getConfigForumCodes()
    {
        if ($this->configForumCodes === null) {
            $this->configForumCodes = [];
            foreach ($this->storeManager->getWebsites() as $website) {
                $forumCode = $this->getForumCode(null, $website->getId());
                if (!in_array($forumCode, $this->configForumCodes)) {
                    $this->configForumCodes[] = $forumCode;
                }
            }
        }
        return $this->configForumCodes;
    }

    /**
     * Retrieves comments data using Disqus API
     *
     * @param  mixed $id
     * @return array
     */
    protected function getCommentsData($id)
    {
        $published = 0;
        $new = 0;
        foreach ($this->getConfigForumCodes() as $forumCode) {
            $commentsData = $this->disqusApi->sendRequest(
                'threads/listPosts',
                [
                    'forum' => $forumCode,
                    'thread:ident' => $id,
                    'related' => ['thread'],
                    'include' => ['unapproved', 'approved']
                ]
            );
            if (is_array($commentsData)) {
                foreach ($commentsData as $commentData) {
                    if ($commentData['isApproved']) {
                        $published++;
                    } else {
                        $new++;
                    }
                }
            }
        }
        return ['published' => $published, 'new' => $new];
    }
}

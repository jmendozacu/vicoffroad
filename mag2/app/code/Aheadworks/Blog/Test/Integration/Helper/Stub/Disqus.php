<?php
namespace Aheadworks\Blog\Test\Integration\Helper\Stub;

/**
 * Class Disqus
 * @package Aheadworks\Blog\Test\Integration\Helper\Stub
 */
class Disqus extends \Aheadworks\Blog\Helper\Disqus
{
    /**
     * @var array|null
     */
    protected $commentsData = null;

    /**
     * @param array $commentsData
     * @return void
     */
    public function setCommentsData(array $commentsData)
    {
        $this->commentsData = $commentsData;
    }

    /**
     * @param  mixed $id
     * @return int
     */
    public function getPublishedCommentsNum($id)
    {
        if ($this->commentsData !== null && isset($this->commentsData[$id]['published'])) {
            return $this->commentsData[$id]['published'];
        }
        return 0;
    }

    /**
     * @param  mixed $id
     * @return int
     */
    public function getNewCommentsNum($id)
    {
        if ($this->commentsData !== null && isset($this->commentsData[$id]['new'])) {
            return $this->commentsData[$id]['new'];
        }
        return 0;
    }
}

<?php
namespace Aheadworks\Blog\Model;

/**
 * Tag model
 *
 * @method string getName()
 * @method int getCount()
 *
 * @method Tag setCount(int $count)
 * @method Tag setPosts(array $posts)
 *
 * @package Aheadworks\Blog\Model
 */
class Tag extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aheadworks\Blog\Model\ResourceModel\Tag');
    }

    /**
     * Load tag instance by name
     *
     * @param string $name
     * @return $this
     */
    public function loadByName($name)
    {
        return $this->load($name, 'name');
    }

    /**
     * Add post ID
     *
     * @param int $postId
     * @return $this
     */
    public function addPost($postId)
    {
        $posts = $this->getPosts();
        if (!in_array($postId, $posts)) {
            $posts[] = $postId;
        }
        $this->setPosts($posts);
        return $this;
    }

    /**
     * Remove post ID
     *
     * @param int $postId
     * @return $this
     */
    public function removePost($postId)
    {
        $posts = $this->getPosts();
        $key = array_search($postId, $posts);
        if ($key !== false) {
            unset($posts[$key]);
        }
        $this->setPosts($posts);
        return $this;
    }

    /**
     * @return array
     */
    public function getPosts()
    {
        $posts = $this->getData('posts');
        return is_array($posts) ? $posts : [];
    }
}

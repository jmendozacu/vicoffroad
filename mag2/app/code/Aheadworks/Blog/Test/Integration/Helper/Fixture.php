<?php
namespace Aheadworks\Blog\Test\Integration\Helper;

/**
 * Class Fixture
 * @package Aheadworks\Blog\Test\Integration\Helper
 */
class Fixture
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
     * @param string $className
     * @param $modelId
     * @param mixed $field
     * @return mixed
     */
    private function loadInstance($className, $modelId, $field = null)
    {
        return $this->getObjectManager()->create($className)
            ->load($modelId, $field);
    }

    /**
     * Retrieves fixture store Ids
     * Use Magento/Store/_files/core_fixturestore.php, Magento/Store/_files/core_second_third_fixturestore.php fixtures
     *
     * @param array $codes
     * @return array
     */
    public function getFixtureStoreIds($codes = [])
    {
        $result = [];
        foreach ($codes as $code) {
            $result[] = $this->loadInstance('Magento\Store\Model\Store', $code)->getId();
        }
        return $result;
    }

    /**
     * Retrieves fixture category Ids
     * Use Aheadworks/Blog/Test/Integration/_files/categories.php fixture
     *
     * @param array $urlKeys
     * @return array
     */
    public function getFixtureCategoryIds($urlKeys = [])
    {
        $result = [];
        foreach ($urlKeys as $urlKey) {
            $result[] = $this->loadInstance('Aheadworks\Blog\Model\Category', $urlKey, 'url_key')->getId();
        }
        return $result;
    }

    /**
     * Retrieves fixture post Ids
     * Use Aheadworks/Blog/Test/Integration/_files/posts.php fixture
     *
     * @param array $urlKeys
     * @return array
     */
    public function getFixturePostIds($urlKeys = [])
    {
        $result = [];
        foreach ($urlKeys as $urlKey) {
            $result[] = $this->loadInstance('Aheadworks\Blog\Model\Post', $urlKey, 'url_key')->getId();
        }
        return $result;
    }

    /**
     * Retrieves fixture tag Ids
     * Use Aheadworks/Blog/Test/Integration/_files/tags.php fixture
     *
     * @param array $names
     * @return array
     */
    public function getFixtureTagIds($names = [])
    {
        $result = [];
        foreach ($names as $name) {
            $result[] = $this->loadInstance('Aheadworks\Blog\Model\Tag', $name, 'name')->getId();
        }
        return $result;
    }
}

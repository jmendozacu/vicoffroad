<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyPage\Model\Customizer\Category;

/**
 * Class Page
 *
 * @author Artem Brunevski
 */

use Amasty\Shopby\Model\Customizer\Category\CustomizerInterface;
use \Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Config as CatalogConfig;
use Amasty\ShopbyPage\Api\PageRepositoryInterface;
use Amasty\ShopbyPage\Api\Data\PageInterface;
use Amasty\ShopbyPage\Model\Page as PageEntity;


class Page implements CustomizerInterface
{
    /** @var \Magento\Framework\App\RequestInterface  */
    protected $request;

    /** @var CatalogConfig  */
    protected $catalogConfig;

    /** @var PageRepositoryInterface  */
    protected $pageRepository;

    /**
     * @param Context $context
     * @param PageRepositoryInterface $pageRepository
     * @param CatalogConfig $catalogConfig
     */
    public function __construct(
        Context $context,
        PageRepositoryInterface $pageRepository,
        CatalogConfig $catalogConfig
    ){
        $this->request = $context->getRequest();
        $this->pageRepository = $pageRepository;
        $this->catalogConfig = $catalogConfig;
    }

    public function prepareData(\Magento\Catalog\Model\Category $category)
    {
        $searchResults = $this->pageRepository->getList($category);

        if ($searchResults->getTotalCount() > 0)
        {
            foreach($searchResults->getItems() as $pageData)
            {
                if ($this->_matchCurrentFilters($pageData)){
                    $this->_modifyCategory($pageData, $category);
                    break;
                }
            }
        }
    }

    /**
     * Compare page filters with selected filters
     * @param PageInterface $pageData
     * @return bool
     */
    protected function _matchCurrentFilters(PageInterface $pageData)
    {
        $match = true;

        $conditions = $pageData->getConditions();

        foreach($conditions as $condition){
            $attribute = $this->catalogConfig->getAttribute(Product::ENTITY, $condition['filter']);
            if ($attribute->getId()){
                $paramValue = $this->request->getParam($attribute->getAttributeCode());

                //compare with array for multiselect attributes
                if ($attribute->getFrontendInput() === 'multiselect') {
                    $paramValue = explode(',', $paramValue);

                    if (!is_array($condition['value'])){
                        $match = false;
                        break;
                    }

                    if (array_diff($condition['value'], $paramValue)){
                        $match = false;
                        break;
                    }

                } else {
                    if ($paramValue !== $condition['value']){
                        $match = false;
                        break;
                    }
                }
            }
        }
        return $match;
    }

    /**
     * @param PageEntity $page
     * @param $pageValue
     * @param $categoryValue
     * @param null $delimiter
     * @return string
     */
    protected function _getModifiedCategoryData(
        PageInterface $page,
        $pageValue,
        $categoryValue,
        $delimiter = null
    ){
        if ($delimiter !== null && $page->getPosition() !== PageEntity::POSITION_REPLACE){
            //if has a delimiter, place at the start or end
            $categoryValueArr =
                $categoryValue !== null  &&
                $categoryValue !== '' ?
                    explode($delimiter, $categoryValue) :
                    [];

            if ($page->getPosition() === PageEntity::POSITION_AFTER){
                $categoryValueArr[] = $pageValue;
            } else if ($page->getPosition() === PageEntity::POSITION_BEFORE){
                $categoryValueArr = array_merge([$pageValue], $categoryValueArr);
            }
            $categoryValue = implode($delimiter, $categoryValueArr);
        } else {
            $categoryValue = $pageValue;
        }
        return $categoryValue;
    }

    /**
     * @param PageInterface $page
     * @param CategoryInterface $category
     * @param $pageKey
     * @param $categoryKey
     * @param null $delimiter
     */
    protected function _modifyCategoryData(
        PageInterface $page,
        CategoryInterface $category,
        $pageKey,
        $categoryKey,
        $delimiter = null
    ){
        $categoryValue = $category->getData($categoryKey);
        $pageValue = $page->getData($pageKey);
        $category->setData($categoryKey, $this->_getModifiedCategoryData($page, $pageValue, $categoryValue, $delimiter));
    }

    /**
     * @param PageInterface $page
     * @param CategoryInterface $category
     */
    protected function _modifyCategory(PageInterface $page, CategoryInterface $category)
    {
        $categoryName = $this->_getModifiedCategoryData($page, $page->getTitle(), $category->getName());
        $category->setName($categoryName);

        $this->_modifyCategoryData($page, $category, 'description', 'description');
        $this->_modifyCategoryData($page, $category, 'meta_title', 'meta_title', ' ');
        $this->_modifyCategoryData($page, $category, 'meta_description', 'meta_description', ' ');
        $this->_modifyCategoryData($page, $category, 'meta_keywords', 'meta_keywords', ',');
        $this->_modifyCategoryData($page, $category, 'top_block_id', 'landing_page');
        $this->_modifyCategoryData($page, $category, 'url', 'url');

        if ($page->getData('top_block_id')) {
            $category->setData(PageEntity::CATEGORY_FORCE_MIXED_MODE, 1);
        }

        if ($page->getData('url')) {
            $category->setData(PageEntity::CATEGORY_FORCE_USE_CANONICAL, 1);
        }
    }
}
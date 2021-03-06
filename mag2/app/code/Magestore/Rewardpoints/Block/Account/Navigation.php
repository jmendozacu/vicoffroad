<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Navigation
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Account;

class Navigation extends \Magento\Framework\View\Element\Template
{
    protected $_links = array();
    protected $_activeLink = false;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Policy $globalConfig)
    {

        parent::__construct($context, []);
       
    }


    /**
     * Add link to navigation
     *
     * @param string $name
     * @param string $path
     * @param string $label
     * @param boolean $enable
     * @param int $order
     * @return Magestore_RewardPoints_Block_Account_Navigation
     */
    public function addLink($name, $path, $label, $enable = true, $order = 0)
    {
        while (isset($this->_links[$order])) {
            $order++;
        }

        $this->_links[$order] = new \Magento\Framework\DataObject(array(
            'name'  => $name,
            'path'  => $path,
            'label' => $label,
            'enable'    => $enable,
            'order'     => $order,
            'url'   => $this->getUrl($path)
        ));

        return $this;
    }

    /**
     * get Sorted links (by order)
     *
     * @return array
     */
    public function getLinks()
    {
        ksort($this->_links);
        return $this->_links;
    }

    /**
     * Set active link on navigation
     *
     * @param string $path
     * @return Magestore_RewardPoints_Block_Account_Navigation
     */
    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }

    /**
     * Check activate link
     *
     * @param string link
     * @return boolean
     */
    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->getRequest()->getActionName('/');
        }
        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        }
        return false;
    }

    /**
     * Repare complete path
     *
     * @param string $path
     * @return string
     */
    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
            case 2:
                $path .= '/index';
        }
        return $path;
    }
}

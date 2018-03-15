<?php
namespace Aheadworks\Blog\Model\Config\Backend\Blog;

/**
 * Route to blog backend model
 * @package Aheadworks\Blog\Model\Config\Backend\Blog
 */
class Route extends \Magento\Framework\App\Config\Value
{
    /**
     * Filter value before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $this->setValue(trim($this->getValue(), ' \t\n\r\0\x0B/'));
        return $this;
    }
}

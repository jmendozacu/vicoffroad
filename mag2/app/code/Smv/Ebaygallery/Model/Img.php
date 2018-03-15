<?php
namespace Smv\Ebaygallery\Model;

class Img extends \Magento\Framework\Model\AbstractModel
{
    protected $_objectManager;

    protected $_coreResource;

    /**---Functions---*/
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \Smv\Ebaygallery\Model\Resource\Img $resource,
        \Smv\Ebaygallery\Model\Resource\Img\Collection $resourceCollection
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    public function _construct()
    {
        $this->_init('Smv\Ebaygallery\Model\Resource\Img');
    }
    
}

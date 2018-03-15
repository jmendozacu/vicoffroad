<?php

namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

class PostDataProcessor
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\View\Model\Layout\Update\ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function filter($data)
    {
        $inputFilter = new \Zend_Filter_Input(
            [],
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();
        return $data;
    }
    
     protected function _isAllowed()
    {
        return true;
    }
}

<?php
namespace Smv\Ebaygallery\Model;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Status implements OptionSourceInterface
{
    
    /**
     * @var \Magento\Cms\Model\
     */
    protected $photogallery;

    /**
     * Constructor
     *
     * @param \Magento\Cms\Model\ $photogallery
     */
    public function __construct(\Smv\Ebaygallery\Model\Photogallery  $photogallery)
    {
        $this->photogallery = $photogallery;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->photogallery->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}

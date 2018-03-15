<?php
namespace Aheadworks\Blog\Model\Source\Post\SharingButtons;

/**
 * Display sharing buttons at source model
 * @package Aheadworks\Blog\Model\Source\Poast
 */
class DisplayAt implements \Magento\Framework\Option\ArrayInterface
{
    const POST  = 1;
    const POST_LIST = 2;

    const POST_LABEL = 'Post';
    const POST_LIST_LABEL = 'List of Posts';


    /**
     * @var null|array
     */
    protected $optionArray = null;

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            self::POST => __(self::POST_LABEL),
            self::POST_LIST => __(self::POST_LIST_LABEL)
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->optionArray === null) {
            $this->optionArray = [];
            foreach ($this->getOptions() as $value => $label) {
                $this->optionArray[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->optionArray;
    }

    /**
     * @param int $value
     * @return null|\Magento\Framework\Phrase
     */
    public function getOptionLabelByValue($value)
    {
        $options = $this->getOptions();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return null;
    }
}

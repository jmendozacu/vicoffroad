<?php
namespace Aheadworks\Blog\Model\Source\Post;

/**
 * Post Status source model
 * @package Aheadworks\Blog\Model\Source\Post
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    //statuses to store in DB
    const DRAFT = 'draft';
    const PUBLICATION = 'publication';

    // statuses to use only in post grid and in post form
    const PUBLICATION_PUBLISHED = 'published';
    const PUBLICATION_SCHEDULED = 'scheduled';

    const DRAFT_LABEL = 'Draft';
    const SCHEDULED_LABEL = 'Scheduled';
    const PUBLISHED_LABEL = 'Published';

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
            self::DRAFT => __(self::DRAFT_LABEL),
            self::PUBLICATION_SCHEDULED => __(self::SCHEDULED_LABEL),
            self::PUBLICATION_PUBLISHED => __(self::PUBLISHED_LABEL)
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

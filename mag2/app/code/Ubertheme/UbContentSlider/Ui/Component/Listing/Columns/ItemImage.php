<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Ubertheme\UbContentSlider\Helper\Image as ImageHelper;

class ItemImage extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'image';

    const ALT_FIELD = 'title';
    
    /**
     * @param ContextInterface $context
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    protected $_storeManager;

    /**
     * @param ContextInterface $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $baseMediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            foreach ($dataSource['data']['items'] as & $item) {
                $itemObj = new \Magento\Framework\DataObject($item);
                if ($itemObj->getData('content_type') == 'image') {
                    $item[$fieldName . '_src'] = $baseMediaUrl.$itemObj->getImage();
                    $item[$fieldName . '_orig_src'] =  $baseMediaUrl.$itemObj->getImage();
                } elseif ($itemObj->getData('content_type') == 'youtube_video') {
                    $item[$fieldName . '_src'] = ImageHelper::getYoutubeThumb($itemObj->getData('video_id'));
                    $item[$fieldName . '_orig_src'] =  ImageHelper::getYoutubeThumb($itemObj->getData('video_id'), '0');
                } elseif ($itemObj->getData('content_type') == 'vimeo_video') {
                    $item[$fieldName . '_src'] = ImageHelper::getVimeoThumb($itemObj->getData('video_id'));
                    $item[$fieldName . '_orig_src'] =  ImageHelper::getVimeoThumb($itemObj->getData('video_id'), 'thumbnail_large');
                }
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: $itemObj->getTitle();
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'ubcontentslider/item/edit',
                    ['item_id' => $itemObj->getItemId(), 'store' => $this->context->getRequestParam('store')]
                );
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
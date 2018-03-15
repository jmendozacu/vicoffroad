<?php
namespace Smv\Ebaygallery\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
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
      
        $media_url = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                );
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if($item['filethumb']==NULL)
                {
                    $item['filethumb'] = 'photogallery/video_icon_full.jpg';
                }
                $item[$fieldName . '_src'] = $media_url.$item['filethumb'];
                $item[$fieldName . '_alt'] = $item['title'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'photogalleryadmin/photogallery/edit',
                    ['id' => $item['photogallery_id'], 'store' => $this->context->getRequestParam('store')]
                );
               
                $item[$fieldName . '_orig_src'] = $media_url.$item['filethumb'];
            }
        }
        return $dataSource;
    }

}

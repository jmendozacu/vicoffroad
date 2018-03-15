<?php
namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

use Magento\Backend\App\Action\Context;
use Smv\Ebaygallery\Model\Photogallery as Photogallery;
use Magento\Framework\Controller\Result\JsonFactory;


class InlineEdit extends \Magento\Backend\App\Action
{
    /** @var PostDataProcessor */
    protected $dataProcessor;

    /** @var Photogallery  */
    protected $photogallery;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param PostDataProcessor $dataProcessor
     * @param Photogallery $photogallery
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Photogallery $photogallery,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->photogallery = $photogallery;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $photogalleryId) {
            /** @var \Magento\Photogallery\Model\Photogallery $photogallery */
            $photogallery = $this->photogallery->load($photogalleryId);
            try {
                $photogalleryData = $this->dataProcessor->filter($postItems[$photogalleryId]);
                $photogallery->setData($photogalleryData);
                $photogallery->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithPhotogalleryId($photogallery, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPhotogalleryId($photogallery, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPhotogalleryId(
                    $photogallery,
                    __('Something went wrong while saving the photogallery.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add photogallery title to error message
     *
     * @param PhotogalleryInterface $photogallery
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithPhotogalleryId(Photogallery $photogallery, $errorText)
    {
        return '[Photogallery ID: ' . $photogallery->getPhotogalleryId() . '] ' . $errorText;
    }
     protected function _isAllowed()
    {
        return true;
    }
}

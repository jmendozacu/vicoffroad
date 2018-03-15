<?php
namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

use \Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Smv_Ebaygallery::save');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Smv_Ebaygallery::manage_items')
            ->addBreadcrumb(__('Photogallery'), __('Photogallery'))
            ->addBreadcrumb(__('Manage Photogallery'), __('Manage Photogallery'));
        return $resultPage;
    }

    /**
     * Edit Photogallery item
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $resultPage = $this->resultPageFactory->create();
		$id     = $this->getRequest()->getParam('id');
		$model  = $this->_objectManager->create('Smv\Ebaygallery\Model\Photogallery')->load($id);

		$photogallery = $this->_objectManager->create('Smv\Ebaygallery\Model\ImgFactory');
		$collection = $photogallery->create()->getCollection()->addFieldToFilter('photogallery_id',$id);


		if ($model->getId() || $id == 0) {
			$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			$this->_objectManager->get('Magento\Framework\Registry')->register('photogallery_data', $model);
			$this->_objectManager->get('Magento\Framework\Registry')->register('photogallery_img', $collection);

			
			$resultPage->addBreadcrumb(
			$id ? __('Edit Photogallery') : __('New Photogallery'),
			$id ? __('Edit Photogallery') : __('New Photogallery')
			);
			$resultPage->getConfig()->getTitle()->prepend(__('Photo Gallery'));
			$resultPage->getConfig()->getTitle()
			->prepend($model->getPhotogalleryId() ? $model->getGalName() : __('New Photo Gallery'));
			
			return $resultPage;
		} else {

			$this->messageManager->addError(__('File does not exist'));
			$resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
		}
    }
}
/*
class Edit extends \Smv\Ebaygallery\Controller\Adminhtml\Photogallery
{
    
    public function execute()
    {
		$resultPage = $this->resultPageFactory->create();
		$id     = $this->getRequest()->getParam('id');
		$model  = $this->_objectManager->create('Smv\Ebaygallery\Model\Photogallery')->load($id);

		$photogallery = $this->_objectManager->create('Smv\Ebaygallery\Model\ImgFactory');
		$collection = $photogallery->create()->getCollection()->addFieldToFilter('photogallery_id',$id);


		if ($model->getId() || $id == 0) {
			$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			$this->_objectManager->get('Magento\Framework\Registry')->register('photogallery_data', $model);
			$this->_objectManager->get('Magento\Framework\Registry')->register('photogallery_img', $collection);

			
			$resultPage->addBreadcrumb(
			$id ? __('Edit Photogallery') : __('New Photogallery'),
			$id ? __('Edit Photogallery') : __('New Photogallery')
			);
			$resultPage->getConfig()->getTitle()->prepend(__('Photogallery Gallery'));
			$resultPage->getConfig()->getTitle()
			->prepend($model->getPhotogalleryId() ? $model->getGalName() : __('New Photogallery'));
			
			return $resultPage;
		} else {
			$this->messageManager->addError(__('File does not exist'));
			//$this->_redirect('');
		}
	
    }
    
}*/

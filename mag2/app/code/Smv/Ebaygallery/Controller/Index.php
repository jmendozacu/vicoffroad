<?php
namespace Smv\Ebaygallery\Controller ;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

abstract class Index extends \Magento\Framework\App\Action\Action
{
    
    /** @var Session */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
        
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function dispatch(RequestInterface $request) {
        
        
        $enableModule = $this->_objectManager->create('Smv\Ebaygallery\Helper\Data')->enableModule();
        if (!$enableModule) {

            //$this->_objectManager->get('Magento\Customer\Model\Session')->addError(__('Please login to download the attachment.'));
            
                $this->_actionFlag->set('', 'no-dispatch', true);
                $this->messageManager->addError(__('Sorry this feature is not available currently'));
            

        }
        $result = parent::dispatch($request);
        return $result;
    }
}

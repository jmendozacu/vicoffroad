<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */
namespace Amasty\Cart\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Checkout\Helper\Data as HelperData;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Url\Helper\Data as UrlHelper;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Amasty\Cart\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $_view;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry,
        \Amasty\Cart\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\View\LayoutInterface $layout,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        HelperData $helperData,
        UrlHelper $urlHelper
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );

        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_productHelper = $productHelper;
        $this->layout = $layout;
        $this->helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        $this->_view = $context->getView();
        $this->_coreRegistry = $coreRegistry;
        $this->urlHelper = $urlHelper;
        $this->catalogSession = $catalogSession;
        $this->categoryFactory = $categoryFactory;
    }

    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $message = __('We can\'t add this item to your shopping cart right now. Please reload the page.');
            return $this->addToCartResponse($message, 0);
        }

        $params = $this->getRequest()->getParams();
        $product = $this->_initProduct();
        /**
         * Check product availability
         */
        if (!$product) {
            $message = __('We can\'t add this item to your shopping cart right now.');
            return $this->addToCartResponse($message, 0);
        }

        try {
            $needShowOptions = $product->getTypeInstance()->hasRequiredOptions($product)
                || ($this->_helper->getModuleConfig('general/display_options') && $product->getHasCustomOptions());


            if(in_array($product->getTypeId(), ['configurable', 'grouped', 'bundle'])
                && !(array_key_exists('super_attribute', $params)
                   || array_key_exists('super_group', $params)
                   || array_key_exists('bundle_option', $params)
                )
                ||
                ($needShowOptions
                    && !array_key_exists('options', $params)
                    && !in_array($product->getTypeId(), ['configurable', 'grouped', 'bundle'])
                )

            ) {
                return $this->showOptionsResponse($product);
            }

            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $related = $this->getRequest()->getParam('related_product');

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = '<p>' . __(
                        'You added %1 to your shopping cart.',
                        '<a href="' . $product->getProductUrl() .'" title=" . ' .
                        $product->getName() . '">' .
                            $product->getName() .
                        '</a>'
                    ) . '</p>';

                    $message = $this->getProductAddedMessage($product, $message);
                    return $this->addToCartResponse($message, 1);
                }
                else{
                    $message = '';
                    $errors = $this->cart->getQuote()->getErrors();
                    foreach ($errors as $error){
                        $message .= $error->getText();
                    }
                    return $this->addToCartResponse($message, 0);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->addToCartResponse(
                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                , 0
            );

        } catch (\Exception $e) {
            $message = __('We can\'t add this item to your shopping cart right now.');
            $message .= $e->getMessage();
            return $this->addToCartResponse($message, 0);
        }
    }

    //creating options popup
    protected function showOptionsResponse(\Magento\Catalog\Model\Product $product) {
        $this->_productHelper->initProduct($product->getEntityId(), $this);
        $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);
        $page->addHandle('catalog_product_view');

        $type = $product->getTypeId();
        $page->addHandle('catalog_product_view_type_' . $type);

        $block = $page->getLayout()->getBlock('product.info');
        if (!$block) {
            $block = $page->getLayout()->createBlock(
                'Magento\Catalog\Block\Product\View',
                'product.info',
                [ 'data' => [] ]
            );
        }

        $block->setProduct($product);
        $html = $block->toHtml();

        /* replace uenc for correct redirect*/
        $currentUenc = $this->urlHelper->getEncodedUrl();
        $refererUrl = $product->getProductUrl();
        $newUenc = $this->urlHelper->getEncodedUrl($refererUrl);
        $html = str_replace($currentUenc, $newUenc, $html);

        $result = array(
            'title'     =>  __('Set options'),
            'message'   =>  $html,
            'b1_name'   =>  __('Add to cart'),
            'b2_name'   =>  __('Cancel'),
            'b1_action' =>  'self.submitFormInPopup();',
            'b2_action' =>  'self.confirmHide();',
            'align' =>  'self.confirmHide();' ,
            'is_add_to_cart' =>  '0'
        );
        $result = $this->replaceJs($result);
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }

    protected function getProductAddedMessage(\Magento\Catalog\Model\Product $product, $message) {
        if ($this->_helper->displayProduct()) {
            $block = $this->layout->getBlock('amasty.cart.product');
            if (!$block) {
                $block = $this->layout->createBlock(
                    'Amasty\Cart\Block\Product',
                    'amasty.cart.product',
                    [ 'data' => [] ]
                );
            }

            $block->setProduct($product);
            $message = $block->toHtml();
        }
        //display count cart item
        if ($this->_helper->displayCount()) {
            $summary = $this->cart->getSummaryQty();
            $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
            if ($summary == 1) {
                $message .=
                    "<p id='amcart-count'>" .
                        __('There is') .
                        ' <a href="' . $cartUrl . '" id="am-a-count">1' .
                            __(' item') .
                        '</a>'.
                        __(' in your cart.') .
                    "</p>";
            }
            else{
                $message .=
                    "<p id='amcart-count'>".
                        __('There are') .
                        ' <a href="'. $cartUrl .'" id="am-a-count">'.
                            $summary.  __(' items') .
                        '</a> '.
                        __(' in your cart.') .
                    "</p>";
            }
        }
        //display summ price
        if ($this->_helper->displaySumm()) {
            $message .=
                '<p>' .
                    __('Cart Subtotal:') .
                    ' <span class="am_price">'.
                    $this->getSubtotalHtml() .
                '</span></p>';
        }

        //display related products
        if ($this->_helper->getModuleConfig('selling/related')) {
            //$this->_coreRegistry->register('product', $product);
            $this->_productHelper->initProduct($product->getEntityId(), $this);
            $relBlock = $this->layout->createBlock(
                'Amasty\Cart\Block\Product\Related',
                'amasty.cart.product_related',
                [ 'data' => [] ]
            );
            $relBlock->setProduct($product)->setTemplate("Amasty_Cart::product/list/items.phtml");
            $message .= $relBlock->toHtml();

            /* replace uenc for correct redirect*/
            $currentUenc = $this->urlHelper->getEncodedUrl();
            $refererUrl = $this->_request->getServer('HTTP_REFERER');
            $newUenc = $this->urlHelper->getEncodedUrl($refererUrl);
            $message = str_replace($currentUenc, $newUenc, $message);
        }

        return $message;
    }

    //creating finale popup
    protected function addToCartResponse($message, $status) {
        $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
        $checkouttUrl = $this->_helper->getUrl('checkout');
        $result = array(
            'title'     =>  __('Information'),
            'message'   =>  $message,
            'b1_name'   =>  __('View cart'),
            'b2_name'   =>  __('Continue Shopping'),
            'b3_name'   =>  __('Go To Checkout'),
            'b1_action' =>  'document.location = "' . $cartUrl . '";',
            'b2_action' =>  'self.confirmHide();',
            'b3_action' =>  'document.location = "' . $checkouttUrl . '";',
            'is_add_to_cart' =>  $status,
            'checkout'  => ''
        );

        if ($this->_helper->getModuleConfig('display/disp_checkout_button')) {
            $goto = __('Go to Checkout');
            $result['checkout'] =
                '<a class="checkout action primary"
                    title="' . $goto . '"
                    data-role="proceed-to-checkout"
                    type="button"
                    href="' . $this->_helper->getUrl('checkout') . '"
                    >
                        <span>' . $goto . '</span>
                </a>';
        }

        $isProductView = $this->getRequest()->getParam('product_page');
        if ($isProductView == 'true' && $this->_helper->getProductButton()) {
            $categoryId = $this->catalogSession->getLastVisitedCategoryId();
            if ($categoryId) {
                $category = $this->categoryFactory->create()->load($categoryId);
                if ($category) {
                    $result['b2_action'] =  'document.location = "'.
                        $category->getUrl()
                        .'";';
                }
            }

        }

        //add timer
        $time = $this->_helper->getTime();
        if (0 < $time) {
            $result['b2_name'] .= '(' . $time . ')';
        }
        $result = $this->replaceJs($result);

        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }

    //replace js in one place
    private function replaceJs($result)
    {
        $arrScript = array();
        $result['script'] = '';
        preg_match_all("@<script type=\"text/javascript\">(.*?)</script>@s",  $result['message'], $arrScript);
        $result['message'] = preg_replace("@<script type=\"text/javascript\">(.*?)</script>@s",  '', $result['message']);
        foreach($arrScript[1] as $script) {
            $result['script'] .= $script;
        }
        $result['script'] =  preg_replace("@var @s",  '', $result['script']);
        return $result;
    }

    protected function getSubtotalHtml()
    {
        $totals = $this->cart->getQuote()->getTotals();
        $subtotal = isset($totals['subtotal']) && $totals['subtotal'] instanceof Total
            ? $totals['subtotal']->getValue()
            : 0;
        return $this->helperData->formatPrice($subtotal);
    }
}

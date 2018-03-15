<?php

namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;

class Save extends \Magento\Backend\App\Action {
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\App\ResourceConnection $coreresource
    ) {

        parent::__construct($context);
        $this->_coreresource = $coreresource;
    }

    public function execute() {

        if ($data = $this->getRequest()->getPostValue()) {
            $dataGallery = $this->getRequest()->getPostValue();
            /* Gallery Images Array */
            $gallery = isset($data['gallery']) ? $data['gallery'] : [];
            /* Images Array */
            $_photos_info = isset($gallery['images']) ? $gallery['images'] : [];
            //echo '<pre>';print_r($_photos_info);exit;
            //Upload File 




            $model = $this->_objectManager->create('Smv\Ebaygallery\Model\Photogallery');
            $id = $this->getRequest()->getParam('photogallery_id');
            if ($id) {
                //echo $id; exit;
                $model->load($id);
            }

            $categoryidsString = '';
            $catIds = explode(",", $categoryidsString);
            $result = array_unique($catIds);
            $comma_separated = implode(",", $result);

            $data["category_ids"] = $comma_separated;

            $model->setData($data);

            if ($id) {
                $model->setId($id);
            }


            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(date('y-m-d h:i:s'))
                            ->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }

                $model->save();
                /* Attaching Uploaded Images With Gallery */


                $_conn_read = $this->_coreresource->getConnection('core_read');
                $_conn = $this->_coreresource->getConnection('core_write');
                $photogallery_images_table = $this->_coreresource->getTableName('photogallery_images');
                if (!empty($_photos_info)) {
                    foreach ($_photos_info as $_photo_info) {

                        //Do update if we have gallery id (menaing photo is already saved)
                        if ($_photo_info['photogallery_id'] != NULL) {

                            $data = array(
                                "img_name" => str_replace(".tmp", "", $_photo_info['file']),
                                "img_label" => $_photo_info['label'],
                                "img_description" => $_photo_info['description'],
                                "photogallery_id" => $_photo_info['photogallery_id'],
                                "img_order" => $_photo_info['position'],
                                "disabled" => $_photo_info['disabled'],
                            );

                            $where = array("img_id = " . (int) $_photo_info['value_id']);
                            $_conn->update($photogallery_images_table, $data, $where);

                            if (isset($_photo_info['removed']) and $_photo_info['removed'] == 1) {
                                $_conn->delete($photogallery_images_table, 'img_id = ' . (int) $_photo_info['value_id']);
                            }
                        } else {

                            $_lookup = $_conn_read->fetchAll("SELECT * FROM " . $photogallery_images_table . " WHERE img_name = ?", $_photo_info['file']);
                            //echo "here2"; exit;
                            if (empty($_lookup)) {

                                $_conn->insert($photogallery_images_table, array(
                                    'img_name' => str_replace(".tmp", "", $_photo_info['file']),
                                    'img_label' => $_photo_info['label'],
                                    'img_description' => $_photo_info['description'],
                                    'photogallery_id' => $model->getId(),
                                    'img_order' => $_photo_info['position'],
                                    "disabled" => $_photo_info['disabled'],
                                ));
                            }
                        }
                    }
                }
                switch ($dataGallery['show_in']){
                    case 1:
                        $this->saveBannerHtml($dataGallery);
                        break;
                    case 2:
                        $this->saveCategoryHtml($dataGallery);
                        break;
                    case 3:
                        $this->saveProductHtml($dataGallery);
                        break;
                    case 4:
                        $this->saveBrandHtml($dataGallery);
                        break;
                }

                /* End Images Section */
                $this->messageManager->addSuccess(__('Photogallery was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find Photogallery to save'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed() {
        return true;
    }
    
    public function saveBannerHtml($dataGallery) {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        $_imageCollection = $dataGallery['gallery']['images'];
        $html = '<article id="slider">';
        $htmlControl = '<div id=controls>';
        $htmlSlider = '<div id=active>';
        $i = 1;
        foreach ($_imageCollection as $item){
        $class = '';
            if($i == 1){
                $class = 'checked=""';
            }
            $html .= '<input id="slide'.$i.'" type="radio" name="slider" '.$class.'>';
            $htmlControl .='<label for=slide'.$i.'></label>';
            $htmlSlider .= '<label for=slide'.$i.'></label>';
            $i++;
            $class = '';
        }
        $htmlControl .= '</div>';
        $htmlSlider .= '</div>';
        
        $html .= '<div id="slides"><div id="overflow"><div class="inner">';
        foreach ($_imageCollection as $item){
            $html .= '<article>
                            <div class="info"><h3>'.$item['description'].'</h3>  <a href="'.$item['label'].'">Shop Now</a></div>
                            <a title="'.$item['description'].'" href="'.$item['label'].'">
                                <img src="' . $mediaUrl.'photogallery/images'.str_replace('.tmp', '', $item['file']) . '" alt = "'.$item['description'].'" ></a>
                    </article>';
        }
        $html .= '</div></div></div>'.$htmlControl.$htmlSlider.'</article>';
        
        $helper = $this->_objectManager->get('\Smv\Ebaygallery\Helper\Data');
        $eBayID = $helper->getConfig('photogallery/general/ebayID');
        $ebayStore = $helper->getConfig('photogallery/general/ebayStore');
        $userToken = $helper->getConfig('photogallery/general/token');
        if($dataGallery['gorder'] > 1){
            $this->setStoreCustomPage($eBayID, $ebayStore, $userToken, '<!--start-banner -->', '<!--end-banner -->', $html, $dataGallery['gorder']);
        }else{
            $this->setStoreHeader($eBayID, $ebayStore, $userToken, '<!--start-banner -->', '<!--end-banner -->', $html);
        }

    }
    
    public function saveCategoryHtml($dataGallery){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        $_imageCollection = $dataGallery['gallery']['images'];
        $i = 1;
        $html = '<ul>';
        if (count($_imageCollection)) {
            $i = 0;
            foreach ($_imageCollection as $item) {
                $html .= '<li>
								<a class="product-intro" title="' . $item['description'] . '" href="' . $item['label'] . '">'
                        . '                                     <img class="lazy-image" src="' . $mediaUrl.'photogallery/images'.str_replace('.tmp', '', $item['file']) . '"  /></a>
								<div class="category-name"><a title="' . $item['description'] . '" href="' . $item['label'] . '">' . $item['description'] . '</a></div>
                                                                    <div style="display: none;" class="category-action"><a title="Shop Now" href="' . $item['label'] . '">Shop Now</a></div>
							</li>';
            }
        }

        $html .= '</ul>';
        
        
        $helper = $this->_objectManager->get('\Smv\Ebaygallery\Helper\Data');
        $eBayID = $helper->getConfig('photogallery/general/ebayID');
        $ebayStore = $helper->getConfig('photogallery/general/ebayStore');
        $userToken = $helper->getConfig('photogallery/general/token');
        if($dataGallery['gorder'] > 1){
            $this->setStoreCustomPage($eBayID, $ebayStore, $userToken, '<!--start-category-->', '<!--end-category-->', $html, $dataGallery['gorder']);
        }
    }
    
    public function saveProductHtml($dataGallery){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        $_imageCollection = @$dataGallery['gallery']['images'];
        $i = 1;
        $html = '<article class=slider-product>';
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $html .= '<input '.$class.' type="radio" name="slider-product" id="product'.$i.'" />';
            $i++;
            $class = '';
        }
        $i = 1;
        $html .='<div class=slides><div class=overflow><div class=inner>';
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $htmlImage = '<img src="' . $mediaUrl.'photogallery/images'.str_replace('.tmp', '', $item['file']) . '" alt="'.$item['description'].'"/>';
            $html .= '<article>
                            <div>
                                    <div class="images-product">
                                    <a href="'.$item['label'].'" title="'.$item['description'].'">'.$htmlImage.'</a>
                                    </div>
                                    <div class="name-product"><a href="'.$item['label'].'" title="'.$item['description'].'">'.$item['description'].'</a></div>
                                    <div class="price-product"><span></span></div>
                                    <p><a href="#" title="">buy now</a></p>
                            </div>
                    </article>';
            $i++;
        }
        $html .= '</div></div></div>';
        $html .= '<div class="controls">';
        $i = 1;
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $html .= '<label for=product'.$i.'></label>';
            $i++;
        }
        
        $html .= '</div>';
        $html .= '<div class="active">';
        $i = 1;
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $html .= '<label for=product'.$i.'></label>';
            $i++;
        }
        
        $html .= '</div>';
        $html .= '</article>';
        
        $helper = $this->_objectManager->get('\Smv\Ebaygallery\Helper\Data');
        $eBayID = $helper->getConfig('photogallery/general/ebayID');
        $ebayStore = $helper->getConfig('photogallery/general/ebayStore');
        $userToken = $helper->getConfig('photogallery/general/token');
        if($dataGallery['gorder'] > 1){
            $this->setStoreCustomPage($eBayID, $ebayStore, $userToken, '<!--start-product-->', '<!--end-product-->', $html, $dataGallery['gorder']);
        }
    }
    
    public function saveBrandHtml($dataGallery){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        $_imageCollection = $dataGallery['gallery']['images'];
        $i = 1;
        $html = '<article  class="slider-product slider-brand">';
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $html .= '<input '.$class.' type="radio" name="brand" id="brand'.$i.'" />';
            $i++;
            $class = '';
        }
        $i = 1;
        $html .='<div class=slides><div class=overflow><div class=inner>';
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $htmlImage = '<img src="' . $mediaUrl.'photogallery/images'.str_replace('.tmp', '', $item['file']) . '" alt="'.$item['description'].'"/>';
            $html .= '<article>
                            <div>
                                    <a href="'.$item['label'].'" title="'.$item['description'].'">'.$htmlImage.'</a>
                            </div>
                    </article>';
            $i++;
        }
        $html .= '</div></div></div>';
        $html .= '<div class="controls">';
        $i = 1;
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $html .= '<label for=brand'.$i.'></label>';
            $i++;
        }
        
        $html .= '</div>';
        $html .= '<div class="active">';
        $i = 1;
        foreach ($_imageCollection as $item){
            if($i == 1){
                $class = 'checked';
            }
            $html .= '<label for=brand'.$i.'></label>';
            $i++;
        }
        
        $html .= '</div>';
        $html .= '</article>';
        
        $helper = $this->_objectManager->get('\Smv\Ebaygallery\Helper\Data');
        $eBayID = $helper->getConfig('photogallery/general/ebayID');
        $ebayStore = $helper->getConfig('photogallery/general/ebayStore');
        $userToken = $helper->getConfig('photogallery/general/token');
        if($dataGallery['gorder'] > 1){
            $this->setStoreCustomPage($eBayID, $ebayStore, $userToken, '<!--start-brand-->', '<!--end-brand-->', $html, $dataGallery['gorder']);
        }
    }

    public function setStoreCustomPage($eBayID, $ebayStore, $userToken, $startElement, $endElement, $htmlContent, $pageId){
        $html = $this->getStoreCustomPage($eBayID, $ebayStore, $userToken, $pageId);
        
        if($html == false){
            return false;
        }
        $html = $this->replace_content_inside_delimiters($startElement, $endElement, $htmlContent, $html);
        
        $SiteID = 15;
        
        $compatabilityLevel = 681;
        $helper = $this->_objectManager->get('\Smv\Ebaygallery\Helper\Data');
        $devID = $helper->getConfig('photogallery/general/devID');
        $appID = $helper->getConfig('photogallery/general/appID');
        $certID = $helper->getConfig('photogallery/general/certID');


        $serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox

        if ($eBayID != "" && $SiteID != "" && $userToken != "") {
            $errorsy = false;

            $verby = 'SetStoreCustomPage';
            //Level / amount of data for the call to return (default = 0)
            $detailLevel = 0;
            ///Build the request Xml string
            $requestXmlBodyy = '<?xml version="1.0" encoding="utf-8"?>
                                <SetStoreCustomPageRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                  <RequesterCredentials>
                                    <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                                  </RequesterCredentials>
                                  <CustomPage>
                                    <PageID>'.$pageId.'</PageID>
                                    <Content>
                                        <![CDATA['.$html.']]>
                                    </Content>
                                    <LeftNav>false</LeftNav>
                                    <Order>1</Order>
                                    <PreviewEnabled>false</PreviewEnabled>
                                  </CustomPage>
                                </SetStoreCustomPageRequest>';

            //Create a new eBay session with all details pulled in from included keys.php
            $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $SiteID, $verby);
            //send the request and get response
            $responseXmly = $session->sendHttpRequest($requestXmlBodyy);
            if (stristr($responseXmly, 'HTTP 404') || $responseXmly == '')
                return false;

            //Xml string is parsed and creates a DOM Document object
            $responseDocy = new \DOMDocument();
            $responseDocy->loadXML($responseXmly);


            //get any error nodes
            $errorsy = $responseDocy->getElementsByTagName('Errors');
            $errormsg = '';
            //if there are error nodes
            if ($errorsy->length > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    public function getStoreCustomPage($eBayID, $ebayStore, $userToken, $pageId){
        $Levels = 3;
        $SiteID = 15;

        $compatabilityLevel = 681;    // eBay API version
        $helper = $this->_objectManager->get('\Smv\Ebaygallery\Helper\Data');
        $devID = $helper->getConfig('photogallery/general/devID');
        $appID = $helper->getConfig('photogallery/general/appID');
        $certID = $helper->getConfig('photogallery/general/certID');


        $serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox

        if ($eBayID != "" && $SiteID != "" && $userToken != "") {
            $errorsy = false;

            $verby = 'GetStoreCustomPage';
            //Level / amount of data for the call to return (default = 0)
            $detailLevel = 0;
            ///Build the request Xml string
            $requestXmlBodyy = '<?xml version="1.0" encoding="utf-8" ?>
                                <GetStoreCustomPageRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                    <RequesterCredentials>
                                        <eBayAuthToken>' . $userToken . '</eBayAuthToken>
                                    </RequesterCredentials>
                                    <PageID>'.$pageId.'</PageID>
                                </GetStoreCustomPageRequest>';

            //Create a new eBay session with all details pulled in from included keys.php
            $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $SiteID, $verby);
            //send the request and get response
            $responseXmly = $session->sendHttpRequest($requestXmlBodyy);
            
            if (stristr($responseXmly, 'HTTP 404') || $responseXmly == '')
                return false;

            //Xml string is parsed and creates a DOM Document object
            $responseDocy = new \DOMDocument();
            $responseDocy->loadXML($responseXmly);


            //get any error nodes
            $errorsy = $responseDocy->getElementsByTagName('Errors');
            //if there are error nodes
            if ($errorsy->length > 0) {
                return false;
            } else {
                $errorsy = true;
                $simplexml_data = simplexml_load_string($responseXmly, NULL, LIBXML_NOCDATA);
                
                $doc = new \DOMDocument();
                $doc->formatOutput = TRUE;
                $doc->loadXML($simplexml_data->CustomPageArray->asXML());
                $xml = $doc->saveXML();
                
                $xml = simplexml_load_string($xml);
                $html = htmlentities($xml->CustomPage->Content);
                $html = (string)$xml->CustomPage->Content;
                return $html;
            }
        } else {
            return false;
        }
    }

        public function replace_content_inside_delimiters($start, $end, $new, $source) {
        return preg_replace('#('.preg_quote($start).')(.*?)('.preg_quote($end).')#si', '$1'.$new.'$3', $source);
    }

}



class eBaySession {

    private $requestToken;
    private $devID;
    private $appID;
    private $certID;
    private $serverUrl;
    private $compatLevel;
    private $siteID;
    private $verb;

    public function __construct($userRequestToken, $developerID, $applicationID, $certificateID, $serverUrl, $compatabilityLevel, $siteToUseID, $callName) {
        $this->requestToken = $userRequestToken;
        $this->devID = $developerID;
        $this->appID = $applicationID;
        $this->certID = $certificateID;
        $this->compatLevel = $compatabilityLevel;
        $this->siteID = $siteToUseID;
        $this->verb = $callName;
        $this->serverUrl = $serverUrl;
    }

    /** 	sendHttpRequest
      Sends a HTTP request to the server for this session
      Input:	$requestBody
      Output:	The HTTP Response as a String
     */
    public function sendHttpRequest($requestBody) {
        //build eBay headers using variables passed via constructor
        $headers = $this->buildEbayHeaders();

        //initialise a CURL session
        $connection = curl_init();
        //set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

        //stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

        //set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

        //set method as POST
        curl_setopt($connection, CURLOPT_POST, 1);

        //set the XML body of the request
        curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);

        //set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        //Send the Request
        $response = curl_exec($connection);

        //close the connection
        curl_close($connection);

        //return the response
        return $response;
    }

    /** 	buildEbayHeaders
      Generates an array of string to be used as the headers for the HTTP request to eBay
      Output:	String Array of Headers applicable for this call
     */
    private function buildEbayHeaders() {
        $headers = array(
            //Regulates versioning of the XML interface for the API
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,
            //set the keys
            'X-EBAY-API-DEV-NAME: ' . $this->devID,
            'X-EBAY-API-APP-NAME: ' . $this->appID,
            'X-EBAY-API-CERT-NAME: ' . $this->certID,
            //the name of the call we are requesting
            'X-EBAY-API-CALL-NAME: ' . $this->verb,
            //SiteID must also be set in the Request's XML
            //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
            //SiteID Indicates the eBay site to associate the call with
            'X-EBAY-API-SITEID: ' . $this->siteID,
        );

        return $headers;
    }

}
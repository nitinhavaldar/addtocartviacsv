<?php
// @Evry India Pvt Ltd
namespace Evry\Uploadorder\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Manager;
use Magento\Checkout\Model\Cart;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;

// Custom Namespaces
use Evry\Uploadorder\Block\Uploadorder;
use Evry\Uploadorder\Helper\Data;
use Evry\Uploadorder\Logger\Logger;
use Evry\Uploadorder\Model\Resource\Uploadorder\CollectionFactory;



class Index extends Action
{
    
   
    protected $_resultPageFactory;
    protected $date;
    protected $_customerSession;
    protected $_block;
    protected $_cart;
    protected $_pro;
    protected $_helper;
    protected $_logger;
    protected $_eventmanager;
    protected $_storeManager;
    protected $_responseFactory;
    protected $_jsonFactory;
    
    
    public function __construct(Context $context,
           PageFactory $resultPageFactory,DateTime 
           $date,Uploadorder $block, Cart $cart, Product $pro,Data $helper,
           Manager $eventmanager,Session $customerSession,Logger $logger,StoreManagerInterface $storeManager, CollectionFactory $responseFactory,JsonFactory $jsonFactory)
    {
        
        parent::__construct($context);
        $this->date = $date;
        $this->_storeManager = $storeManager;
        $this->_eventmanager = $eventmanager;
        $this->_cart = $cart;
        $this->_helper = $helper;
        $this->_block = $block;
        $this->_pro = $pro;
        $this->_jsonFactory = $jsonFactory;
        $this->_responseFactory = $responseFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_logger = $logger;
        
    }
 
    public function execute()
    {
        
       $post = $this->getRequest()->getPostValue();
       if($post) {
                
                $error = '';
                $count = 0;
                $target_dir = $this->_block->getDir();
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
                
                if(!in_array($_FILES['fileToUpload']['type'],$mimes)){
                    
                    $error = "Please Upload csv formatted file";
                } 

                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                     
                    $error = "Sorry, your file is too large.";
                }

                // Check if $error is set to 0 by an error
                if ($error != '') {
                    
                    $this->messageManager->addError(__($error));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setRefererOrBaseUrl();

                
                } 

                else 
                {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        
                        //Process the uploaded file
                        try {
                              
                              
                              //open the recent uploaded in file in read mode
                              $csv_file = fopen($target_file, 'r');
                              
                              //Array to store the sku & qty in terms of key & value pair..
                              $products = [];
                              $product_ids = [];
                              
                              // Skip the first row in the csv (Row header)
                              fgetcsv($csv_file);
                              
                              //Push the values into array
                              while (($data = fgetcsv($csv_file,1000,",")) !== FALSE) {
                                    $products[$data[0]] = $data[1];
                              }

                              //get the Product ID's by SKU & store it in product_ids array
                              foreach ($products as $sku => $qty) {
                                 $pid = $this->_block->getProductId($sku);
                                 $product_ids[$pid]=$qty;
                              }

                              //Process the product_ids array  
                              $msg="";
                              foreach ($product_ids as $proId => $quanty) {
                                        
                                        //Convert the string into Integer
                                        $pid = intval($proId); 
                                        $qty = intval($quanty);

                                        //Load the Product Object by Product ID
                                        $_product = $this->_pro->setStoreId($this->_storeManager->getStore()->getId())->load($pid);
                                        $params = array();
                                        $params['qty'] = $qty;
                                        
                                        //Pass the Prooject Object & Qty
                                        $this->_cart->addProduct($pid,$params);
                                        $msg .= $_product->getName(). " is successfully added into cart<br>";


                                         
                                }        
                                    
                                  //Stores the file details in databse
                                  $filename = $_FILES["fileToUpload"]["name"];

                                  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();       
                                  $question = $objectManager->create('Evry\Uploadorder\Model\Uploadorder');
                                  $question->setCustomerId($this->_customerSession->getCustomer()->getId());
                                  $question->setCustomerEmail($this->_customerSession->getCustomer()->getEmail());
                                  $question->setFileName($filename);
                                  $question->setFileSize($_FILES["fileToUpload"]["size"]);
                                  $question->setUploadedOn($this->date->gmtDate());
                                  $question->save(); 


                                  // Save the Products in Quote Table.
                                  $this->_cart->save();
                                  $this->messageManager->addSuccess(__($msg));
                                  $this->getResponse()->setRedirect($this->_helper->getBaseUrl().'checkout/cart/index'); 
                               
                            } catch(LocalizedException $e) {
                                    $this->_logger->info($e->getMessage());
                                    $this->messageManager->addError(__($e->getMessage()));
                                    $resultRedirect = $this->resultRedirectFactory->create();
                                    return $resultRedirect->setRefererOrBaseUrl();
                            }

                    } else {
                        $this->messageManager->addError(__("Sorry, there was an error uploading your file."));
                        $resultRedirect = $this->resultRedirectFactory->create();
                        return $resultRedirect->setRefererOrBaseUrl();
                       
                   }
                }

        }

      $resultPage = $this->_resultPageFactory->create();
      return $resultPage;
    }   
}

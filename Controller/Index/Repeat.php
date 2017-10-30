<?php
// @Evry India Pvt Ltd
namespace Evry\Uploadorder\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Store\Model\StoreManagerInterface;
use Evry\Uploadorder\Model\UploadorderFactory; 
use Evry\Uploadorder\Helper\Data;
use Evry\Uploadorder\Block\Uploadorder;
use Evry\Uploadorder\Controller\Index;

class Repeat extends Action
{
    
   
    protected $_requestInfo;
    protected $_helper;
    protected $_customerSession;
    protected $_jsonFactory;
    protected $_block;
    protected $_cart;
    protected $_pro;
    protected $_storeManager;

    
    
    public function __construct(Context $context,
                                Data $helper,
                                Session $customerSession,
                                UploadorderFactory $requestInfo,
                                JsonFactory $jsonFactory,
                                Uploadorder $block,
                                Cart $cart,
                                Product $pro,
                                StoreManagerInterface $storeManager)
    {
        
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_block = $block;
        $this->_customerSession = $customerSession;
        $this->_requestInfo = $requestInfo;
        $this->_jsonFactory = $jsonFactory;
        $this->_pro = $pro;
        $this->_cart = $cart;
        $this->_storeManager = $storeManager;

       
       


    }
 
    public function execute()
    {
        
      $actual_file = $this->getRequest()->getPost('filename');
      $resultJson = $this->_jsonFactory->create();
      
      try {
          
          $dir = $this->_block->getDir();
          $list_files = scandir($dir);
          
          if(in_array($actual_file, $list_files)) {

              //open the recent uploaded in file in read mode
              $target_file = $dir .$actual_file;
              $csv_file = fopen($target_file, 'r');
                              
              //Array to store the sku & qty in terms of key & value pair..
              $products = [];
              $product_ids = [];
                              
              //Skip the first row in the csv (Row header)
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

              // Save the Products in Quote Table.
              $this->_cart->save();
              $this->messageManager->addSuccess(__($msg));
              $response = ['responseJson' => 'success', 'message' => $msg];
          } else {
            $msg = "Unable to Serve your request. Because the file you are looking may be removed permanentaly from our database";
            $this->messageManager->addError(__($msg));
            $response = ['responseJson' => 'error', 'message' => $msg];
          }
          
      } catch (\Exception $e) {
          $this->messageManager->addError(__($e->getMessage()));
          $response = ['responseJson' => 'exception', 'message' => $e->getMessage()];
      }
        
        return $resultJson->setData($response);

    }   
}

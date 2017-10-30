<?php
// @Evry India Pvt Ltd
namespace Evry\Uploadorder\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Evry\Uploadorder\Model\UploadorderFactory; 
use Evry\Uploadorder\Helper\Data;

class Delete extends Action
{
    
   
    protected $_requestInfo;
    protected $_helper;
    protected $_customerSession;
    protected $_jsonFactory;
    
    public function __construct(Context $context,
                                Data $helper,
                                Session $customerSession,
                                UploadorderFactory $requestInfo,
                                JsonFactory $jsonFactory)
    {
        
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_customerSession = $customerSession;
        $this->_requestInfo = $requestInfo;
        $this->_jsonFactory = $jsonFactory;

    }
 
    public function execute()
    {
        
      $params = $this->getRequest()->getPost('id');
      $resultJson = $this->_jsonFactory->create();
      
      try {
          
          $cid = $this->_customerSession->getCustomer()->getId();
          $modelRequest =  $this->_requestInfo->create();
            
            //Check the Record ID against the customer ID
          $collection = $modelRequest->getCollection()
                    ->addFieldToFilter('entity_id',array('eq' => $params))
                    ->addFieldToFilter('customer_id',array('eq' => $cid));
            
            //if the record is found, then delete it accordingly
            if(count($collection) > 0) { 
              foreach ($collection as $item) {
                $name = $item->getFileName();
                $item->delete(); //performs the delete operation
              }
               $msg = "$name has been successfully deleted";
               
               $this->messageManager->addSuccess($msg);
               $response = ['responseJson' => 'success', 'message' => $msg];
            }

      } catch (\Exception $e) {
        $this->messageManager->addError($e->getMessage());
        $response = ['responseJson' => 'error', 'message' => $e->getMessage()];
      }

      return $resultJson->setData($response);

    }   
}

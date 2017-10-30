<?php
// @Evry India Pvt Ltd


?>
<?php
namespace Evry\Uploadorder\Block;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Customer\Model\Session;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Evry\Uploadorder\Model\UploadorderFactory; 
 
class Uploadorder extends \Magento\Framework\View\Element\Template
{
   
	protected $_customerSession;
	protected $_productFactory;
	protected $_directorylist;
	
	public function __construct(
    Context $context,
    ProductFactory $productFactory,
    Session $customerSession,
    UploadorderFactory $requestInfo,
    DirectoryList $directorylist,
      array $data = []
	)
	{        
	    
	    $this->_requestInfo = $requestInfo;
	    $this->_productFactory = $productFactory;
	    $this->_customerSession = $customerSession;
	    $this->_directorylist = $directorylist;
	    parent::__construct($context, $data);
	}

	public function getCartUrl() {
		return $this->getBaseUrl().'checkout/cart';
	}

	public function getFormAction() {
		return $this->getBaseUrl().'uploadorder';
	}

	public function getDir() {
		return $this->_directorylist->getRoot()."/uploads/";
	}

	public function getProductId($sku) {

		//Get Product Object
		$product = $this->_productFactory->create(); 
		$product->load($product->getIdBySku($sku));
		return $product->getEntityId();
		
	}

	public function getCustomerSession() {
    	return $this->_customerSession;
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->getAllCsvFiles()) {
            $toolbar = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'upload_csv_addtocart'
            )->setCollection(
                $this->getAllCsvFiles()
            );

            $this->setChild('toolbar', $toolbar);
        }
        return parent::_prepareLayout();
    }

	public function getAllCsvFiles() {  

		$id = $this->_customerSession->getCustomer()->getId();
		$customerRequest =  $this->_requestInfo->create();
		$collection = $customerRequest->getCollection()
					 ->addFieldToFilter('customer_id',array('eq' => $id)); 
		return $collection;
	}

	public function getDeleteUrl() {

		return $this->getBaseUrl().'uploadorder/index/delete';

	}

	public function getRepeatUrl() {

		return $this->getBaseUrl().'uploadorder/index/repeat';

	}

	public function getQuickOrderUrl() {

		return $this->getBaseUrl().'bulkenquiry/quickorder/index';

	}


	public function getCustomCartUrl() {

		return $this->getBaseUrl().'bulkenquiry/quickorder/addtocart';

	}


	


	




    
	


	

    

}
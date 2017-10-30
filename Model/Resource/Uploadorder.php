<?php
// @Evry India Pvt Ltd
namespace Evry\Uploadorder\Model\Resource;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Uploadorder extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('upload_order_file', 'entity_id');
    }

    
}
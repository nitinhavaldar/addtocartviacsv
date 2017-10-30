<?php
// @Evry India Pvt Ltd
namespace Evry\Uploadorder\Model\Resource\Uploadorder;
 
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected $_idFieldName = 'entity_id'; 

    protected function _construct()
    {
        $this->_init(
            'Evry\Uploadorder\Model\Uploadorder',
            'Evry\Uploadorder\Model\Resource\Uploadorder'
        );
    }
}
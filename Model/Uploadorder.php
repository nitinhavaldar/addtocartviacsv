<?php
// @Evry India Pvt Ltd 
namespace Evry\Uploadorder\Model;
 
use Magento\Framework\Model\AbstractModel;
 
class Uploadorder extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Evry\Uploadorder\Model\Resource\Uploadorder');
    }
}
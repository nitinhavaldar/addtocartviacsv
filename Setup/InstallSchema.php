<?php
// @Evry India Pvt Ltd
namespace Evry\Uploadorder\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
/**
* @codeCoverageIgnore
*/
class InstallSchema implements InstallSchemaInterface{
/**
* {@inheritdoc}
* @SuppressWarnings(PHPMD.ExcessiveMethodLength)
*/
public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
$installer = $setup;
$installer->startSetup();

/**
* Creating table jute_ecommerce
*/
$table = $installer->getConnection()->newTable(
$installer->getTable('upload_order_file')
)->addColumn('entity_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Entity Id')
->addColumn('customer_id',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,['nullable' => true],'Customer ID')
->addColumn('customer_email',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,['nullable' => true,'default' => null],'Customer Email')
->addColumn('file_name',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,['nullable' => true,'default' => null],'File Name')
->addColumn('file_size',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,['nullable' => true,'default' => null],'File Size')
->addColumn('uploaded_on',\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,null,['nullable' => false],'Uploaded On')
->setComment('Evry India');
$installer->getConnection()->createTable($table);
$installer->endSetup();





}
}
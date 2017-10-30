<?php
namespace Evry\Uploadorder\Logger;
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;
 
    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/customfile.log';
}
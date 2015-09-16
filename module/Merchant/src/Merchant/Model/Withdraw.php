<?php
namespace Merchant\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Model\DBModel;
use Merchant\Model\Report;

class Withdraw extends DBModel
{
    const WITHDRAW_TYPE_ALIPAY = 'alipay';
    const WITHDRAW_TYPE_BANK = 'bank';
    
    const WITHDRAW_PAY_STATUS_NO = 0;
    const WITHDRAW_PAY_STATUS_YES = 1;
    
    public $report;
    
    function __construct(ServiceLocatorInterface $sl, $id = null)
    {
        parent::__construct($sl, 'withdraw', $id);
        
        $this->report = new Report($sl,$this->merchant_id);
    }
}

?>
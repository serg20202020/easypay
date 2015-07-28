<?php
namespace Merchant\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Model\DBModel;

class Withdraw extends DBModel
{
    const WITHDRAW_TYPE_ALIPAY = 'alipay';
    const WITHDRAW_TYPE_BANK = 'bank';
    
    const WITHDRAW_PAY_STATUS_NO = 0;
    const WITHDRAW_PAY_STATUS_YES = 1;
    
    function __construct(ServiceLocatorInterface $sl, $id = null)
    {
        parent::__construct($sl, 'withdraw', $id);
    }
}

?>
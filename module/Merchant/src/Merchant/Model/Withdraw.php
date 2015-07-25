<?php
namespace Merchant\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Model\DBModel;

class Withdraw extends DBModel
{
    const WITHDRAW_TYPE_ALIPAY = 'alipay';
    const WITHDRAW_TYPE_BANK = 'bank';
    
    function __construct(ServiceLocatorInterface $sl, $id = null)
    {
        parent::__construct($sl, 'withdraw', $id);
    }
}

?>
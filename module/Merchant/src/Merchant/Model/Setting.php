<?php
namespace Merchant\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Model\DBModel;

class Setting extends DBModel
{
    function __construct(ServiceLocatorInterface $sl, $id = null)
    {
        parent::__construct($sl, 'merchant', $id);
    }

}

?>
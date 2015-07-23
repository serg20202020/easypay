<?php
namespace Merchant\Controller;

use Application\Controller\AclController;


/**
 * BaseController
 *
 * @author
 *
 * @version
 *
 */
class BaseController extends AclController
{
    public $AclResourceName = __CLASS__;
}
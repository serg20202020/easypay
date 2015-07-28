<?php
namespace Merchant\Controller;

use Application\Controller\AclController;
use Zend\View\Model\ViewModel;


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
    
    function __construct() {
        
        //@session_start();
        //$_SESSION['testMerchant'] = '1';
    }

    protected function setChildViews(ViewModel $view_page) {
    
        $view_menu = new ViewModel();
        $view_menu->setTemplate('merchant/common/menu');
        $view_page->addChild($view_menu,'menu');
    
        return $view_page;
    }

}
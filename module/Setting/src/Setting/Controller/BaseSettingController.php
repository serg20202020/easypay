<?php
namespace Setting\Controller;

use Zend\View\Model\ViewModel;
use Application\Controller\AclController;

/**
 * BaseSettingController
 *
 * @author
 *
 * @version
 *
 */
class BaseSettingController extends AclController
{
    public $AclResourceName = __CLASS__;
    
    protected function setChildViews(ViewModel $view_page) {
    
        $view_menu = new ViewModel();
        $view_menu->setTemplate('setting/common/menu');
        $view_page->addChild($view_menu,'menu');
    
        return $view_page;
    }
}
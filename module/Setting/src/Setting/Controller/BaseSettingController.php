<?php
namespace Setting\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * BaseSettingController
 *
 * @author
 *
 * @version
 *
 */
class BaseSettingController extends AbstractActionController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e){
        $Acl = $this->getServiceLocator()->get('Acl');
        $Acl( __CLASS__);
        return parent::onDispatch($e);
    }
    
    protected function setChildViews(ViewModel $view_page) {
    
        $view_menu = new ViewModel();
        $view_menu->setTemplate('setting/common/menu');
        $view_page->addChild($view_menu,'menu');
    
        return $view_page;
    }
}
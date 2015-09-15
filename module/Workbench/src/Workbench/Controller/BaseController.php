<?php
namespace Workbench\Controller;

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
    
    protected function setChildViews(ViewModel $view_page) {
    
        $view_menu = new ViewModel(array(
            'RouteName'=>$this->getEvent()->getRouteMatch()->getMatchedRouteName()
        ));
        $view_menu->setTemplate('workbench/common/menu');
        $view_page->addChild($view_menu,'menu');
    
        return $view_page;
    }
}
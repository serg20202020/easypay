<?php
namespace Setting\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author
 *
 * @version
 *
 */
class IndexController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {

        $view_page = new ViewModel();
        $view_menu = new ViewModel();
        $view_menu->setTemplate('setting/common/menu');
        $view_page->addChild($view_menu,'menu');
        
        return $view_page;
        
    }
}
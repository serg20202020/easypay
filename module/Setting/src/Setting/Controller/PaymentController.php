<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Setting for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Setting\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PaymentController extends AbstractActionController
{
    public function indexAction()
    {
        $view_page = new ViewModel();
        
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }

    public function alipayAction()
    {
        $view_page = new ViewModel();
        
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
    
    public function wxpayAction()
    {
        $view_page = new ViewModel();
        
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
    
    private function setChildViews(ViewModel $view_page) {
        
        $view_menu = new ViewModel();
        $view_menu->setTemplate('setting/common/menu');
        $view_page->addChild($view_menu,'menu');
        
        $view_paymentmenu = new ViewModel();
        $view_paymentmenu->setTemplate('setting/common/payment-menu');
        $view_page->addChild($view_paymentmenu,'paymentMenu');
        
        return $view_page;
    }
}

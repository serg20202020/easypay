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
use Setting\Form\PaymentSettingForm;
use Setting\Model\PaymentInterface;

class PaymentController extends AbstractActionController
{
    public function indexAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('Payment interface setting'));
        
        $view_page = new ViewModel();
        
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }

    public function alipayAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('Alipay interface setting'));
        
        
        $form = new PaymentSettingForm(PaymentInterface::PAYMENT_TYPE_ALIPAY);
        
        $payment_interface = new PaymentInterface(PaymentInterface::PAYMENT_TYPE_ALIPAY,$this->getServiceLocator());
        $form->bind($payment_interface);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
            
            // Validate the form
            if ($form->isValid()) {
                $payment_interface->save();
            }
        }
        
        
        $view_page = new ViewModel(array('form'=>$form));
        
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
    
    public function wxpayAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('Weixin-pay interface setting'));
        
        
        $form = new PaymentSettingForm(PaymentInterface::PAYMENT_TYPE_WXPAY);
        
        $payment_interface = new PaymentInterface(PaymentInterface::PAYMENT_TYPE_WXPAY,$this->getServiceLocator());
        $form->bind($payment_interface);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
            
            // Validate the form
            if ($form->isValid()) {
                $payment_interface->save();
            }
        }
        
        
        $view_page = new ViewModel(array('form'=>$form));
        
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

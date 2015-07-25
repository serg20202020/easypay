<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Setting for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Setting\Controller;

use Zend\View\Model\ViewModel;
use Setting\Form\PaymentSettingForm;
use Setting\Model\PaymentInterface;

class PaymentController extends BaseSettingController
{
    public function indexAction()
    {
        $this->appendTitle($this->translate('Payment interface setting'));
        
        $view_page = new ViewModel();
        
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }

    public function alipayAction()
    {
        $this->appendTitle($this->translate('Alipay interface setting'));
        
        
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
        $this->appendTitle($this->translate('Weixin-pay interface setting'));
        
        
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
    
    protected function setChildViews(ViewModel $view_page) {
        
        parent::setChildViews($view_page);
        
        $view_paymentmenu = new ViewModel();
        $view_paymentmenu->setTemplate('setting/common/payment-menu');
        $view_page->addChild($view_paymentmenu,'paymentMenu');
        
        return $view_page;
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\LoginForm;

class SessionController extends AbstractActionController
{
    public function indexAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('System Login'));
        
        
        $form = new LoginForm();
        
        /*
        $payment_interface = new PaymentInterface(PaymentInterface::PAYMENT_TYPE_ALIPAY,$this->getServiceLocator());
        $form->bind($payment_interface);*/
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
        
            // Validate the form
            if ($form->isValid()) {
                //$payment_interface->save();
            }
        }
        
        
        $view_page = new ViewModel(array('form'=>$form));
        
        return $view_page;
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /session/session/foo
        return array();
    }
}

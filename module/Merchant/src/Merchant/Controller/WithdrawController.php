<?php
namespace Merchant\Controller;

use Zend\View\Model\ViewModel;
use Merchant\Form;

/**
 * WithdrawController
 *
 * @author
 *
 * @version
 *
 */
class WithdrawController extends BaseController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('Withdraw'));
        
        $form = new Form\WithdrawForm();
        $vars = array('form'=>$form);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
        
            // Validate the form
            if ($form->isValid()) {
                
            }
        }
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
}
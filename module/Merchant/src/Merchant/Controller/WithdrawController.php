<?php
namespace Merchant\Controller;

use Zend\View\Model\ViewModel;
use Merchant\Form;
use Merchant\Model\Withdraw;

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
        $this->appendTitle($this->translate('Withdraw'));
        
        $form = new Form\WithdrawForm();
        $vars = array('form'=>$form);
        
        $withdraw = new Withdraw($this->getServiceLocator());
        $form->bind($withdraw);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
        
            // Validate the form
            if ($form->isValid()) {
                
                $GetClientMerchantID = $this->getServiceLocator()->get('GetClientMerchantID');
                $MerchantID = $GetClientMerchantID();
                
                if (empty($MerchantID)) throw new \Exception('MerchantID is empty !');
                
                $withdraw->merchant_id = $MerchantID;
                $withdraw->pay_status = 0;
                
                $make_time = $this->getServiceLocator()->get('MysqlDatetimeMaker');
                $now_time = $make_time();
                $withdraw->create_time = $now_time;
                
                $withdraw->save();
            }
        }
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
}
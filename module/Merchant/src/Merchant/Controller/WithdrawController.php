<?php
namespace Merchant\Controller;

use Zend\View\Model\ViewModel;
use Merchant\Form;
use Merchant\Model\Withdraw;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;

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
    public function indexAction(){
        $this->appendTitle($this->translate('Withdraw query'));
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $select = new \Zend\Db\Sql\Select();
        $select->from('withdraw')->where(array(
            'merchant_id'=>$this->getMerchantId()
        ));
        
        
        $dbselect = new DbSelect($select,$dbAdapter);
        $paginator = new Paginator($dbselect);
        
        $paginator->setCurrentPageNumber( $this->params()->fromRoute('page') );
        
        $vars = array('paginator'=>$paginator);
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
    
    public function addAction()
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
                
                $withdraw->merchant_id = $this->getMerchantId();
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
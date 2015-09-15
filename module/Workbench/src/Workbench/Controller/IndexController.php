<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Workbench for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Workbench\Controller;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;
use Workbench\Form;
use Merchant\Model\Withdraw;
use Merchant\Model\Setting;


class IndexController extends BaseController
{
    public function indexAction()
    {
        $view_page = new ViewModel();
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
    
    public function withdrawAction()
    {
        $this->appendTitle($this->translate('Withdraw list'));
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $select = new \Zend\Db\Sql\Select();
        $select->from('withdraw')->order(array(
            'create_time'=>'desc',
        ));
        
        
        $dbselect = new DbSelect($select,$dbAdapter);
        $paginator = new Paginator($dbselect);
        
        $paginator->setCurrentPageNumber( $this->params()->fromRoute('page') );
        
        $vars = array(
            'paginator'=>$paginator,
            'sl'=>$this->serviceLocator,
        );
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }

    public function withdraweditAction()
    {
        $this->appendTitle($this->translate('Withdraw Edit'));
        
        
        $form = new Form\WithdrawEditForm();
        $vars = array('form'=>$form);
        
        $withdraw = new Withdraw($this->getServiceLocator(),$this->params()->fromRoute('withdraw_id'));
        $form->bind($withdraw);
        
        $vars['withdraw'] = $withdraw;
        $vars['merchant'] = new Setting($this->getServiceLocator(),$withdraw->merchant_id);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
        
            // Validate the form
            if ($form->isValid()) {
        
                $make_time = $this->getServiceLocator()->get('MysqlDatetimeMaker');
                $now_time = $make_time();
                $withdraw->pay_time = $now_time;
        
                $withdraw->save();
                
                $vars['saved'] = true;
            }
        }
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
}
